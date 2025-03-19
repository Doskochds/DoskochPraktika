<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function create()
    {
        return view('files.create');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
            'file' => 'required|file|mimes:jpg,png,jpeg,gif,bmp,tiff,ai,cdr,svg,wmf,emf|max:5120',
            'comment' => 'nullable|string|max:255',
            'delete_at' => 'nullable|date|after:today',
            ]
        );

        $this->fileService->uploadFile($request);

        return redirect()->route('files.index');
    }

    public function index()
    {
        $files = $this->fileService->getUserFiles();

        return view('files.index', compact('files'));
    }

    public function show($id)
    {
        $file = $this->fileService->getFile($id);

        return view('files.show', compact('file'));
    }

    public function destroy($id)
    {
        $this->fileService->deleteFile($id);

        return redirect()->route('files.index')->with('success', 'Файл успішно видалено');
    }

    public function view($id)
    {
        $file = $this->fileService->getFileForView($id);

        return response()->file($file);
    }
    public function statistics()
    {
        $report = $this->fileService->getStatistics();

        return view('files.statistics', compact('report'));
    }
}
