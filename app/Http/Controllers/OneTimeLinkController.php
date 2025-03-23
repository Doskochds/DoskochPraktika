<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use Illuminate\Http\Request;

class OneTimeLinkController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function generate($fileId, Request $request)
    {
        try {
            $count = $request->input('count', 1);
            $links = $this->fileService->generateOneTimeLinks($fileId, $count);
            return response()->json(['urls' => $links]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show($fileId)
    {
        $file = $this->fileService->getFile($fileId);
        return view('files.show', compact('file'));
    }

    public function view($token)
    {
        $filePath = $this->fileService->getFileByToken($token);
        $this->fileService->deleteOneTimeLink($token);
        return response()->file($filePath);



    }

    public function deleteLink($token)
    {
        $this->fileService->deleteOneTimeLink($token);

    }

//    public function cleanUpLinks()
//    {
//        $this->fileService->cleanUpExpiredLinks();
//        return response()->json(['message' => 'Expired links cleaned up successfully']);
//    }
}
