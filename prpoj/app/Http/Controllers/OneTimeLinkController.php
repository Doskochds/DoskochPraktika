<?php
namespace App\Http\Controllers;

use App\Models\File;
use App\Models\OneTimeLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class OneTimeLinkController extends Controller
{
public function generate($fileId)
{
$token = Str::random(32);
$link = OneTimeLink::create(['file_id' => $fileId, 'token' => $token]);
return response()->json(['url' => route('file.view.one', ['token' => $token])]);
}

public function view($token)
{
$link = OneTimeLink::where('token', $token)->first();
if (!$link) {
abort(404);
}

$file = File::findOrFail($link->file_id);
$link->delete(); // Видаляємо після першого перегляду

return response()->file(Storage::disk('public')->path($file->file_name));
}
}
