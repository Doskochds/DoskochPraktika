<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\OneTimeLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $totalFiles = File::count();
        $deletedFiles = File::onlyTrashed()->count();
        $totalLinks = OneTimeLink::count();
        $usedLinks = OneTimeLink::whereNotIn('id', OneTimeLink::pluck('id'))->count();
        $totalViews = DB::table('file_views')->sum('views');

        return view('reports.index', compact('totalFiles', 'deletedFiles', 'totalLinks', 'usedLinks', 'totalViews'));
    }
}
