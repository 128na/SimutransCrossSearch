<?php
namespace App\Services;

use App\Models\ScheduleLog;
use Illuminate\Support\Collection;

class ScheduleLogService
{
    /**
     * @var ScheduleLog
     */
    private $model;

    public function __construct(ScheduleLog $model)
    {
        $this->model = $model;
    }

    public function getLatest($limit = 50): Collection
    {
        return $this->model
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
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
