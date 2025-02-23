<?php
namespace App\Http\Controllers;

use App\Models\File;
use App\Models\OneTimeLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OneTimeLinkController extends Controller
{

    public function generate($fileId, Request $request)
    {

        $count = $request->input('count', 1);


        if ($count > 50) {
            return response()->json(['error' => 'Не більше 50 за раз'], 400);
        }


        $links = [];
        for ($i = 0; $i < $count; $i++) {
            $token = Str::random(32);
            $link = OneTimeLink::create([
                'file_id' => $fileId,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
            $links[] = [
                'token' => $token,
                'url' => route('file.view.one', ['token' => $token]),
                'created_at' => $link->created_at->toDateTimeString()
            ];
        }


        return response()->json(['urls' => $links]);
    }
    public function show($fileId)
    {
        $file = File::with('oneTimeLinks')->findOrFail($fileId);

        return view('files.show', compact('file'));
    }

    public function view($token)
    {
        $link = OneTimeLink::where('token', $token)->first();

        if (!$link) {
            abort(404);
        }

        $file = File::findOrFail($link->file_id);

        $file->increment('views');


        $link->update(['used_at' => Carbon::now()]);
        $link->delete();


        return response()->file(storage_path("app/public/{$file->file_name}"));
    }


    public function deleteLink($token)
    {
        $link = OneTimeLink::where('token', $token)->first();

        if (!$link) {
            abort(404);
        }


        $link->delete();

        return response()->json(['message' => 'Link deleted successfully']);
    }


    public function cleanUpLinks()
    {
        $expiredLinks = OneTimeLink::where('created_at', '<', Carbon::now()->subMinutes(10))
            ->get();

        foreach ($expiredLinks as $link) {
            $link->delete();
        }

        return response()->json(['message' => 'Expired links cleaned up successfully']);
    }
}
