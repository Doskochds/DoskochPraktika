<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Services\FileService;
use App\DTO\FileDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFileController extends Controller
{
    protected FileService $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpg,png,jpeg,gif,bmp,tiff,ai,cdr,svg,wmf,emf|max:5120',
            'comment' => 'nullable|string|max:255',
            'delete_at' => 'nullable|date|after:today',
        ]);
        $fileDTO = new FileDTO(
            $validated['file'],
            $validated['comment'] ?? null,
            $validated['delete_at'] ?? null
        );
        $file = $this->fileService->uploadFile($fileDTO);
        return response()->json(['message' => 'Файл успішно завантажено', 'file' => $file], 201);
    }
    public function index(): JsonResponse
    {
        $files = $this->fileService->getUserFiles();
        return response()->json(['files' => $files]);
    }
    public function show(int $id): JsonResponse
    {
        $file = $this->fileService->getFile($id);
        return response()->json(['file' => $file]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->fileService->deleteFile($id);
        return response()->json(['message' => 'Файл успішно видалено']);
    }

    public function view(int $id)
    {
        $file = $this->fileService->getFileForView($id);
        return response()->file($file);
    }

    public function statistics(): JsonResponse
    {
        $report = $this->fileService->getStatistics();
        return response()->json(['statistics' => $report]);
    }
}
