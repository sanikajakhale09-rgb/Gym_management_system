@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-4">
    
    <!-- Header Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4 text-white shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h2 class="fw-bold mb-1">Expense Categories</h2>
                        <p class="text-white-50 mb-0">Manage gym expense categories dynamically.</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-light text-primary shadow-sm px-4 py-2 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus-circle me-2"></i> Add Category
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('expenses.categories.index') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group bg-light rounded-3 overflow-hidden border">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none ps-2" placeholder="Search category by name..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark rounded-3 px-4 py-2 fw-semibold">Search</button>
                    @if(request('search'))
                        <a href="{{ route('expenses.categories.index') }}" class="btn btn-outline-secondary rounded-3 ms-2">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body px-0 pb-0 pt-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase fs-7 text-muted border-top border-bottom">
                        <tr>
                            <th class="py-3 ps-4">#</th>
                            <th class="py-3">Branch</th>
                            <th class="py-3">Category Name</th>
                            <th class="py-3">Description</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index => $category)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $categories->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    {{ $category->branch->name ?? 'All Branches / General' }}
                                </span>
                            </td>
                            <td><span class="fw-semibold text-dark">{{ $category->name }}</span></td>
                            <td><span class="text-muted small">{{ $category->description ?? '-' }}</span></td>
                            <td>
                                <span class="badge {{ $category->status == 'Active' ? 'bg-success' : 'bg-secondary' }} px-2 py-1">
                                    {{ $category->status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-light text-warning border shadow-sm px-2 rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Delete / Deactivate Form -->
                                    <form action="{{ route('expenses.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure? If expenses are linked, it will be marked Inactive.');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm px-2 rounded-pill fw-semibold" title="Delete/Deactivate">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No categories found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-white border-top">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Add New Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('expenses.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Gym Branch *</label>
                        <select name="branch_id" class="form-select rounded-3" required>
                            <option value="" disabled selected>Select Branch</option>
                            @foreach($branches ?? [] as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Category Name *</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Water Bill, Maintenance" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Description</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Optional description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modals -->
@foreach($categories as $cat)
<div class="modal fade" id="editCategoryModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-warning text-dark py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('expenses.categories.update', $cat->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Gym Branch *</label>
                        <select name="branch_id" class="form-select rounded-3" required>
                            @foreach($branches ?? [] as $branch)
                                <option value="{{ $branch->id }}" {{ $cat->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Category Name *</label>
                        <input type="text" name="name" class="form-control rounded-3" value="{{ $cat->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Description</label>
                        <textarea name="description" class="form-control rounded-3" rows="3">{{ $cat->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Status</label>
                        <select name="status" class="form-select rounded-3" required>
                            <option value="Active" {{ $cat->status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $cat->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-dark">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection