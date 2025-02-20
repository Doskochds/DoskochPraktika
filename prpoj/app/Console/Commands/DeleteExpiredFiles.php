<?php
namespace App\Console\Commands;

use App\Services\FileDeletionService;
use Illuminate\Console\Command;

class DeleteExpiredFiles extends Command
 {
   protected $signature = 'files:delete-expired';
   protected $description = 'Видаляє файли з вказаною датою видалення';

   public function handle(FileDeletionService $service): void
   {
      $service->deleteExpiredFiles();
      $this->info('Видалило застарілі файли успішно.');
   }
 }
