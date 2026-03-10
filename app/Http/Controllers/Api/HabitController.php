<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Habit;

class HabitController extends Controller
{
    public function index(Request $r){
        return response()->json(['success'=>true,'data'=>$r->user()->habits,'message'=>'All habits']);
    }

    public function store(Request $r){
        $r->validate([
            'title'=>'required|max:100',
            'frequency'=>'required|in:daily,weekly,monthly',
            'target_days'=>'required|integer|min:1'
        ]);
        $h = Habit::create($r->only('title','description','frequency','target_days')+['user_id'=>$r->user()->id,'is_active'=>true]);
        return response()->json(['success'=>true,'data'=>$h,'message'=>'Habit created'],201);
    }

    public function show(Habit $habit){
        return response()->json(['success'=>true,'data'=>$habit,'message'=>'Habit detail']);
    }

    public function update(Request $r,Habit $habit){
        $r->validate([
            'title'=>'sometimes|required|max:100',
            'frequency'=>'sometimes|required|in:daily,weekly,monthly',
            'target_days'=>'sometimes|required|integer|min:1'
        ]);
        $habit->update($r->only('title','description','frequency','target_days','is_active'));
        return response()->json(['success'=>true,'data'=>$habit,'message'=>'Habit updated']);
    }

    public function destroy(Habit $habit){
        $habit->delete();
        return response()->json(['success'=>true,'data'=>null,'message'=>'Habit deleted']);
    }
}
