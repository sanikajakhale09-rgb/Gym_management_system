@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Title with Accent Line -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-2">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="fas fa-file-alt text-primary me-2"></i> Attendance Reports</h2>
            <p class="text-muted small mb-0">Track and filter member attendance history effortlessly.</p>
        </div>
        <div>
            <span class="badge bg-gradient bg-primary text-white px-3 py-2 rounded-pill shadow-sm">
                <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('d M, Y') }}
            </span>
        </div>
    </div>

    <!-- Filter Card with Gradient Accent -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden" style="background: linear-gradient(to right, #f8f9fa, #ffffff); border-left: 5px solid #0d6efd !important;">
        <div class="card-body p-4">
            <form action="{{ route('attendance.reports') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">
                        <i class="fas fa-calendar-plus text-primary me-1"></i> From Date
                    </label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control rounded-3 border-secondary-subtle shadow-none focus-ring">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">
                        <i class="fas fa-calendar-minus text-danger me-1"></i> To Date
                    </label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control rounded-3 border-secondary-subtle shadow-none focus-ring">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">
                        <i class="fas fa-user-check text-success me-1"></i> Select Member
                    </label>
                    <select name="member_id" class="form-select rounded-3 border-secondary-subtle shadow-none focus-ring">
                        <option value="">✨ All Members</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }} ({{ $m->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm hover-scale">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reports Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white text-uppercase fs-7">
                        <tr>
                            <th class="py-3 ps-4 text-white-55">#</th>
                            <th class="py-3 text-white">Member Name</th>
                            <th class="py-3 text-white">Check-in Date</th>
                            <th class="py-3 text-white">Check-in Time</th>
                            <th class="py-3 text-white">Check-out Time</th>
                            <th class="py-3 text-white text-center">Status</th>
                            <th class="py-3 pe-4 text-white text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $index => $row)
                        <tr class="transition-hover">
                            <td class="ps-4 fw-bold text-secondary">{{ $reports->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark d-flex align-items-center">
                                    <div class="avatar-sm bg-light-primary text-primary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 14px; background-color: #e7f1ff;">
                                        {{ substr($row->member->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        {{ $row->member->name ?? 'N/A' }}
                                        <div class="text-muted small fw-normal"><i class="fas fa-phone-alt fa-xs text-success me-1"></i> {{ $row->member->phone ?? 'No Phone' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="far fa-calendar-alt text-primary me-1"></i> {{ $row->date ?? $row->created_at->format('Y-m-d') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 fw-semibold">
                                    <i class="fas fa-sign-in-alt me-1"></i> {{ $row->check_in_time ?? $row->created_at->format('h:i A') }}
                                </span>
                            </td>
                            <td>
                                @if($row->check_out_time)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 fw-semibold">
                                        <i class="fas fa-sign-out-alt me-1"></i> {{ $row->check_out_time }}
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 text-dark">
                                        <i class="fas fa-clock me-1"></i> Not Checked Out
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($row->check_out_time)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-check-circle me-1"></i> Checked-Out
                                    </span>
                                @else
                                    <span class="badge bg-info bg-opacity-15 text-info px-3 py-2 rounded-pill fw-bold" style="background-color: #cff4fc !important; color: #055160 !important;">
                                        <i class="fas fa-user-clock me-1"></i> Checked-In
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 rounded-pill fw-semibold shadow-sm download-pdf-btn" 
                                    data-name="{{ $row->member->name ?? 'N/A' }}"
                                    data-phone="{{ $row->member->phone ?? 'N/A' }}"
                                    data-date="{{ $row->date ?? $row->created_at->format('Y-m-d') }}"
                                    data-checkin="{{ $row->check_in_time ?? 'N/A' }}"
                                    data-checkout="{{ $row->check_out_time ?? 'Not Checked Out' }}"
                                    data-status="{{ $row->check_out_time ? 'Checked-Out' : 'Checked-In' }}">
                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted bg-light">
                                <div class="py-4">
                                    <i class="fas fa-folder-open fa-3x text-secondary mb-3 opacity-50 d-block"></i>
                                    <h5 class="fw-bold text-secondary">No Records Found</h5>
                                    <p class="text-muted small mb-0">Try changing your date filter or selecting a different member.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($reports->hasPages())
        <div class="card-footer bg-white py-3 border-0 shadow-sm">
            <div class="d-flex justify-content-center">
                {{ $reports->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .transition-hover {
        transition: all 0.2s ease-in-out;
    }
    .transition-hover:hover {
        background-color: #fcfdfd;
        transform: scale(1.002);
    }
</style>

<!-- Include html2pdf Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pdfButtons = document.querySelectorAll('.download-pdf-btn');
        
        pdfButtons.forEach(button => {
            button.addEventListener('click', function () {
                const name = this.getAttribute('data-name');
                const phone = this.getAttribute('data-phone');
                const date = this.getAttribute('data-date');
                const checkin = this.getAttribute('data-checkin');
                const checkout = this.getAttribute('data-checkout');
                const status = this.getAttribute('data-status');
                const currentDate = new Date().toLocaleDateString();

                // Create a temporary container for PDF content
                const element = document.createElement('div');
                element.innerHTML = `
                    <div style="font-family: Arial, sans-serif; padding: 25px; color: #333; background: #fff; width: 550px; margin: auto; border: 1px solid #ddd; border-radius: 8px;">
                        <h2 style="text-align: center; color: #0d6efd; margin-bottom: 5px;">Attendance Report Slip</h2>
                        <p style="text-align: center; color: #777; font-size: 13px; margin-top: 0; margin-bottom: 25px;">Generated on: ${currentDate}</p>
                        
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                            <tr style="background: #0d6efd; color: #fff;">
                                <td colspan="2" style="padding: 10px; font-weight: bold; font-size: 14px;">Member Details</td>
                            </tr>
                            <tr>
                                <td style="padding: 9px; border-bottom: 1px solid #eee; width: 40%; font-weight: bold;">Member Name:</td>
                                <td style="padding: 9px; border-bottom: 1px solid #eee;">${name}</td>
                            </tr>
                            <tr>
                                <td style="padding: 9px; border-bottom: 1px solid #eee; font-weight: bold;">Phone Number:</td>
                                <td style="padding: 9px; border-bottom: 1px solid #eee;">${phone}</td>
                            </tr>
                        </table>

                        <table style="width: 100%; border-collapse: collapse;">
                            <tr style="background: #6c757d; color: #fff;">
                                <td colspan="2" style="padding: 10px; font-weight: bold; font-size: 14px;">Attendance Information</td>
                            </tr>
                            <tr>
                                <td style="padding: 9px; border-bottom: 1px solid #eee; width: 40%; font-weight: bold;">Check-in Date:</td>
                                <td style="padding: 9px; border-bottom: 1px solid #eee;">${date}</td>
                            </tr>
                            <tr>
                                <td style="padding: 9px; border-bottom: 1px solid #eee; font-weight: bold;">Check-in Time:</td>
                                <td style="padding: 9px; border-bottom: 1px solid #eee;">${checkin}</td>
                            </tr>
                            <tr>
                                <td style="padding: 9px; border-bottom: 1px solid #eee; font-weight: bold;">Check-out Time:</td>
                                <td style="padding: 9px; border-bottom: 1px solid #eee;">${checkout}</td>
                            </tr>
                            <tr>
                                <td style="padding: 9px; border-bottom: 1px solid #eee; font-weight: bold;">Status:</td>
                                <td style="padding: 9px; border-bottom: 1px solid #eee; color: #055160; font-weight: bold;">${status}</td>
                            </tr>
                        </table>

                        <div style="text-align: center; margin-top: 35px; font-size: 11px; color: #888;">
                            <p>Thank you! This is a computer-generated document.</p>
                        </div>
                    </div>
                `;

                // Options for PDF generation
                const opt = {
                    margin:       10,
                    filename:     'Attendance_' + name.replace(/\s+/g, '_') + '.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                // Generate and download PDF directly
                html2pdf().from(element).set(opt).save();
            });
        });
    });
</script>
@endsection