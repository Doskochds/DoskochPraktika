<?php
namespace App\DTO;

class FileDTO
{
public string $file; // Шлях до файлу
public ?string $comment; // Коментар
public ?string $deleteAt;

public function __construct(string $file, ?string $comment, ?string $deleteAt)
{
$this->file = $file;
$this->comment = $comment;
$this->deleteAt = $deleteAt;
}
}

