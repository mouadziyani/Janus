<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HabitLogIndexRequest;
use App\Http\Requests\Api\HabitLogStoreRequest;
use App\Http\Requests\Api\HabitLogDestroyRequest;
use App\Models\HabitLog;
use App\Models\Habit;

class HabitLogController extends Controller
{
    public function index(HabitLogIndexRequest $r, $id){
        $habit = Habit::findOrFail($id);
        if ($habit->user_id !== $r->user()->id) {
            return $this->error(null,'Unauthorized',403);
        }
        return $this->success($habit->logs,'Opération réussie');
    }

    public function store(HabitLogStoreRequest $r,$id){
        $habit = Habit::findOrFail($id);
        if ($habit->user_id !== $r->user()->id) {
            return $this->error(null,'Unauthorized',403);
        }
        $log = HabitLog::firstOrCreate(
            ['habit_id'=>$habit->id,'log_date'=>now()->format('Y-m-d')],
            ['note'=>$r->note]
        );
        if (!$log->wasRecentlyCreated) {
            return $this->error(['log_date'=>['Already logged today']],'Log exists',409);
        }
        return $this->success($log,'Opération réussie',201);
    }

    public function destroy(HabitLogDestroyRequest $r,$habit_id,$logId){
        $habit = Habit::findOrFail($habit_id);
        if ($habit->user_id !== $r->user()->id) {
            return $this->error(null,'Unauthorized',403);
        }
        $log = HabitLog::where('habit_id',$habit_id)->findOrFail($logId);
        $log->delete();
        return $this->success(null,'Opération réussie');
    }
}
