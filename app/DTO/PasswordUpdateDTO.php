<?php


declare(strict_types=1);

namespace App\DTO;

class PasswordUpdateDTO
{
    public string $password;
    public function __construct(string $password)
    {
        $this->password = $password;
    }
}
