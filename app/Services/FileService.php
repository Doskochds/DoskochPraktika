<?php

declare(strict_types=1);
namespace App\Services;

use App\DTO\FileDTO;
use App\DTO\StatisticsDTO;
use App\DTO\OneTimeLinkDTO;
use App\Models\File;
use App\Models\OneTimeLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class FileService
{
    /**
     * Завантажити файл і зберегти його в базі
     *
     * @param FileDTO $fileDTO
     * @return File
     */
    public function uploadFile(FileDTO $fileDTO): File
    {
        $filePath = $fileDTO->file;
        return File::create([
            'user_id' => Auth::id(),
            'file_name' => $filePath,
            'comment' => $fileDTO->comment,
            'delete_at' => $fileDTO->deleteAt,
        ]);
    }

    /**
     * Отримати всі файли користувача
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserFiles(): \Illuminate\Database\Eloquent\Collection
    {
        return File::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Отримати файл по ID
     *
     * @param int $id
     * @return File
     */
    public function getFile(int $id): File
    {
        return File::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Видалити файл по ID
     *
     * @param int $id
     * @return void
     */
    public function deleteFile(int $id): void
    {
        $file = File::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        Storage::disk('public')->delete($file->file_name);
        $file->oneTimeLinks()->delete();
        $file->delete();
    }

    /**
     * Отримати файл для перегляду по ID
     *
     * @param int $id
     * @return string
     */
    public function getFileForView(int $id): string
    {
        $file = File::findOrFail($id);
        $file->increment('views');
        return storage_path("app/public/{$file->file_name}");
    }

    /**
     * Видалити прострочені файли
     *
     * @return void
     */
    public function deleteExpiredFiles(): void
    {
        $expiredFiles = File::whereNotNull('delete_at')
            ->where('delete_at', '<=', Carbon::now())
            ->get();
        foreach ($expiredFiles as $file) {
            Storage::disk('public')->delete($file->file_name);
            $file->oneTimeLinks()->delete();
            $file->delete();
        }
    }

    /**
     * Отримати статистику по файлах і посиланнях
     *
     * @return StatisticsDTO
     */
    public function getStatistics(): StatisticsDTO
    {
        $totalLinks = OneTimeLink::withTrashed()->count();
        $unusedLinks = OneTimeLink::whereNull('used_at')->count();
        $userLinks = OneTimeLink::whereHas(
            'file', function ($query) {
            $query->where('user_id', Auth::id());
        }
        )->withTrashed()->count();
        $userUnusedLinks = OneTimeLink::whereHas(
            'file', function ($query) {
            $query->where('user_id', Auth::id());
        }
        )->whereNull('used_at')->count();
        $userUsedLinks = $userLinks - $userUnusedLinks;
        $totalViews = (int) File::sum('views');
        $userTotalViews = (int) File::where('user_id', Auth::id())->sum('views');
        return new StatisticsDTO(
            File::count(),
            File::onlyTrashed()->count(),
            $totalLinks,
            $totalLinks - $unusedLinks,
            $unusedLinks,
            $totalViews,
            File::withTrashed()->withCount('oneTimeLinks')->get()->toArray(),
            File::where('user_id', Auth::id())->count(),
            File::where('user_id', Auth::id())->onlyTrashed()->count(),
            $userLinks,
            $userUsedLinks,
            $userUnusedLinks,
            $userTotalViews
        );
    }


    /**
     * Генерація одноразових посилань
     *
     * @param int $fileId
     * @param int $count
     * @return OneTimeLinkDTO[]
     */
    public function generateOneTimeLinks(int $fileId, int $count = 1): array
    {
        if ($count > 50) {
            throw new Exception('Не більше 50 за раз');
        }
        $links = [];
        for ($i = 0; $i < $count; $i++) {
            $token = Str::random(32);
            $link = OneTimeLink::create(
                [
                    'file_id' => $fileId,
                    'token' => $token,
                    'created_at' => Carbon::now(),
                ]
            );

            $links[] = new OneTimeLinkDTO(
                $token,
                route('file.views.one', ['token' => $token]),
                $link->created_at->toDateTimeString()
            );
        }
        return $links;
    }

    /**
     * Отримати файл за одноразовим посиланням
     *
     * @param string $token
     * @return string
     */
    public function getFileByToken(string $token): string
    {
        $link = OneTimeLink::where('token', $token)->firstOrFail();
        if ($link->used_at || $link->deleted_at) {
            throw new Exception('Посилання недоступне або вже використано.');
        }
        $file = File::findOrFail($link->file_id);
        $file->increment('views');
        $link->update(['used_at' => now()]);
        return storage_path("app/public/{$file->file_name}");
    }

    /**
     * Видалити одноразовий лінк
     *
     * @param string $token
     * @return void
     */
    public function deleteOneTimeLink(string $token): void
    {
        $link = OneTimeLink::where('token', $token)->firstOrFail();
        $link->delete();
    }
}
