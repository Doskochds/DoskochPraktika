<?php

namespace App\Services;

use App\Models\File;
use App\Models\OneTimeLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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
        return storage_path("app/public/{$file->file_name}");
    }

    public function deleteExpiredFiles()
    {
        $expiredFiles = File::whereNotNull('delete_at')
            ->where('delete_at', '<=', Carbon::now())
            ->get();

        foreach ($expiredFiles as $file) {
            Storage::disk('public')->delete($file->file_name);
            $file->delete();
        }
    }

    public function getStatistics()
    {

        $totalFiles = File::count();
        $deletedFiles = File::onlyTrashed()->count();
        $totalLinks = OneTimeLink::count();
        $usedLinks = OneTimeLink::whereNotNull('used_at')->count();
        $totalViews = File::sum('views');
        $files = File::withTrashed()->withCount('oneTimeLinks')->get();


        $userFiles = File::where('user_id', Auth::id())->count();
        $userDeletedFiles = File::where('user_id', Auth::id())->onlyTrashed()->count();
        $userLinks = OneTimeLink::whereHas('file', function ($query) {
            $query->where('user_id', Auth::id());
        })->count();
        $userUsedLinks = OneTimeLink::whereHas('file', function ($query) {
            $query->where('user_id', Auth::id());
        })->whereNotNull('used_at')->count();

        return [
            'totalFiles' => $totalFiles,
            'deletedFiles' => $deletedFiles,
            'totalLinks' => $totalLinks,
            'usedLinks' => $usedLinks,
            'totalViews' => $totalViews,
            'files' => $files,
            'userFiles' => $userFiles,
            'userDeletedFiles' => $userDeletedFiles,
            'userLinks' => $userLinks,
            'userUsedLinks' => $userUsedLinks,
        ];
    }
}
