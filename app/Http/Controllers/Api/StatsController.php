<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Habit;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function habitStats(Habit $habit){
        $logs = $habit->logs()->orderBy('log_date','desc')->pluck('log_date')->toArray();
        $streak = 0;
        $today = Carbon::today();
        foreach($logs as $date){
            $d = Carbon::parse($date);
            if($d->eq($today->subDays($streak))) $streak++;
            else break;
        }
        $total = count($logs);
        return response()->json([
            'success'=>true,
            'data'=>[
                'current_streak'=>$streak,
                'total_completions'=>$total
            ],
            'message'=>'Habit stats'
        ]);
    }

    public function overview($user_id){
        $habits = Habit::where('user_id',$user_id)->get();
        $total = $habits->count();
        return response()->json(['success'=>true,'data'=>['total_habits'=>$total],'message'=>'Overview']);
    }
}
