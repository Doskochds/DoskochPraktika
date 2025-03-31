<?php

namespace App\Http\Controllers;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiOneTimeLinkController extends Controller
{
    protected $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    public function generate($fileId, Request $request): JsonResponse
    {
        try {
            $count = $request->input('count', 1);
            $links = $this->fileService->generateOneTimeLinks($fileId, $count);
            return response()->json(['urls' => $links], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function view($token)
    {
        try {
            $filePath = $this->fileService->getFileByToken($token);
            return response()->file($filePath);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid or expired token'], 404);
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
