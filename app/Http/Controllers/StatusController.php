<?php

namespace App\Http\Controllers;

use App\Models\ScheduleLog;

class StatusController extends Controller
{
    public function index()
    {
        $logs = ScheduleLog::orderBy('created_at', 'desc')->limit(50)->get();
        $logs = $logs->sortBy('created_at');
        return view('status', compact('logs'));
    }

}
