<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Default Values
        |--------------------------------------------------------------------------
        */

        $members = Member::all();

        $selectedMember = null;

        $measurements = collect();

        $workouts = [
            'total_sessions' => 0,
            'completed'      => 0,
            'missed'         => 0,
            'percentage'     => 0,
        ];

        $goal = null;


        /*
        |--------------------------------------------------------------------------
        | Selected Member
        |--------------------------------------------------------------------------
        */

        if ($request->filled('member_id')) {

            $selectedMember = Member::with([
                'measurements',
                'workoutPlans',
                'fitnessGoals',
                'trainer',
                'memberships'
            ])->find($request->member_id);


            /*
            |--------------------------------------------------------------------------
            | Member Found
            |--------------------------------------------------------------------------
            */

            if ($selectedMember) {

                /*
                |--------------------------------------------------------------------------
                | Body Measurements
                |--------------------------------------------------------------------------
                */

                $measurements = $selectedMember->measurements
                    ->sortBy('measurement_date')
                    ->values();


                /*
                |--------------------------------------------------------------------------
                | Workout Progress
                |--------------------------------------------------------------------------
                |
                | Existing workoutPlans() relation is used here.
                |
                */

                $totalWorkouts = $selectedMember->workoutPlans->count();

                $completedWorkouts = $selectedMember->workoutPlans
                    ->filter(function ($workout) {

                        return strtolower(
                            trim($workout->status ?? '')
                        ) === 'completed';

                    })
                    ->count();


                $missedWorkouts = $selectedMember->workoutPlans
                    ->filter(function ($workout) {

                        return strtolower(
                            trim($workout->status ?? '')
                        ) === 'missed';

                    })
                    ->count();


                /*
                |--------------------------------------------------------------------------
                | Workout Completion Percentage
                |--------------------------------------------------------------------------
                */

                $workoutPercentage = $totalWorkouts > 0
                    ? round(($completedWorkouts / $totalWorkouts) * 100)
                    : 0;


                $workouts = [
                    'total_sessions' => $totalWorkouts,
                    'completed'      => $completedWorkouts,
                    'missed'         => $missedWorkouts,
                    'percentage'     => $workoutPercentage,
                ];


                /*
                |--------------------------------------------------------------------------
                | Latest Fitness Goal
                |--------------------------------------------------------------------------
                */

                $goal = $selectedMember->fitnessGoals
                    ->sortBy(function ($item) {

                        return $item->created_at ?? $item->id;

                    })
                    ->last();


                /*
                |--------------------------------------------------------------------------
                | No Goal
                |--------------------------------------------------------------------------
                */

                if (!$goal) {
                    $goal = null;
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Return Progress Tracking View
        |--------------------------------------------------------------------------
        */

        return view(
            'trainers.progresstracking',
            compact(
                'members',
                'selectedMember',
                'measurements',
                'workouts',
                'goal'
            )
        );
    }
}