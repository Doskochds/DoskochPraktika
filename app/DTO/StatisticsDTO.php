<?php

declare(strict_types=1);

namespace App\DTO;

class StatisticsDTO
{
    public int $totalFiles;
    public int $deletedFiles;
    public int $totalLinks;
    public int $usedLinks;
    public int $unusedLinks;
    public int $totalViews;
    public array $files;
    public int $userFiles;
    public int $userDeletedFiles;
    public int $userLinks;
    public int $userUsedLinks;
    public int $userUnusedLinks;
    public int $userTotalViews;

    public function __construct(
        int $totalFiles,
        int $deletedFiles,
        int $totalLinks,
        int $usedLinks,
        int $unusedLinks,
        int $totalViews,
        array $files,
        int $userFiles,
        int $userDeletedFiles,
        int $userLinks,
        int $userUsedLinks,
        int $userUnusedLinks,
        int $userTotalViews
    ) {
        $this->totalFiles = $totalFiles;
        $this->deletedFiles = $deletedFiles;
        $this->totalLinks = $totalLinks;
        $this->usedLinks = $usedLinks;
        $this->unusedLinks = $unusedLinks;
        $this->totalViews = $totalViews;
        $this->files = $files;
        $this->userFiles = $userFiles;
        $this->userDeletedFiles = $userDeletedFiles;
        $this->userLinks = $userLinks;
        $this->userUsedLinks = $userUsedLinks;
        $this->userUnusedLinks = $userUnusedLinks;
        $this->userTotalViews = $userTotalViews;
    }
}
