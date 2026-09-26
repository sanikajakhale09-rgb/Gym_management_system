@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            
            <!-- Header Banner -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="p-4 rounded-4 text-white shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="fw-bold mb-1">Add New Equipment</h2>
                                <p class="text-white-50 mb-0">Register gym equipment, warranty details, and inventory status.</p>
                            </div>
                            <div>
                                <a href="{{ route('equipments.index') }}" class="btn btn-light text-primary shadow-sm px-4 py-2 rounded-pill fw-bold">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Inventory
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

            <!-- Form Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('equipments.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Equipment Name *</label>
                                <input type="text" name="name" class="form-control rounded-3" placeholder="Enter equipment name" value="{{ old('name') }}" required>
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
                                <label class="form-label fw-semibold small text-muted">Serial Number</label>
                                <input type="text" name="serial_number" class="form-control rounded-3" placeholder="Enter manufacturer serial number" value="{{ old('serial_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Location / Section</label>
                                <input type="text" name="location" class="form-control rounded-3" placeholder="Enter equipment location" value="{{ old('location') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control rounded-3" value="{{ old('purchase_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Purchase Price (₹)</label>
                                <input type="number" step="0.01" name="purchase_price" class="form-control rounded-3" placeholder="Enter Price" value="{{ old('purchase_price') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Vendor / Supplier</label>
                                <input type="text" name="vendor" class="form-control rounded-3" placeholder="Fitness Store Name" value="{{ old('vendor') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Warranty Start Date</label>
                                <input type="date" name="warranty_start_date" class="form-control rounded-3" value="{{ old('warranty_start_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Warranty End Date</label>
                                <input type="date" name="warranty_end_date" class="form-control rounded-3" value="{{ old('warranty_end_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Condition *</label>
                                <select name="condition" class="form-select rounded-3" required>
                                    <option value="New">New</option>
                                    <option value="Good" selected>Good</option>
                                    <option value="Fair">Fair</option>
                                    <option value="Damaged">Damaged</option>
                                    <option value="Needs Repair">Needs Repair</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Status *</label>
                                <select name="status" class="form-select rounded-3" required>
                                    <option value="Active" selected>Active</option>
                                    <option value="Under Maintenance">Under Maintenance</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Retired">Retired</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Invoice Number</label>
                                <input type="text" name="invoice_number" class="form-control rounded-3" placeholder="INV-1001" value="{{ old('invoice_number') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Description / Notes</label>
                                <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Enter equipment specifications, maintenance instructions, or notes...">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <a href="{{ route('equipments.index') }}" class="btn btn-secondary rounded-pill px-4 me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-5" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none;">Save Equipment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection