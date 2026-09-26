<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrainerRequest;
use App\Models\Branch;
use App\Models\Trainer;
use App\Models\Member;
use App\Models\Exercise; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function create(): View
    {
        $user = Auth::user();
        $gymId = $user?->gym_id ?? session('current_gym_id');
        
        $branches = $gymId ? Branch::where('gym_id', $gymId)->get() : Branch::all();

        return view('trainers.create', compact('branches'));
    }

    public function store(StoreTrainerRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $gymId = $user?->gym_id ?? session('current_gym_id') ?? 1;

        Trainer::create([
            'gym_id' => $gymId,
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'specialization' => $request->specialization,
            'experience' => $request->experience,
            'salary' => $request->salary,
            'status' => $request->status,
        ]);

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer added successfully.');
    }

    public function index(): View
    {
        $user = Auth::user();
        $gymId = $user?->gym_id ?? session('current_gym_id');

        $trainers = $gymId ? Trainer::with('branch')->whereHas('branch', function($q) use ($gymId) {
            $q->where('gym_id', $gymId);
        })->get() : Trainer::with('branch')->get();

        return view('trainers.index', compact('trainers'));
    }

    public function show(int $id)
    {
        $trainer = Trainer::with('branch')->findOrFail($id);
        
        return response()->json($trainer);
    }

    public function edit(int $id): View
    {
        $trainer = Trainer::findOrFail($id);
        $user = Auth::user();
        $gymId = $user?->gym_id ?? session('current_gym_id');
        
        $branches = $gymId ? Branch::where('gym_id', $gymId)->get() : Branch::all();
        
        return view('trainers.edit', compact('trainer', 'branches'));
    }

    public function update(StoreTrainerRequest $request, int $id): RedirectResponse
    {
        $trainer = Trainer::findOrFail($id);
        
        $trainer->update([
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'specialization' => $request->specialization,
            'experience' => $request->experience,
            'salary' => $request->salary,
            'status' => $request->status,
        ]);

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $trainer = Trainer::findOrFail($id);
        $trainer->delete();

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer deleted successfully.');
    }

    public function assignMembers(): View
    {
        $user = Auth::user();
        $gymId = $user?->gym_id ?? session('current_gym_id');
        
        $trainers = Trainer::all();
        $members = Member::all();
        $branches = $gymId ? Branch::where('gym_id', $gymId)->get() : Branch::all();
        $assignedMembers = Member::with(['trainer', 'branch'])->whereNotNull('trainer_id')->get();

        return view('trainers.assign-members', compact('trainers', 'members', 'branches', 'assignedMembers'));
    }

    public function storeAssignedMember(Request $request): RedirectResponse
    {
        $request->validate([
            'trainer_id' => 'required|exists:trainers,id',
            'member_id' => 'required|exists:members,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'trainer_charges' => 'nullable|numeric',
        ]);

        $member = Member::findOrFail($request->member_id);
        
        $member->update([
            'trainer_id' => $request->trainer_id,
            'start_date' => $request->start_date ?? now()->toDateString(),
            'end_date' => $request->end_date ?? now()->addMonth()->toDateString(),
            'trainer_charges' => $request->trainer_charges ?? 1500,
        ]);

        return redirect()->route('trainers.assign')
            ->with('success', 'Trainer assigned successfully!');
    }
   public function unassignMember(int $id): RedirectResponse
{
    $member = Member::findOrFail($id);
    
    $member->update([
        'trainer_id' => null,
        'start_date' => null,
        'end_date' => null,
        'trainer_charges' => 0 // Changed from null to 0 to satisfy the NOT NULL constraint
    ]);

    return redirect()->route('trainers.assign')
        ->with('success', 'Member unassigned successfully!');
}

    public function getTrainersByBranch(int $branchId): JsonResponse
    {
        $trainers = Trainer::where('branch_id', $branchId)->get();
        return response()->json($trainers);
    }

    public function getMembersByBranch(int $branchId): JsonResponse
    {
        $members = Member::where('branch_id', $branchId)->get();
        return response()->json($members);
    }

    // --- एक्सरसाइज लायब्ररी फंक्शन्स ---

    public function exerciseIndex(Request $request): View
    {
        $query = Exercise::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('filter_muscle')) {
            $query->where('muscle_group', $request->filter_muscle);
        }

        $exercises = $query->latest()->paginate(10);

        return view('trainers.exercise', compact('exercises'));
    }

    public function exerciseStore(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $gymId = $request->gym_id ?? $user?->gym_id ?? session('current_gym_id') ?? 1;
        $branchId = $request->branch_id ?? $user?->branch_id ?? session('current_branch_id') ?? 1;

        $request->validate([
            'name' => 'required|string|max:255',
            'muscle_group' => 'required|string|max:100',
            'difficulty_level' => 'nullable|string|max:50',
            'equipment' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'media_reference' => 'nullable|string',
        ]);

        Exercise::create([
            'gym_id' => $gymId,
            'branch_id' => $branchId,
            'name' => $request->name,
            'muscle_group' => $request->muscle_group,
            'difficulty_level' => $request->difficulty_level,
            'equipment' => $request->equipment,
            'description' => $request->description,
            'media_reference' => $request->media_reference,
        ]);

        return redirect()->route('exercises.index')->with('success', 'Exercise added to library successfully!');
    }

    public function exerciseDestroy(int $id): RedirectResponse
    {
        $exercise = Exercise::findOrFail($id);
        $exercise->delete();

        return redirect()->route('exercises.index')->with('success', 'Exercise deleted successfully!');
    }

    public function exerciseEdit(int $id): View
    {
        $exercise = Exercise::findOrFail($id);
        $exercises = Exercise::latest()->paginate(10); 
        
        return view('trainers.exercise', compact('exercise', 'exercises'));
    }

    public function exerciseUpdate(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'muscle_group' => 'required|string|max:100',
            'difficulty_level' => 'nullable|string|max:50',
            'equipment' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'media_reference' => 'nullable|string',
        ]);

        $exercise = Exercise::findOrFail($id);
        
        $exercise->update([
            'name' => $request->name,
            'muscle_group' => $request->muscle_group,
            'difficulty_level' => $request->difficulty_level,
            'equipment' => $request->equipment,
            'description' => $request->description,
            'media_reference' => $request->media_reference,
        ]);

        return redirect()->route('exercises.index')->with('success', 'Exercise updated successfully!');
    }
}