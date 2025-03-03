<?php

namespace App\Services;

use App\Models\File;
use App\Models\OneTimeLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileService
{
    public function uploadFile($request)
    {
        $filePath = $request->file('file')->store('files', 'public');

        return File::create([
            'user_id' => Auth::id(),
            'file_name' => $filePath,
            'comment' => $request->input('comment'),
            'delete_at' => $request->input('delete_at'),
        ]);
    }

    public function getUserFiles()
    {
        return File::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getFile($id)
    {
        return File::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    }

    public function deleteFile($id)
    {
        $file = File::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        Storage::disk('public')->delete($file->file_name);

        $file->oneTimeLinks()->delete();

        $file->delete();
    }

    public function getFileForView($id)
    {
        $file = File::findOrFail($id);
        $file->increment('views');
        return storage_path("app/public/{$file->file_name}");
    }

    public function deleteExpiredFiles()
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

    public function getStatistics()
    {
        $totalLinks = OneTimeLink::withTrashed()->count();
        $unusedLinks = OneTimeLink::whereNull('used_at')->count();

        $userLinks = OneTimeLink::whereHas('file', function ($query) {
            $query->where('user_id', Auth::id());
        })->withTrashed()->count();

        $userUnusedLinks = OneTimeLink::whereHas('file', function ($query) {
            $query->where('user_id', Auth::id());
        })->whereNull('used_at')->count();

        $userUsedLinks = $userLinks - $userUnusedLinks;

        return [
            'totalFiles' => File::count(),
            'deletedFiles' => File::onlyTrashed()->count(),
            'totalLinks' => $totalLinks,
            'usedLinks' => $totalLinks - $unusedLinks,
            'unusedLinks' => $unusedLinks,
            'totalViews' => File::sum('views'),
            'files' => File::withTrashed()->withCount('oneTimeLinks')->get(),
            'userFiles' => File::where('user_id', Auth::id())->count(),
            'userDeletedFiles' => File::where('user_id', Auth::id())->onlyTrashed()->count(),
            'userLinks' => $userLinks,
            'userUsedLinks' => $userUsedLinks,
            'userUnusedLinks' => $userUnusedLinks,
            'userTotalViews' => File::where('user_id', Auth::id())->sum('views'),
        ];
    }

    public function generateOneTimeLinks($fileId, $count = 1)
    {
        if ($count > 50) {
            throw new \Exception('Не більше 50 за раз');
        }

        $links = [];
        for ($i = 0; $i < $count; $i++) {
            $token = Str::random(32);
            $link = OneTimeLink::create([
                'file_id' => $fileId,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            $links[] = [
                'token' => $token,
                'url' => route('file.views.one', ['token' => $token]),
                'created_at' => $link->created_at->toDateTimeString(),
            ];
        }

        return $links;
    }

    public function getFileByToken($token)
    {

        $link = OneTimeLink::where('token', $token)->firstOrFail();


        $file = File::findOrFail($link->file_id);
        $file->increment('views');

        $link->update(['used_at' => now()]);


        return storage_path("app/public/{$file->file_name}");
    }

    public function deleteOneTimeLink($token)
    {
        $link = OneTimeLink::where('token', $token)->firstOrFail();
        $link->delete();
    }

    //Якщо одобрять видалення по часу невикористаних
    //public function cleanUpExpiredLinks()
    //{
        //OneTimeLink::where('created_at', '<', Carbon::now()->subMinutes(10))->delete();
    //}
}
