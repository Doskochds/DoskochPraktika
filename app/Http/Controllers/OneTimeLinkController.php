<?php

namespace App\Http\Controllers;

use App\DTO\OneTimeLinkDTO;
use App\Services\FileService;
use Illuminate\Http\Request;

class OneTimeLinkController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function generate($fileId, Request $request)
    {
        try {
            $count = $request->input('count', 1);
            $links = $this->fileService->generateOneTimeLinks($fileId, $count);

            return response()->json([
                'links' => array_map(function (OneTimeLinkDTO $link) {
                    return [
                        'token' => $link->token,
                        'url' => $link->url,
                        'created_at' => $link->createdAt,
                    ];
                }, $links),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show($fileId)
    {
        try {
            $file = $this->fileService->getFile($fileId);
            return view('files.show', compact('file'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function view($token)
    {
        try {
            $filePath = $this->fileService->getFileByToken($token);

            // Видалення одноразового лінка після перегляду
            $this->fileService->deleteOneTimeLink($token);

            return response()->file($filePath);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteLink($token)
    {
        try {
            $this->fileService->deleteOneTimeLink($token);
            return response()->json(['message' => 'Link deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}

