<?php


declare(strict_types=1);

namespace App\DTO;

class PasswordResetDTO
{
    public string $email;
    public string $token;
    public string $password;
    public function __construct(string $email, string $token, string $password)
    {
        $this->email = $email;
        $this->token = $token;
        $this->password = $password;
    }
}
