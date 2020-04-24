<?php

namespace App\Http\Controllers;

use App\Services\ScheduleLogService;

class ScheduleLogController extends Controller
{
    /**
     * @var ScheduleLogService
     */
    private $service;

    public function __construct(ScheduleLogService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $logs = $this->service->getLatest(50);
        $logs = $logs->sortBy('created_at');
        return view('logs.schdule', compact('logs'));
    }

}
