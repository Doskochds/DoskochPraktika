<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function create()
    {
        return view('files.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,png,jpeg,gif,bmp,tiff,ai,cdr,svg,wmf,emf|max:5120',
            'comment' => 'nullable|string|max:255',
            'delete_at' => 'nullable|date|after:today',
        ]);

        // завантаження  файлу зі шляху користувача
        $filePath = $request->file('file')->store('files', 'public');


        File::create([
            'user_id' => auth()->id(),
            'file_name' => $filePath,
            'comment' => $request->input('comment'),
            'delete_at' => $request->input('delete_at'),
        ]);

        return redirect()->route('files.index');
    }
    public function index()
    {
        $files = File::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();


        return view('files.index', compact('files'));
    }
    public function show($id)
    {
        $file = File::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        return view('files.show', compact('file'));
    }

    public function destroy($id)
    {
        $file = File::where('id', $id)->where('user_id', auth()->id())->firstOrFail();


        Storage::disk('public')->delete($file->file_name);


        $file->delete();
        $file->oneTimeLinks()->delete();

        return redirect()->route('files.index')->with('success', 'Файл успішно видалено');
    }
    public function view($id)
    {
        $file = File::findOrFail($id);


        return response()->file(storage_path("app/public/{$file->file_name}"));
    }
}
