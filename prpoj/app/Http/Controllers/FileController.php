<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function create()
    {
        return view('files.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,png,pdf,docx|max:5120',
            'comment' => 'nullable|string|max:255',
            'delete_at' => 'nullable|date|after:today',
        ]);

        // завантаження зі шляху користувача
        $filePath = $request->file('file')->store('files', 'public');


        File::create([
            'user_id' => auth()->id(),
            'file_name' => $filePath,
            'comment' => $request->input('comment'),
            'delete_at' => $request->input('delete_at'),
        ]);

        return redirect()->route('files.index');
    }
}
