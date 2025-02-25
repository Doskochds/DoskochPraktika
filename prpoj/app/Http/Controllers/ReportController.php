<?php

namespace App\Http\Controllers;
use App\Models\File;
use App\Models\OneTimeLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index()
    {

        $totalFiles = File::count();
        $deletedFiles = File::onlyTrashed()->count();
        $totalLinks = OneTimeLink::count();
        $totalViews = File::sum('views');


        $usedLinks = OneTimeLink::whereNotNull('used_at')->count();

        $files = File::withTrashed()->withCount('oneTimeLinks')->get();


        return view('reports.index', compact('totalFiles', 'deletedFiles', 'totalLinks', 'usedLinks', 'totalViews', 'files'));
    }
}
