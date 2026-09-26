@extends('auth.sidebar')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 text-center bg-white">
                
                <!-- Icon / Header -->
                <div class="mb-4">
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px;">
                        <i class="fas fa-qrcode fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Gym Check-in Portal</h3>
                    <p class="text-muted small">Scan the QR code below from your mobile or enter details manually</p>
                </div>

                <!-- QR Code Box (थेट सेल्फ चेक-इन पेजची लिंक जनरेट करेल) -->
                <div class="qr-box p-3 bg-light d-inline-block border rounded-4 shadow-sm mb-4">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('member.self-checkin')) }}" alt="Self Check-in QR Code" class="img-fluid rounded">
                </div>

                <div class="alert alert-info border-0 bg-info bg-opacity-15 text-info py-2 px-3 rounded-3 mb-4 small">
                    <i class="fas fa-info-circle me-1"></i> Scan this QR code with your mobile camera to open the self check-in page.
                </div>

                <!-- Manual Check-in Form -->
                <form id="checkinForm">
                    @csrf
                    <div class="input-group input-group-lg mb-3 shadow-sm rounded-pill overflow-hidden border">
                        <input type="text" name="member_identifier" id="member_identifier" class="form-control border-0 px-4 text-center fs-6" placeholder="Enter Member ID or Phone" required>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">Check-in</button>
                    </div>
                </form>
                
                <!-- Alert Message Box -->
                <div id="alertMessage" class="mt-3"></div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- FontAwesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#checkinForm').on('submit', function(e) {
    e.preventDefault();
    
    let formData = $(this).serialize();
    let submitBtn = $(this).find('button[type="submit"]');
    
    submitBtn.prop('disabled', true).text('Processing...');

    $.ajax({
        url: "{{ route('attendance.qr-scan.store') }}",
        type: "POST",
        data: formData,
        success: function(response) {
            $('#alertMessage').html(`<div class="alert alert-success border-0 shadow-sm rounded-3 py-2">${response.message}</div>`);
            $('#member_identifier').val('');
            submitBtn.prop('disabled', false).text('Check-in');
        },
        error: function(xhr) {
            let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong!';
            $('#alertMessage').html(`<div class="alert alert-danger border-0 shadow-sm rounded-3 py-2">${errorMsg}</div>`);
            $('#member_identifier').val('');
            submitBtn.prop('disabled', false).text('Check-in');
        }
    });
});
</script>
@endsection