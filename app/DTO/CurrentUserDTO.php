<?php

declare(strict_types=1);

namespace App\DTO;

class CurrentUserDTO
{
    public string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }
}
