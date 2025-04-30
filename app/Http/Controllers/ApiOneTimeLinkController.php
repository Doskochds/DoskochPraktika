<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiOneTimeLinkController extends Controller
{
    protected FileService $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    /**
     * Генерація одноразових посилань для файлу.
     *
     * @param string|int $fileId
     * @param Request $request
     * @return JsonResponse
     */
    public function generate(string|int $fileId, Request $request): JsonResponse
    {
        try {
            $count = (int) $request->input('count', 1);
            $fileId = (int) $fileId; // Приводимо до int
            $links = $this->fileService->generateOneTimeLinks($fileId, $count);
            return response()->json(['urls' => $links], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    /**
     * Перегляд файлу через одноразове посилання.
     *
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function view(string $token)
    {
        try {
            $filePath = $this->fileService->getFileByToken($token);
            return response()->file($filePath);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid or expired token'], 404);
        }
    }
    /**
     * Видалення одноразового посилання.
     *
     * @param string $token
     * @return JsonResponse
     */
    public function deleteLink(string $token): JsonResponse
    {
        try {
            $this->fileService->deleteOneTimeLink($token);
            return response()->json(['message' => 'Link deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    /**
     * Отримати всі одноразові посилання для файлу.
     *
     * @param string|int $fileId
     * @return JsonResponse
     */
    public function getLinksByFileId(string|int $fileId): JsonResponse
    {
        try {
            $fileId = (int) $fileId;
            $links = $this->fileService->getOneTimeLinksByFileId($fileId);
            return response()->json(['links' => $links]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No links found for this file or invalid file ID'], 404);
        }
    }
}
