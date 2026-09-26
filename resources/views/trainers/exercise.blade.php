@extends('auth.sidebar')

@section('title', 'Exercise Library -  Gym Management')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8fafc; min-height: 100vh;">
    
    <div class="text-center mb-4">
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="fas fa-dumbbell text-primary me-2"></i>Exercise Library
        </h1>
        <p class="text-muted small mb-0">Central repository of exercises categorized by muscle groups</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert" style="background-color: #d1fae5; color: #065f46;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Add Exercise Form -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-header text-white py-3 px-4 border-0" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);">
                    <h5 class="m-0 fw-bold"><i class="fas fa-plus-circle me-2"></i>Add New Exercise</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('exercises.store') }}" method="POST">
                        @csrf
                        
                        <!-- Multi-Tenant & Branch Scoping (BRD Sec 6.3) -->
                        <input type="hidden" name="gym_id" value="{{ auth()->user()->gym_id ?? 1 }}">
                        <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id ?? 1 }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-secondary">Exercise Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control rounded-pill py-2 shadow-none @error('name') is-invalid @enderror" placeholder="e.g. Bench Press" required>
                            @error('name')
                                <div class="invalid-feedback px-3">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Muscle Group Categorization (BRD Sec 5.7) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-secondary">Muscle Group <span class="text-danger">*</span></label>
                            <select name="muscle_group" class="form-select rounded-pill py-2 shadow-none @error('muscle_group') is-invalid @enderror" required>
                                <option value="">Select Muscle Group</option>
                                <option value="Chest" {{ old('muscle_group') == 'Chest' ? 'selected' : '' }}>Chest</option>
                                <option value="Back" {{ old('muscle_group') == 'Back' ? 'selected' : '' }}>Back</option>
                                <option value="Shoulders" {{ old('muscle_group') == 'Shoulders' ? 'selected' : '' }}>Shoulders</option>
                                <option value="Biceps" {{ old('muscle_group') == 'Biceps' ? 'selected' : '' }}>Biceps</option>
                                <option value="Triceps" {{ old('muscle_group') == 'Triceps' ? 'selected' : '' }}>Triceps</option>
                                <option value="Legs" {{ old('muscle_group') == 'Legs' ? 'selected' : '' }}>Legs</option>
                                <option value="Abs" {{ old('muscle_group') == 'Abs' ? 'selected' : '' }}>Abs / Core</option>
                                <option value="Full Body" {{ old('muscle_group') == 'Full Body' ? 'selected' : '' }}>Full Body</option>
                            </select>
                            @error('muscle_group')
                                <div class="invalid-feedback px-3">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Difficulty Level Dropdown -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-secondary">Difficulty Level</label>
                            <select name="difficulty_level" class="form-select rounded-pill py-2 shadow-none @error('difficulty_level') is-invalid @enderror">
                                <option value="Beginner" {{ old('difficulty_level') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="Intermediate" {{ old('difficulty_level') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="Advanced" {{ old('difficulty_level') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            @error('difficulty_level')
                                <div class="invalid-feedback px-3">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Equipment Required -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-secondary">Equipment Required</label>
                            <input type="text" name="equipment" value="{{ old('equipment') }}" class="form-control rounded-pill py-2 shadow-none @error('equipment') is-invalid @enderror" placeholder="e.g. Barbell, Dumbbell, Machine">
                            @error('equipment')
                                <div class="invalid-feedback px-3">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Detailed Descriptions (BRD Sec 5.7) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-secondary">Description</label>
                            <textarea name="description" class="form-control rounded-4 shadow-none @error('description') is-invalid @enderror" rows="3" placeholder="Brief instruction...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback px-3">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Media Reference (BRD Sec 5.7) -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-uppercase text-secondary">Media Reference (URL)</label>
                            <input type="url" name="media_reference" value="{{ old('media_reference') }}" class="form-control rounded-pill py-2 shadow-none @error('media_reference') is-invalid @enderror" placeholder="YouTube or Video Link">
                            @error('media_reference')
                                <div class="invalid-feedback px-3">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold shadow-sm" style="background-color: #4f46e5; border: none;">
                            <i class="fas fa-save me-1"></i> Save Exercise
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Exercise Table List & Filters -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-header text-white py-3 px-4 border-0 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                    <h5 class="m-0 fw-bold"><i class="fas fa-list me-2"></i>Exercise Library List</h5>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Search & Filters (BRD Sec 5.15) -->
                    <form method="GET" action="{{ route('exercises.index') }}" class="row g-3 mb-4">
                        <div class="col-md-7">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control rounded-start-pill py-2 shadow-none" placeholder="Search exercises..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary rounded-end-pill px-3" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <select name="filter_muscle" class="form-select rounded-pill py-2 shadow-none" onchange="this.form.submit()">
                                <option value="">All Muscle Groups</option>
                                <option value="Chest" {{ request('filter_muscle') == 'Chest' ? 'selected' : '' }}>Chest</option>
                                <option value="Back" {{ request('filter_muscle') == 'Back' ? 'selected' : '' }}>Back</option>
                                <option value="Shoulders" {{ request('filter_muscle') == 'Shoulders' ? 'selected' : '' }}>Shoulders</option>
                                <option value="Biceps" {{ request('filter_muscle') == 'Biceps' ? 'selected' : '' }}>Biceps</option>
                                <option value="Triceps" {{ request('filter_muscle') == 'Triceps' ? 'selected' : '' }}>Triceps</option>
                                <option value="Legs" {{ request('filter_muscle') == 'Legs' ? 'selected' : '' }}>Legs</option>
                                <option value="Abs" {{ request('filter_muscle') == 'Abs' ? 'selected' : '' }}>Abs / Core</option>
                                <option value="Full Body" {{ request('filter_muscle') == 'Full Body' ? 'selected' : '' }}>Full Body</option>
                            </select>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-uppercase small">
                                <tr>
                                    <th>Exercise Name</th>
                                    <th>Muscle Group</th>
                                    <th>Level / Equip</th>
                                    <th>Description</th>
                                    <th>Media</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($exercises)
                                    @forelse($exercises as $exercise)
                                        <tr>
                                            <td class="fw-semibold text-dark">{{ $exercise->name ?? '' }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">{{ $exercise->muscle_group ?? '' }}</span>
                                            </td>
                                            <td class="small text-muted">
                                                <div>{{ $exercise->difficulty_level ?? 'Beginner' }}</div>
                                                <span class="text-primary">{{ $exercise->equipment ?? 'N/A' }}</span>
                                            </td>
                                            <td class="text-muted small">
                                                {{ !empty($exercise->description) ? \Illuminate\Support\Str::limit($exercise->description, 25) : '-' }}
                                            </td>
                                            <td>
                                                @if(!empty($exercise->media_reference))
                                                    <a href="{{ $exercise->media_reference }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0">
                                                        <i class="fas fa-play-circle"></i> View
                                                    </a>
                                                @else
                                                    <span class="text-muted small">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <!-- View Details Button -->
                                                    <button type="button" class="btn btn-sm btn-outline-info rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" data-bs-toggle="modal" data-bs-target="#viewExerciseModal{{ $exercise->id }}" title="View Details">
                                                        <i class="fas fa-eye small"></i>
                                                    </button>

                                                    <!-- Edit Button -->
                                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-circle p-1 d-flex align-items-center justify-content-center text-dark" style="width: 30px; height: 30px;" data-bs-toggle="modal" data-bs-target="#editExerciseModal{{ $exercise->id }}" title="Edit">
                                                        <i class="fas fa-edit small"></i>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form action="{{ route('exercises.destroy', $exercise->id) }}" method="POST" class="d-inline m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" onclick="return confirm('Are you sure you want to delete this exercise?')" title="Delete">
                                                            <i class="fas fa-trash-alt small"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- View Modal -->
                                        <div class="modal fade" id="viewExerciseModal{{ $exercise->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <div class="modal-header bg-dark text-white rounded-top-4">
                                                        <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i>Exercise Details</h5>
                                                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4 text-start">
                                                        <p class="mb-2"><strong>Name:</strong> {{ $exercise->name }}</p>
                                                        <p class="mb-2"><strong>Muscle Group:</strong> <span class="badge bg-light text-dark border">{{ $exercise->muscle_group }}</span></p>
                                                        <p class="mb-2"><strong>Difficulty Level:</strong> {{ $exercise->difficulty_level ?? 'N/A' }}</p>
                                                        <p class="mb-2"><strong>Equipment:</strong> {{ $exercise->equipment ?? 'N/A' }}</p>
                                                        <p class="mb-2"><strong>Description:</strong> {{ $exercise->description ?? 'No description provided.' }}</p>
                                                        <p class="mb-0"><strong>Media Link:</strong> 
                                                            @if(!empty($exercise->media_reference))
                                                                <a href="{{ $exercise->media_reference }}" target="_blank" class="text-primary text-decoration-underline">Watch Video</a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editExerciseModal{{ $exercise->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);">
                                                        <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Exercise</h5>
                                                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('exercises.update', $exercise->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body p-4 text-start">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-uppercase text-secondary">Exercise Name <span class="text-danger">*</span></label>
                                                                <input type="text" name="name" value="{{ $exercise->name }}" class="form-control rounded-pill py-2 shadow-none" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-uppercase text-secondary">Muscle Group <span class="text-danger">*</span></label>
                                                                <select name="muscle_group" class="form-select rounded-pill py-2 shadow-none" required>
                                                                    <option value="Chest" {{ $exercise->muscle_group == 'Chest' ? 'selected' : '' }}>Chest</option>
                                                                    <option value="Back" {{ $exercise->muscle_group == 'Back' ? 'selected' : '' }}>Back</option>
                                                                    <option value="Shoulders" {{ $exercise->muscle_group == 'Shoulders' ? 'selected' : '' }}>Shoulders</option>
                                                                    <option value="Biceps" {{ $exercise->muscle_group == 'Biceps' ? 'selected' : '' }}>Biceps</option>
                                                                    <option value="Triceps" {{ $exercise->muscle_group == 'Triceps' ? 'selected' : '' }}>Triceps</option>
                                                                    <option value="Legs" {{ $exercise->muscle_group == 'Legs' ? 'selected' : '' }}>Legs</option>
                                                                    <option value="Abs" {{ $exercise->muscle_group == 'Abs' ? 'selected' : '' }}>Abs / Core</option>
                                                                    <option value="Full Body" {{ $exercise->muscle_group == 'Full Body' ? 'selected' : '' }}>Full Body</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-uppercase text-secondary">Difficulty Level</label>
                                                                <select name="difficulty_level" class="form-select rounded-pill py-2 shadow-none">
                                                                    <option value="Beginner" {{ $exercise->difficulty_level == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                                                    <option value="Intermediate" {{ $exercise->difficulty_level == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                                                    <option value="Advanced" {{ $exercise->difficulty_level == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-uppercase text-secondary">Equipment Required</label>
                                                                <input type="text" name="equipment" value="{{ $exercise->equipment }}" class="form-control rounded-pill py-2 shadow-none" placeholder="e.g. Barbell, Dumbbell">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-uppercase text-secondary">Description</label>
                                                                <textarea name="description" class="form-control rounded-4 shadow-none" rows="3">{{ $exercise->description }}</textarea>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small text-uppercase text-secondary">Media Reference (URL)</label>
                                                                <input type="url" name="media_reference" value="{{ $exercise->media_reference }}" class="form-control rounded-pill py-2 shadow-none">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 px-4 pb-4">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #4f46e5; border: none;">Update Exercise</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No exercises found in the library.</td>
                                        </tr>
                                    @endforelse
                                @endisset
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    @if(method_exists($exercises, 'links'))
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $exercises->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection