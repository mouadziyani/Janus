<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HabitIndexRequest;
use App\Http\Requests\Api\HabitStoreRequest;
use App\Http\Requests\Api\HabitShowRequest;
use App\Http\Requests\Api\HabitUpdateRequest;
use App\Http\Requests\Api\HabitDestroyRequest;
use App\Models\Habit;

class HabitController extends Controller
{
    public function index(HabitIndexRequest $r){
        return $this->success($r->user()->habits,'Opération réussie');
    }

    public function store(HabitStoreRequest $r){
        $data = $r->only('title','description','frequency','target_days','color','is_active');
        if (!array_key_exists('is_active', $data) || $data['is_active'] === null) {
            $data['is_active'] = true;
        }
        $h = Habit::create($data+['user_id'=>$r->user()->id]);
        return $this->success($h,'Opération réussie',201);
    }

    public function show(HabitShowRequest $r, Habit $habit){
        if ($habit->user_id !== $r->user()->id) {
            return $this->error(null,'Unauthorized',403);
        }
        return $this->success($habit,'Opération réussie');
    }

    public function update(HabitUpdateRequest $r, Habit $habit){
        if ($habit->user_id !== $r->user()->id) {
            return $this->error(null,'Unauthorized',403);
        }
        $habit->update($r->only('title','description','frequency','target_days','color','is_active'));
        return $this->success($habit,'Opération réussie');
    }

    public function destroy(HabitDestroyRequest $r, Habit $habit){
        if ($habit->user_id !== $r->user()->id) {
            return $this->error(null,'Unauthorized',403);
        }
        $habit->delete();
        return $this->success(null,'Opération réussie');
    }
}
