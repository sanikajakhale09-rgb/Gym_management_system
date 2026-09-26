@extends('auth.sidebar')

@section('content')

<div class="container-fluid px-4">

    <div class="row justify-content-center">

        <div class="col-xl-11 col-xxl-10">

            {{-- Page Heading --}}
            <h1 class="mt-4 text-center fw-bold"
                style="color: #1e3a8a !important;">
                All Enquiries
            </h1>

            {{-- Success Alert --}}
            @if(session('success'))

                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4"
                     role="alert">

                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>

                </div>

            @endif


            {{-- Error Alert --}}
            @if(session('error'))

                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4"
                     role="alert">

                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>

                </div>

            @endif


            {{-- Validation Errors --}}
            @if($errors->any())

                <div class="alert alert-danger shadow-sm rounded-3">

                    <strong>Please fix the following errors:</strong>

                    <ul class="mb-0 mt-2">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- Main Card --}}
            <div class="card shadow-lg border-0 rounded-4 mb-4">

                {{-- Card Header --}}
                <div class="card-header text-white py-3 rounded-top-4 d-flex justify-content-between align-items-center"
                     style="background-color: #1e3a8a;">

                    <div>

                        <i class="fas fa-table me-2 fa-lg"></i>

                        <span class="fs-5 fw-semibold">
                            Customer Leads & Enquiries
                        </span>

                    </div>

                    <a href="{{ route('enquiries.create') }}"
                       class="btn btn-light btn-sm fw-semibold">

                        <i class="fas fa-plus me-1"></i>
                        Add Enquiry

                    </a>

                </div>


                {{-- Card Body --}}
                <div class="card-body p-4 bg-white rounded-bottom-4">

                    <div class="table-responsive">

                        <table class="table table-hover table-striped align-middle text-center border">

                            <thead style="background-color: #1e3a8a; color: white;">

                                <tr>

                                    <th>#</th>
                                    <th>Name / Identifier</th>
                                    <th>Mobile Number</th>
                                    <th>Source</th>
                                    <th>Branch</th>
                                    <th>Message / Notes</th>
                                    <th>Status</th>
                                    <th>Received At</th>
                                    <th style="width: 160px;">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($enquiries as $index => $enquiry)

                                    @php

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Safe Values
                                        |--------------------------------------------------------------------------
                                        */

                                        $enquiryId = data_get($enquiry, 'id');

                                        $name = data_get(
                                            $enquiry,
                                            'name',
                                            'N/A'
                                        );

                                        $phone = data_get(
                                            $enquiry,
                                            'phone'
                                        ) ?? data_get(
                                            $enquiry,
                                            'mobile_number',
                                            'N/A'
                                        );

                                        $email = data_get(
                                            $enquiry,
                                            'email',
                                            'N/A'
                                        );

                                        $source = data_get(
                                            $enquiry,
                                            'source',
                                            'Manual'
                                        );

                                        $branchName = data_get(
                                            $enquiry,
                                            'branch.name',
                                            'Main Branch'
                                        );

                                        $message = data_get(
                                            $enquiry,
                                            'message',
                                            '-'
                                        );

                                        $status = data_get(
                                            $enquiry,
                                            'status',
                                            'New'
                                        );

                                        $createdAt = data_get(
                                            $enquiry,
                                            'created_at'
                                        );

                                        $receivedAt = 'N/A';

                                        if ($createdAt) {

                                            try {

                                                $receivedAt = \Carbon\Carbon::parse(
                                                    $createdAt
                                                )
                                                ->timezone('Asia/Kolkata')
                                                ->format('d M Y, h:i A');

                                            } catch (\Exception $exception) {

                                                $receivedAt = 'N/A';

                                            }

                                        }

                                        /*
                                        |--------------------------------------------------------------------------
                                        | WhatsApp Phone Number
                                        |--------------------------------------------------------------------------
                                        */

                                        $whatsappPhone = preg_replace(
                                            '/[^0-9]/',
                                            '',
                                            (string) $phone
                                        );

                                        if (strlen($whatsappPhone) === 10) {

                                            $whatsappPhone = '91' . $whatsappPhone;

                                        }

                                    @endphp


                                    {{-- Enquiry Row --}}
                                    <tr>

                                        {{-- Serial Number --}}
                                        <td class="fw-bold text-muted">

                                            {{ $index + 1 }}

                                        </td>


                                        {{-- Name --}}
                                        <td class="fw-semibold text-dark">

                                            {{ $name }}

                                        </td>


                                        {{-- Mobile Number --}}
                                        <td>

                                            <span class="badge bg-light text-dark border px-2 py-1">

                                                {{ $phone }}

                                            </span>

                                        </td>


                                        {{-- Source --}}
                                        <td>

                                            @if(
                                                strtolower((string) $source) === 'sms' ||
                                                strtolower((string) $source) === 'whatsapp'
                                            )

                                                <span class="badge bg-success px-3 py-2 shadow-sm">

                                                    {{ $source }}

                                                </span>

                                            @else

                                                <span class="badge bg-secondary px-3 py-2 shadow-sm">

                                                    {{ $source }}

                                                </span>

                                            @endif

                                        </td>


                                        {{-- Branch --}}
                                        <td>

                                            <span class="text-muted">

                                                {{ $branchName }}

                                            </span>

                                        </td>


                                        {{-- Message --}}
                                        <td class="text-muted text-start px-3">

                                            {{ $message }}

                                        </td>


                                        {{-- Status --}}
                                        <td>

                                            @php

                                                $statusClass = match($status) {

                                                    'New' => 'primary',

                                                    'Contacted' => 'info',

                                                    'Follow-up' => 'warning',

                                                    'Converted' => 'success',

                                                    'Lost' => 'danger',

                                                    'Completed' => 'success',

                                                    default => 'secondary'

                                                };

                                            @endphp


                                            <span class="badge bg-{{ $statusClass }} px-3 py-2 shadow-sm fw-bold">

                                                {{ $status }}

                                            </span>

                                        </td>


                                        {{-- Received At --}}
                                        <td class="small text-muted">

                                            {{ $receivedAt }}

                                        </td>


                                        {{-- Actions --}}
                                        <td>

                                            <div class="d-flex justify-content-center align-items-center gap-1">

                                                {{-- View & Reply Button --}}
                                                <button type="button"
                                                        class="btn btn-info btn-sm text-white px-2 py-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#viewReplyModal{{ $enquiryId }}"
                                                        title="View & Reply"
                                                        style="font-size: 13px;">

                                                    <i class="fas fa-eye"></i>

                                                </button>


                                                {{-- Edit Button --}}
                                                <button type="button"
                                                        class="btn btn-outline-primary btn-sm px-2 py-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $enquiryId }}"
                                                        title="Edit"
                                                        style="font-size: 13px;">

                                                    <i class="fas fa-edit"></i>

                                                </button>


                                                {{-- Delete Button --}}
                                                <button type="button"
                                                        class="btn btn-danger btn-sm px-2 py-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $enquiryId }}"
                                                        title="Delete"
                                                        style="font-size: 13px;">

                                                    <i class="fas fa-trash"></i>

                                                </button>

                                            </div>

                                        </td>

                                    </tr>


                                    {{-- ================================================= --}}
                                    {{-- VIEW & REPLY MODAL --}}
                                    {{-- ================================================= --}}

                                    <div class="modal fade"
                                         id="viewReplyModal{{ $enquiryId }}"
                                         tabindex="-1"
                                         aria-hidden="true">

                                        <div class="modal-dialog modal-dialog-centered">

                                            <div class="modal-content">

                                                {{-- Modal Header --}}
                                                <div class="modal-header text-white"
                                                     style="background-color: #1e3a8a;">

                                                    <h5 class="modal-title">

                                                        <i class="fas fa-comment-dots me-2"></i>
                                                        Enquiry Information

                                                    </h5>

                                                    <button type="button"
                                                            class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close">
                                                    </button>

                                                </div>


                                                {{-- Modal Body --}}
                                                <div class="modal-body text-start">

                                                    {{-- Customer Information --}}
                                                    <div class="mb-3 p-3 bg-light rounded border">

                                                        <p class="mb-2">

                                                            <strong>Name:</strong>
                                                            {{ $name }}

                                                        </p>

                                                        <p class="mb-2">

                                                            <strong>Mobile Number:</strong>
                                                            {{ $phone }}

                                                        </p>

                                                        <p class="mb-2">

                                                            <strong>Email:</strong>
                                                            {{ $email }}

                                                        </p>

                                                        <p class="mb-2">

                                                            <strong>Source:</strong>
                                                            {{ $source }}

                                                        </p>

                                                        <p class="mb-2">

                                                            <strong>Branch:</strong>
                                                            {{ $branchName }}

                                                        </p>

                                                        <p class="mb-2">

                                                            <strong>Status:</strong>
                                                            {{ $status }}

                                                        </p>

                                                        <p class="mb-2">

                                                            <strong>Enquiry Message:</strong><br>

                                                            {{ $message }}

                                                        </p>

                                                        <p class="mb-0">

                                                            <strong>Received At:</strong>
                                                            {{ $receivedAt }}

                                                        </p>

                     </div>


                                                    {{-- WhatsApp Reply Form --}}
                                                    @if(
                                                        $whatsappPhone !== '' &&
                                                        $whatsappPhone !== '91N/A'
                                                    )

                                                        <form action="{{ route('enquiries.whatsapp-reply', $enquiryId) }}" method="POST" target="_blank">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold text-dark">
                                                                    <i class="fab fa-whatsapp text-success me-1"></i>
                                                                    Send WhatsApp Message
                                                                </label>
                                                                <textarea name="text" class="form-control" rows="3" placeholder="Type your reply message here..." required></textarea>
                                                            </div>
                                                            <div class="text-end">
                                                                <button type="submit" class="btn btn-success">
                                                                    <i class="fab fa-whatsapp me-1"></i> Send on WhatsApp & Update Status
                                                                </button>
                                                            </div>
                                                        </form>

                                                    @else

                                                        <div class="alert alert-warning">

                                                            Mobile number is not available for WhatsApp reply.

                                                        </div>

                                                    @endif

                                                </div>


                                                {{-- Modal Footer --}}
                                                <div class="modal-footer">

                                                    <button type="button"
                                                            class="btn btn-secondary"
                                                            data-bs-dismiss="modal">

                                                        Close

                                                    </button>

                                                </div>

                                            </div>

                                        </div>

                                    </div>


                                    {{-- ================================================= --}}
                                    {{-- EDIT MODAL --}}
                                    {{-- ================================================= --}}

                                    <div class="modal fade"
                                         id="editModal{{ $enquiryId }}"
                                         tabindex="-1"
                                         aria-hidden="true">

                                        <div class="modal-dialog modal-dialog-centered">

                                            <div class="modal-content">

                                                <form action="{{ route('enquiries.update', $enquiryId) }}"
                                                      method="POST">

                                                    @csrf

                                                    @method('PUT')


                                                    {{-- Header --}}
                                                    <div class="modal-header text-white"
                                                         style="background-color: #1e3a8a;">

                                                        <h5 class="modal-title">

                                                            <i class="fas fa-edit me-2"></i>
                                                            Edit Enquiry

                                                        </h5>

                                                        <button type="button"
                                                                class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close">
                                                        </button>

                                                    </div>


                                                    {{-- Body --}}
                                                    <div class="modal-body text-start">

                                                        {{-- Name --}}
                                                        <div class="mb-3">

                                                            <label class="form-label fw-semibold">

                                                                Name

                                                            </label>

                                                            <input type="text"
                                                                   name="name"
                                                                   class="form-control"
                                                                   value="{{ $name }}"
                                                                   required>

                                                        </div>


                                                        {{-- Phone --}}
                                                        <div class="mb-3">

                                                            <label class="form-label fw-semibold">

                                                                Mobile Number

                                                            </label>

                                                            <input type="text"
                                                                   name="phone"
                                                                   class="form-control"
                                                                   value="{{ $phone }}"
                                                                   maxlength="10"
                                                                   required>

                                                        </div>


                                                        {{-- Branch --}}
                                                        <div class="mb-3">

                                                            <label class="form-label fw-semibold">

                                                                Branch

                                                            </label>

                                                            <select name="branch_id"
                                                                    class="form-select rounded-3">

                                                                <option value="">

                                                                    Select Branch

                                                                </option>

                                                                @foreach(($branches ?? []) as $branchItem)

                                                                    <option value="{{ data_get($branchItem, 'id') }}"
                                                                        @selected(
                                                                            data_get($enquiry, 'branch_id') ==
                                                                            data_get($branchItem, 'id')
                                                                        )>

                                                                        {{ data_get($branchItem, 'name', 'Unnamed Branch') }}

                                                                    </option>

                                                                @endforeach

                                                            </select>

                                                        </div>


                                                        {{-- Status --}}
                                                        <div class="mb-3">

                                                            <label class="form-label fw-semibold">

                                                                Status

                                                            </label>

                                                            <select name="status"
                                                                    class="form-select"
                                                                    required>

                                                                @foreach([
                                                                    'New',
                                                                    'Contacted',
                                                                    'Follow-up',
                                                                    'Converted',
                                                                    'Lost'
                                                                ] as $statusOption)

                                                                    <option value="{{ $statusOption }}"
                                                                        @selected($status === $statusOption)>

                                                                        {{ $statusOption }}

                                                                    </option>

                                                                @endforeach

                                                            </select>

                                                        </div>


                                                        {{-- Message --}}
                                                        <div class="mb-3">

                                                            <label class="form-label fw-semibold">

                                                                Message / Notes

                                                            </label>

                                                            <textarea name="message"
                                                                      class="form-control"
                                                                      rows="4">{{ $message }}</textarea>

                                                        </div>

                                                    </div>


                                                    {{-- Footer --}}
                                                    <div class="modal-footer">

                                                        <button type="button"
                                                                class="btn btn-secondary"
                                                                data-bs-dismiss="modal">

                                                            Cancel

                                                        </button>

                                                        <button type="submit"
                                                                class="btn text-white"
                                                                style="background-color: #1e3a8a;">

                                                            <i class="fas fa-save me-1"></i>
                                                            Update

                                                        </button>

                                                    </div>

                                                </form>

                                            </div>

                                        </div>

                                    </div>


                                    {{-- ================================================= --}}
                                    {{-- DELETE MODAL --}}
                                    {{-- ================================================= --}}

                                    <div class="modal fade"
                                         id="deleteModal{{ $enquiryId }}"
                                         tabindex="-1"
                                         aria-hidden="true">

                                        <div class="modal-dialog modal-dialog-centered">

                                            <div class="modal-content">

                                                <form action="{{ route('enquiries.destroy', $enquiryId) }}"
                                                      method="POST">

                                                    @csrf

                                                    @method('DELETE')


                                                    {{-- Header --}}
                                                    <div class="modal-header bg-danger text-white">

                                                        <h5 class="modal-title">

                                                            <i class="fas fa-trash me-2"></i>
                                                            Delete Enquiry

                                                        </h5>

                                                        <button type="button"
                                                                class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close">
                                                        </button>

                                                    </div>


                                                    {{-- Body --}}
                                                    <div class="modal-body text-center py-4">

                                                        <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>

                                                        <p class="fs-5 mb-0">

                                                            Are you sure you want to delete enquiry for

                                                            <strong>{{ $name }}</strong>?

                                                        </p>

                                                    </div>


                                                    {{-- Footer --}}
                                                    <div class="modal-footer">

                                                        <button type="button"
                                                                class="btn btn-secondary"
                                                                data-bs-dismiss="modal">

                                                            Cancel

                                                        </button>

                                                        <button type="submit"
                                                                class="btn btn-danger">

                                                            <i class="fas fa-trash me-1"></i>
                                                            Yes, Delete

                                                        </button>

                                                    </div>

                                                </form>

                                            </div>

                                        </div>

                                    </div>


                                @empty

                                    <tr>

                                        <td colspan="9"
                                            class="text-center py-5 text-muted fst-italic">

                                            <i class="fas fa-inbox fa-2x mb-3 d-block"></i>

                                            No enquiries found.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection