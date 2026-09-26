@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-4">
    
    <!-- Header Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4 text-white shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h2 class="fw-bold mb-1">Equipment Inventory</h2>
                        <p class="text-white-50 mb-0">Manage gym equipment, serial numbers, warranty, and condition.</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-light text-primary shadow-sm px-4 py-2 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                            <i class="fas fa-plus-circle me-2"></i> Add Equipment
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
            <form method="GET" action="{{ route('equipments.index') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group bg-light rounded-3 overflow-hidden border">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none ps-2" placeholder="Search by equipment name or serial no..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark rounded-3 px-4 py-2 fw-semibold">Search</button>
                    @if(request('search'))
                        <a href="{{ route('equipments.index') }}" class="btn btn-outline-secondary rounded-3 ms-2">Reset</a>
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
                            <th class="py-3">Equipment Name</th>
                            <th class="py-3">Category</th>
                            <th class="py-3">Branch</th>
                            <th class="py-3">Serial Number</th>
                            <th class="py-3">Purchase Date</th>
                            <th class="py-3">Purchase Price</th>
                            <th class="py-3">Warranty Expiry</th>
                            <th class="py-3">Location</th>
                            <th class="py-3">Condition</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipments as $index => $item)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $equipments->firstItem() + $index }}</td>
                            <td><span class="fw-semibold text-dark">{{ $item->name }}</span></td>
                            <td><span class="text-muted">{{ $item->category ?? '-' }}</span></td>
                            <td><span class="text-muted">{{ $item->branch->name ?? 'Main Branch' }}</span></td>
                            <td><span class="badge bg-light text-dark border">{{ $item->serial_number ?? 'N/A' }}</span></td>
                            <td><span class="text-muted">{{ $item->purchase_date ? \Carbon\Carbon::parse($item->purchase_date)->format('d M Y') : '-' }}</span></td>
                            <td><span class="fw-semibold text-dark">{{ $item->purchase_price ? '₹' . number_format($item->purchase_price, 2) : '-' }}</span></td>
                            <td>
                                @if($item->warranty_end_date)
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-semibold">{{ \Carbon\Carbon::parse($item->warranty_end_date)->format('d M Y') }}</span>
                                        <span>
                                            @if(\Carbon\Carbon::parse($item->warranty_end_date)->isFuture())
                                                <span class="badge bg-success px-2 py-0.5 mt-1" style="font-size: 11px;">Valid</span>
                                            @else
                                                <span class="badge bg-danger px-2 py-0.5 mt-1" style="font-size: 11px;">Expired</span>
                                            @endif
                                        </span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><span class="text-muted">{{ $item->location ?? '-' }}</span></td>
                            <td>
                                <span class="badge 
                                    @if($item->condition == 'New') bg-info 
                                    @elseif($item->condition == 'Good') bg-success 
                                    @elseif($item->condition == 'Fair') bg-primary 
                                    @else bg-danger @endif px-2 py-1">
                                    {{ $item->condition }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $item->status == 'Active' ? 'bg-success' : ($item->status == 'Under Maintenance' ? 'bg-warning text-dark' : 'bg-secondary') }} px-2 py-1">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-light text-warning border shadow-sm px-2 rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#editEquipmentModal{{ $item->id }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Delete Form -->
                                    <form action="{{ route('equipments.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this equipment?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm px-2 rounded-pill fw-semibold" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-5 text-muted">No equipment records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-white border-top">
                {{ $equipments->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Equipment Modal -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Add New Equipment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('equipments.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Equipment Name *</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Treadmill, Bench Press" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Equipment Category</label>
                            <select name="category" class="form-select rounded-3">
                                <option value="" selected>Select Category</option>
                                <option value="Cardio">Cardio</option>
                                <option value="Strength">Strength</option>
                                <option value="Free Weights">Free Weights</option>
                                <option value="Flexibility">Flexibility</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Gym Branch</label>
                            <select name="branch_id" class="form-select rounded-3">
                                <option value="" selected>Select Branch</option>
                                @foreach($branches ?? [] as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Serial Number</label>
                            <input type="text" name="serial_number" class="form-control rounded-3" placeholder="TM2026001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Location / Section</label>
                            <input type="text" name="location" class="form-control rounded-3" placeholder="Cardio Section">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Vendor / Supplier</label>
                            <input type="text" name="vendor" class="form-control rounded-3" placeholder="Fitness Store Name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control rounded-3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted">Purchase Price (₹)</label>
                            <input type="number" step="0.01" name="purchase_price" class="form-control rounded-3" placeholder="85000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted">Invoice Number</label>
                            <input type="text" name="invoice_number" class="form-control rounded-3" placeholder="INV-1001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Warranty Start Date</label>
                            <input type="date" name="warranty_start_date" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Warranty End Date</label>
                            <input type="date" name="warranty_end_date" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Condition *</label>
                            <select name="condition" class="form-select rounded-3" required>
                                <option value="New">New</option>
                                <option value="Good" selected>Good</option>
                                <option value="Fair">Fair</option>
                                <option value="Damaged">Damaged</option>
                                <option value="Needs Repair">Needs Repair</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Status *</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="Active" selected>Active</option>
                                <option value="Under Maintenance">Under Maintenance</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Retired">Retired</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-muted">Description / Notes</label>
                            <textarea name="description" class="form-control rounded-3" rows="2" placeholder="Enter equipment specifications, maintenance instructions, or notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none;">Save Equipment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Equipment Modals -->
@foreach($equipments as $item)
<div class="modal fade" id="editEquipmentModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Edit Equipment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('equipments.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Equipment Name *</label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ $item->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Equipment Category</label>
                            <select name="category" class="form-select rounded-3">
                                <option value="">Select Category</option>
                                <option value="Cardio" {{ $item->category == 'Cardio' ? 'selected' : '' }}>Cardio</option>
                                <option value="Strength" {{ $item->category == 'Strength' ? 'selected' : '' }}>Strength</option>
                                <option value="Free Weights" {{ $item->category == 'Free Weights' ? 'selected' : '' }}>Free Weights</option>
                                <option value="Flexibility" {{ $item->category == 'Flexibility' ? 'selected' : '' }}>Flexibility</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Gym Branch</label>
                            <select name="branch_id" class="form-select rounded-3">
                                <option value="">Select Branch</option>
                                @foreach($branches ?? [] as $branch)
                                    <option value="{{ $branch->id }}" {{ $item->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Serial Number</label>
                            <input type="text" name="serial_number" class="form-control rounded-3" value="{{ $item->serial_number }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Location / Section</label>
                            <input type="text" name="location" class="form-control rounded-3" value="{{ $item->location }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Vendor / Supplier</label>
                            <input type="text" name="vendor" class="form-control rounded-3" value="{{ $item->vendor }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control rounded-3" value="{{ $item->purchase_date }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted">Purchase Price (₹)</label>
                            <input type="number" step="0.01" name="purchase_price" class="form-control rounded-3" value="{{ $item->purchase_price }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted">Invoice Number</label>
                            <input type="text" name="invoice_number" class="form-control rounded-3" value="{{ $item->invoice_number }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Warranty Start Date</label>
                            <input type="date" name="warranty_start_date" class="form-control rounded-3" value="{{ $item->warranty_start_date }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Warranty End Date</label>
                            <input type="date" name="warranty_end_date" class="form-control rounded-3" value="{{ $item->warranty_end_date }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Condition *</label>
                            <select name="condition" class="form-select rounded-3" required>
                                <option value="New" {{ $item->condition == 'New' ? 'selected' : '' }}>New</option>
                                <option value="Good" {{ $item->condition == 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ $item->condition == 'Fair' ? 'selected' : '' }}>Fair</option>
                                <option value="Damaged" {{ $item->condition == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                                <option value="Needs Repair" {{ $item->condition == 'Needs Repair' ? 'selected' : '' }}>Needs Repair</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Status *</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="Active" {{ $item->status == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Under Maintenance" {{ $item->status == 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                <option value="Inactive" {{ $item->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="Retired" {{ $item->status == 'Retired' ? 'selected' : '' }}>Retired</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-muted">Description / Notes</label>
                            <textarea name="description" class="form-control rounded-3" rows="2">{{ $item->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 text-white" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none;">Update Equipment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection