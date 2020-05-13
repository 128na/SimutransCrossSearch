<?php

namespace App\Http\Controllers;

use App\Services\ScheduleLogService;

class ScheduleLogController extends Controller
{
    private ScheduleLogService $service;

    public function __construct(ScheduleLogService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $logs = $this->service->getLatest();
        return view('logs.schdule', compact('logs'));
    }
}
