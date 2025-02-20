<?php

namespace App\Services;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class FileDeletionService
{
    public function deleteExpiredFiles(): void
    {
        $expiredFiles = File::whereNotNull('delete_at')->where('delete_at', '<=', Carbon::now())->get();

        foreach ($expiredFiles as $file) {
            Storage::disk('public')->delete($file->file_name);
            $file->delete();
        }
    }
}
