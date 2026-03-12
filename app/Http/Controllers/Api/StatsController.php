<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HabitStatsRequest;
use App\Http\Requests\Api\OverviewRequest;
use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function habitStats(HabitStatsRequest $r, Habit $habit){
        if ($habit->user_id !== $r->user()->id) {
            return $this->error(null,'Unauthorized',403);
        }
        $logs = $habit->logs()->orderBy('log_date','desc')->pluck('log_date')->toArray();
        $currentStreak = $this->currentStreak($logs);
        $longestStreak = $this->longestStreak($logs);
        $total = count($logs);
        $count30 = $habit->logs()->where('log_date','>=',Carbon::today()->subDays(29))->count();
        $completionRate = round(($count30 / 30) * 100, 2);
        return $this->success([
            'current_streak'=>$currentStreak,
            'longest_streak'=>$longestStreak,
            'total_completions'=>$total,
            'completion_rate'=>$completionRate
        ],'Opération réussie');
    }

    public function overview(OverviewRequest $r){
        $user = $r->user();
        $habits = Habit::where('user_id',$user->id)->get();
        $activeHabits = $habits->where('is_active', true);
        $activeCount = $activeHabits->count();
        $habitIds = $habits->pluck('id')->toArray();
        $today = Carbon::today()->format('Y-m-d');

        $completedToday = 0;
        if (!empty($habitIds)) {
            $completedToday = HabitLog::whereIn('habit_id',$habitIds)
                ->where('log_date',$today)
                ->distinct('habit_id')
                ->count('habit_id');
        }

        $longest = null;
        $longestValue = 0;
        foreach ($habits as $habit) {
            $logs = $habit->logs()->orderBy('log_date','desc')->pluck('log_date')->toArray();
            $value = $this->longestStreak($logs);
            if ($value > $longestValue) {
                $longestValue = $value;
                $longest = [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'longest_streak' => $value
                ];
            }
        }

        $rate7 = 0.0;
        if ($activeCount > 0) {
            $count7 = HabitLog::whereIn('habit_id',$activeHabits->pluck('id')->toArray())
                ->where('log_date','>=',Carbon::today()->subDays(6))
                ->count();
            $rate7 = round(($count7 / ($activeCount * 7)) * 100, 2);
        }

        return $this->success([
            'total_active_habits'=>$activeCount,
            'habits_completed_today'=>$completedToday,
            'habit_with_longest_streak'=>$longest,
            'completion_rate_last_7_days'=>$rate7
        ],'Opération réussie');
    }

    private function currentStreak(array $logs): int
    {
        $streak = 0;
        $expected = Carbon::today();
        foreach ($logs as $date) {
            $d = Carbon::parse($date);
            if ($d->eq($expected)) {
                $streak++;
                $expected = $expected->copy()->subDay();
            } else {
                break;
            }
        }
        return $streak;
    }

    private function longestStreak(array $logs): int
    {
        if (empty($logs)) {
            return 0;
        }
        $dates = array_reverse($logs);
        $longest = 1;
        $current = 1;
        for ($i = 1; $i < count($dates); $i++) {
            $prev = Carbon::parse($dates[$i - 1]);
            $curr = Carbon::parse($dates[$i]);
            if ($prev->copy()->addDay()->eq($curr)) {
                $current++;
                if ($current > $longest) {
                    $longest = $current;
                }
            } else {
                $current = 1;
            }
        }
        return $longest;
    }
}
