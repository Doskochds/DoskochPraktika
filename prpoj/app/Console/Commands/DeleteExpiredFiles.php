<?php
namespace App\Console\Commands;


use App\Services\FileService;
use Illuminate\Console\Command;

class DeleteExpiredFiles extends Command
 {
   protected $signature = 'files:delete-expired';
   protected $description = 'Видаляє файли з вказаною датою видалення';

    public function handle()
    {
        $fileService = app(FileService::class);
        $fileService->deleteExpiredFiles();
    }
 }
