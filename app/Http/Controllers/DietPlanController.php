<?php

namespace App\Http\Controllers;

use App\Models\DietPlan;
use App\Models\Member;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DietPlanController extends Controller
{
    public function index()
    {
        $gymId = Auth::user()->gym_id ?? null;
        $branchId = Auth::user()->branch_id ?? null;

        $query = DietPlan::with('member', 'trainer')->latest('measurement_date');

        if ($gymId && Schema::hasColumn('diet_plans', 'gym_id')) {
            $query->where('gym_id', $gymId);
        }
        if ($branchId && Schema::hasColumn('diet_plans', 'branch_id')) {
            $query->where('branch_id', $branchId);
        }

        $dietPlans = $query->get();

        return view('trainers.diet', compact('dietPlans'));
    }

    public function create()
    {
        $gymId = Auth::user()->gym_id ?? null;
        $branchId = Auth::user()->branch_id ?? null;

        $memberQuery = Member::query();
        if ($gymId && Schema::hasColumn('members', 'gym_id')) {
            $memberQuery->where('gym_id', $gymId);
        }
        if ($branchId && Schema::hasColumn('members', 'branch_id')) {
            $memberQuery->where('branch_id', $branchId);
        }
        $members = $memberQuery->get();

        $trainerQuery = Trainer::query();
        if ($gymId && Schema::hasColumn('trainers', 'gym_id')) {
            $trainerQuery->where('gym_id', $gymId);
        }
        if ($branchId && Schema::hasColumn('trainers', 'branch_id')) {
            $trainerQuery->where('branch_id', $branchId);
        }
        $trainers = $trainerQuery->get();

        $branches = Schema::hasTable('branches') ? DB::table('branches')->get() : collect();
        $gyms = Schema::hasTable('gyms') ? DB::table('gyms')->get() : collect();

        return view('trainers.diet', compact('members', 'trainers', 'branches', 'gyms'));
    }

    // Member wise active diet plans fetch karnyacha AJAX endpoint method
    public function getMemberDietPlans(int $id)
    {
        $gymId = Auth::user()->gym_id ?? null;
        $branchId = Auth::user()->branch_id ?? null;

        $query = DietPlan::where('member_id', $id)->latest('start_date');

        if ($gymId && Schema::hasColumn('diet_plans', 'gym_id')) {
            $query->where('gym_id', $gymId);
        }
        if ($branchId && Schema::hasColumn('diet_plans', 'branch_id')) {
            $query->where('branch_id', $branchId);
        }

        $dietPlans = $query->get();

        return response()->json($dietPlans);
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id'        => 'required|exists:members,id',
            'trainer_id'       => 'nullable|exists:trainers,id',
            'title'            => 'required|string|max:255',
            'measurement_date' => 'nullable|date',
            'start_date'       => 'nullable|date',
            'end_date'         => 'nullable|date',
            'breakfast'        => 'nullable|string',
            'mid_morning'      => 'nullable|string',
            'lunch'            => 'nullable|string',
            'evening'          => 'nullable|string',
            'dinner'           => 'nullable|string',
            'goal_type'        => 'nullable|string|max:100',
            'starting_value'   => 'nullable|string|max:50',
            'current_value'    => 'nullable|string|max:50',
            'target_value'     => 'nullable|string|max:50',
            'weight'           => 'nullable|numeric',
            'height'           => 'nullable|numeric',
            'bmi'              => 'nullable|numeric',
            'chest'            => 'nullable|string|max:50',
            'waist'            => 'nullable|string|max:50',
            'biceps'           => 'nullable|string|max:50',
            'hip'              => 'nullable|string|max:50',
            'thigh'            => 'nullable|string|max:50',
            'body_fat'         => 'nullable|numeric',
            'before_photo'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'after_photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $beforePhotoPath = $request->hasFile('before_photo') ? $request->file('before_photo')->store('progress_photos', 'public') : null;
        $afterPhotoPath = $request->hasFile('after_photo') ? $request->file('after_photo')->store('progress_photos', 'public') : null;

        $startDate = $request->start_date ?? $request->measurement_date ?? now()->toDateString();
        $endDate = $request->end_date ?? \Carbon\Carbon::parse($startDate)->addDays(30)->toDateString();

        $data = [
            'member_id'        => $request->member_id,
            'trainer_id'       => $request->filled('trainer_id') ? $request->trainer_id : null,
            'title'            => $request->title,
            'measurement_date' => $request->measurement_date ?? now()->toDateString(),
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'breakfast'        => $request->breakfast,
            'mid_morning'      => $request->mid_morning,
            'lunch'            => $request->lunch,
            'evening'          => $request->evening,
            'dinner'           => $request->dinner,
            'goal_type'        => $request->goal_type,
            'starting_value'   => $request->starting_value,
            'current_value'    => $request->current_value,
            'target_value'     => $request->target_value,
            'weight'           => $request->weight,
            'height'           => $request->height,
            'bmi'              => $request->bmi,
            'chest'            => $request->chest,
            'waist'            => $request->waist,
            'biceps'           => $request->biceps,
            'hip'              => $request->hip,
            'thigh'            => $request->thigh,
            'body_fat'         => $request->body_fat,
            'before_photo'     => $beforePhotoPath,
            'after_photo'      => $afterPhotoPath,
            'gym_id'           => Auth::user()->gym_id ?? null,
            'branch_id'        => Auth::user()->branch_id ?? null,
        ];

        DietPlan::create($data);

        return redirect()->route('members.show', $request->member_id)
                         ->with('success', 'Diet Plan & Measurements history updated successfully.');
    }
}