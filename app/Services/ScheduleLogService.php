<?php
namespace App\Services;

use App\Models\ScheduleLog;
use Illuminate\Pagination\Paginator;

class ScheduleLogService
{
    private ScheduleLog $model;

    public function __construct(ScheduleLog $model)
    {
        $this->model = $model;
    }

    public function getLatest($limit = 20): Paginator
    {
        return $this->model
            ->orderBy('id', 'desc')
            ->simplePaginate($limit);
    }

    public function begin($label): ScheduleLog
    {
        return $this->model->create(['label' => $label]);
    }
    public function end($label, $status = 'success'): ScheduleLog
    {
        return $this->model->create(['label' => $label, 'status' => $status]);
    }
}
