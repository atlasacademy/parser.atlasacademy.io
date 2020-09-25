<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Submission;
use DB;

class AdminController extends Controller
{

    public function index()
    {
        $failedCount = Submission::query()->whereBetween('status', [20, 29])->count();
        $pendingCount = Submission::query()->whereBetween('status', [10, 19])->count();
        $jobCount = DB::table('jobs')->count();

        return response()->view(
            'admin-index',
            compact('failedCount', 'pendingCount', 'jobCount')
        );
    }

}
