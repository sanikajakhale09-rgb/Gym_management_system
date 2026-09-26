<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FitnessGoal;
use App\Models\Member;
use App\Models\Trainer;

class FitnessGoalController extends Controller
{
    public function index()
    {
        $goals = FitnessGoal::with(['member.trainer', 'member.memberships', 'trainer'])->latest()->get();
        $members = Member::with(['trainer', 'memberships', 'fitnessGoals'])->get();

        return view('trainers.fitness_goals', compact('goals', 'members'));
    }

    // Member select kelyavar JS dwara active membership details fetch karnyasathi
    public function getMemberDetails($id)
    {
        $member = Member::with(['membershipPlan', 'trainer'])->find($id);

        if (!$member) {
            return response()->json(['status' => false, 'message' => 'Member not found']);
        }

        return response()->json([
            'status' => true,
            'member' => $member,
            'membership_plan' => $member->membershipPlan ? $member->membershipPlan->name : 'No active membership found',
            'joining_date' => $member->joining_date,
            'expiry_date' => $member->expiry_date,
            'trainer_id' => $member->trainer_id,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'goal_type' => 'required',
            'starting_value' => 'required',
            'current_value' => 'required',
            'target_value' => 'required',
            'start_date' => 'required|date',
        ]);

        $user = Auth::user();

        FitnessGoal::create([
            'gym_id' => $user->gym_id ?? 1,
            'branch_id' => $user->branch_id ?? 1,
            'member_id' => $request->member_id,
            'trainer_id' => $request->trainer_id,
            'goal_type' => $request->goal_type,
            'starting_value' => $request->starting_value,
            'current_value' => $request->current_value,
            'target_value' => $request->target_value,
            'start_date' => $request->start_date,
            'target_date' => $request->target_date,
            'status' => 'Active',
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Fitness Goal added successfully!');
    }

    public function edit(int $id)
    {
        $goal = FitnessGoal::findOrFail($id);
        $goals = FitnessGoal::with(['member', 'trainer'])->get();
        $members = Member::all();
        $trainers = Trainer::all();
        
        return view('trainers.fitness_goals', compact('goals', 'members', 'trainers', 'goal'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'goal_type' => 'required',
            'starting_value' => 'required',
            'current_value' => 'required',
            'target_value' => 'required',
            'start_date' => 'required|date',
        ]);

        $goal = FitnessGoal::findOrFail($id);

        $goal->update([
            'member_id' => $request->member_id,
            'trainer_id' => $request->trainer_id,
            'goal_type' => $request->goal_type,
            'starting_value' => $request->starting_value,
            'current_value' => $request->current_value,
            'target_value' => $request->target_value,
            'start_date' => $request->start_date,
            'target_date' => $request->target_date,
            'status' => $request->status ?? $goal->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('fitness-goals.index')->with('success', 'Fitness Goal updated successfully!');
    }

    public function destroy(int $id)
    {
        $goal = FitnessGoal::findOrFail($id);
        $goal->delete();

        return redirect()->route('fitness-goals.index')->with('success', 'Fitness Goal deleted successfully!');
    }
}