<?php

declare(strict_types=1);

namespace App\DTO;

class OneTimeLinkDTO
{
    public string $token;
    public string $url;
    public string $createdAt;

    public function __construct(string $token, string $url, string $createdAt)
    {
        $this->token = $token;
        $this->url = $url;
        $this->createdAt = $createdAt;
    }
}
