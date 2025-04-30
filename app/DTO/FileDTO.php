<?php
namespace App\DTO;
use Illuminate\Http\UploadedFile;

class FileDTO
{
    public UploadedFile $file;
    public ?string $comment;
    public ?string $deleteAt;
    public function __construct(UploadedFile $file, ?string $comment, ?string $deleteAt)
    {
        $this->file = $file;
        $this->comment = $comment;
        $this->deleteAt = $deleteAt;
    }
}


