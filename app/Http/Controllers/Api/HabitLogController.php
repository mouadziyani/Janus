<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HabitLog;
use App\Models\Habit;

class HabitLogController extends Controller
{
    public function index(Habit $habit){
        return response()->json(['success'=>true,'data'=>$habit->logs,'message'=>'Logs list']);
    }

    public function store(Request $r,$id){
        $r->validate(['note'=>'nullable|string']);
        $habit = Habit::findOrFail($id);
        $log = HabitLog::firstOrCreate(['habit_id'=>$habit->id,'log_date'=>now()->format('Y-m-d')],['note'=>$r->note]);
        return response()->json(['success'=>true,'data'=>$log,'message'=>'Log added'],201);
    }

    public function destroy($habit_id,$logId){
        $log = HabitLog::where('habit_id',$habit_id)->findOrFail($logId);
        $log->delete();
        return response()->json(['success'=>true,'data'=>null,'message'=>'Log deleted']);
    }
}
