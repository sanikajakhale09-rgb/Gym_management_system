<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanItem;
use App\Models\Exercise;
use App\Models\Member;
use App\Models\Trainer;
use App\Models\DietPlan;
use App\Models\FitnessGoal; 
use Carbon\Carbon;

class WorkoutPlanController extends Controller
{
    public function index()
    {
        $members = Member::with('trainer')->get();
        $trainers = Trainer::all();
        $exercises = Exercise::all();
        
        return view('trainers.workouts', compact('members', 'trainers', 'exercises')); 
    }

    public function create()
    {
        $members = Member::all();
        $trainers = Trainer::all();
        $exercises = Exercise::all();
        
        return view('trainers.create', compact('members', 'trainers', 'exercises'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'trainer_id' => 'required|exists:trainers,id',
            'title' => 'required|string|max:255',
            'goal' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'exercises' => 'nullable|array',
            'exercises.*.exercise_id' => 'required_with:exercises|exists:exercises,id',
            'exercises.*.day_of_week' => 'nullable|string|max:20',
            'exercises.*.workout_date' => 'nullable|date',
        ]);

        return DB::transaction(function () use ($request) {
            
            WorkoutPlan::where('member_id', $request->member_id)->delete();

            $workoutPlan = WorkoutPlan::create([
                'gym_id' => Auth::user()->gym_id ?? 1,
                'branch_id' => Auth::user()->branch_id ?? 1,
                'member_id' => $request->member_id,
                'trainer_id' => $request->trainer_id,
                'title' => $request->title,
                'goal' => $request->goal,
                'level' => $request->level,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => $request->notes,
            ]);

            $member = Member::find($request->member_id);
            if ($member) {
                $member->update([
                    'trainer_id' => $request->trainer_id
                ]);
            }

           if ($request->has('exercises')) {
                foreach ($request->exercises as $item) {
                    $workoutDate = $item['workout_date'] ?? null;
                    $dayOfWeek = $item['day_of_week'] ?? 'Monday';

                    if (!empty($workoutDate)) {
                        try {
                            $dayOfWeek = Carbon::parse($workoutDate)->format('l');
                        } catch (\Exception $e) {
                            // Keep default
                        }
                    } elseif (empty($workoutDate) && !empty($dayOfWeek)) {
                        try {
                            $startDate = Carbon::parse($request->start_date);
                            if (strtolower($startDate->format('l')) === strtolower($dayOfWeek)) {
                                $workoutDate = $startDate->toDateString();
                            } else {
                                $workoutDate = $startDate->copy()->next($dayOfWeek)->toDateString();
                            }
                        } catch (\Exception $e) {
                            $workoutDate = $request->start_date;
                        }
                    }

                    WorkoutPlanItem::create([
                        'workout_plan_id' => $workoutPlan->id,
                        'day_of_week' => $dayOfWeek,
                        'workout_date' => $workoutDate,
                        'muscle_group' => $item['muscle_group'] ?? null,
                        'exercise_id' => $item['exercise_id'],
                        'sets' => $item['sets'] ?? null,
                        'reps' => $item['reps'] ?? null,
                        'weight' => $item['weight'] ?? null,
                        'rest_time' => $item['rest_time'] ?? null,
                        'instructions' => $item['instructions'] ?? null,
                    ]);
                }
            }

            return redirect()->route('workouts.index')->with('success', 'Workout plan created successfully and trainer assigned to member!');
        });
    }

    public function getWorkoutsByMember(int $memberId)
    {
        $workoutPlans = WorkoutPlan::with('items.exercise')
                            ->where('member_id', $memberId)
                            ->orderBy('created_at', 'desc')
                            ->get();

        $activeFitnessGoal = FitnessGoal::where('member_id', $memberId)
                                    ->where('status', 'Active')
                                    ->first() ?? FitnessGoal::where('member_id', $memberId)->latest()->first();

        $goalValue = $activeFitnessGoal ? ($activeFitnessGoal->goal ?? $activeFitnessGoal->goal_type) : null;
        $latestWorkout = $workoutPlans->first();

        return response()->json([
            'workout_plans' => $workoutPlans,
            'goal' => $goalValue,
            'latest_goal' => $goalValue,
            'title' => $latestWorkout ? $latestWorkout->title : ($goalValue ? $goalValue . ' Plan' : null),
            'level' => $activeFitnessGoal ? $activeFitnessGoal->level : ($latestWorkout ? $latestWorkout->level : null),
            'start_date' => $activeFitnessGoal ? $activeFitnessGoal->start_date : ($latestWorkout ? $latestWorkout->start_date : null),
            'target_date' => $activeFitnessGoal ? $activeFitnessGoal->target_date : ($latestWorkout ? $latestWorkout->end_date : null)
        ]);
    }

public function getMemberDetails(int $id)
    {
        try {
            $member = Member::find($id);

            if (!$member) {
                return response()->json(['status' => false, 'message' => 'Member not found']);
            }

            // थेट डेटाबेसवरून लेटेस्ट फिटनेस गोल शोधा
            $latestGoal = FitnessGoal::where('member_id', $id)->latest()->first();
            $latestWorkout = WorkoutPlan::where('member_id', $id)->latest()->first();

            $goalValue = null;
            if ($latestGoal) {
                // तुमच्या टेबलमधील सर्व संभाव्य कॉलमचे नाव तपासत आहे
                $goalValue = $latestGoal->goal ?? $latestGoal->goal_type ?? $latestGoal->title ?? $latestGoal->type ?? null;
            } elseif ($latestWorkout) {
                $goalValue = $latestWorkout->goal;
            }

            $planTitle = null;
            if ($goalValue) {
                $planTitle = $goalValue . ' Plan';
            } elseif ($latestWorkout) {
                $planTitle = $latestWorkout->title;
            }

            return response()->json([
                'status' => true,
                'goal' => $goalValue,
                'latest_goal' => $goalValue,
                'title' => $planTitle,
                'start_date' => $latestGoal ? ($latestGoal->start_date ?? null) : ($latestWorkout ? ($latestWorkout->start_date ?? null) : null),
                'target_date' => $latestGoal ? ($latestGoal->target_date ?? ($latestGoal->end_date ?? null)) : ($latestWorkout ? ($latestWorkout->end_date ?? null) : null),
                'level' => $latestGoal ? ($latestGoal->level ?? ($latestGoal->difficulty_level ?? null)) : ($latestWorkout ? ($latestWorkout->level ?? null) : null),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id)
    {
        $member = Member::with(['trainer', 'workoutPlans.items.exercise'])->findOrFail($id);
        $dietPlans = DietPlan::where('member_id', $id)->get();

        $todayName = Carbon::now()->format('l'); 
        $todayDate = Carbon::now()->toDateString(); 
        $todaysWorkouts = collect();
        
        foreach($member->workoutPlans as $plan) {
            if($todayDate >= $plan->start_date && $todayDate <= $plan->end_date) {
                foreach($plan->items as $item) {
                    if((!empty($item->workout_date) && $item->workout_date == $todayDate) || 
                       (empty($item->workout_date) && strtolower($item->day_of_week) === strtolower($todayName))) {
                        $todaysWorkouts->push($item);
                    }
                }
            }
        }

        $today = $todayName;

        return view('admin.members.show', compact('member', 'dietPlans', 'todaysWorkouts', 'today'));
    }
}