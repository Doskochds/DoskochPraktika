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

    public function getReports()
    {
        return [
            'totalFiles' => File::count(),
            'deletedFiles' => File::onlyTrashed()->count(),
            'totalLinks' => OneTimeLink::count(),
            'usedLinks' => OneTimeLink::whereNotNull('used_at')->count(),
            'totalViews' => File::sum('views'),
            'files' => File::withTrashed()->withCount('oneTimeLinks')->get(),
        ];
    }
}
