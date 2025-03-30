<?php

namespace App\Http\Controllers;

use App\DTO\FileDTO;
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

            // Створюємо DTO для кількості лінків
            $links = $this->fileService->generateOneTimeLinks($fileId, $count);

            return response()->json(['urls' => $links]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show($fileId)
    {
        // Отримуємо файл через сервіс
        $file = $this->fileService->getFile($fileId);

        return view('files.show', compact('file'));
    }

    public function view($token)
    {
        try {
            // Отримуємо шлях до файлу через токен
            $filePath = $this->fileService->getFileByToken($token);

            // Видаляємо одноразове посилання після використання
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
