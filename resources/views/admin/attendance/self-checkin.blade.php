<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Member Self Attendance</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: linear-gradient(135deg, #1f4068, #162447);
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #1b1b2f;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .form-control {
            background: #162447;
            border: 1px solid #e43f5a;
            color: #fff;
        }
        .form-control:focus {
            background: #162447;
            color: #fff;
            border-color: #ff6584;
            box-shadow: none;
        }
        .btn-custom {
            background: #e43f5a;
            color: #fff;
            border: none;
        }
        .btn-custom:hover {
            background: #d02b46;
            color: #fff;
        }
        .btn-checkout {
            background: #f0ad4e;
            color: #fff;
            border: none;
        }
        .btn-checkout:hover {
            background: #ec971f;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4">
                <div class="text-center mb-4">
                    <h3>🏋️‍♂️ Gym Self Attendance</h3>
                    <p class="text-muted">Enter your Member ID or Phone Number</p>
                </div>

                <div id="alert-box" class="alert d-none" role="alert"></div>

                <form id="selfCheckinForm">
                    @csrf
                    <div class="mb-3">
                        <label for="member_identifier" class="form-label">Member ID / Phone Number</label>
                        <input type="text" class="form-control form-control-lg" id="member_identifier" name="member_identifier" placeholder="Enter ID or Phone" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" id="checkinBtn" class="btn btn-custom btn-lg">Check-in</button>
                    </div>
                </form>

                <!-- Check-out Extra Button (जर आधीच चेक-इन झाले असेल तर दिसेल) -->
                <div id="checkoutContainer" class="d-grid gap-2 mt-2 d-none">
                    <button type="button" id="checkoutBtn" class="btn btn-checkout btn-lg">Check-out Now</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#selfCheckinForm').on('submit', function(e) {
        e.preventDefault();
        submitAttendance('checkin');
    });

    $('#checkoutBtn').on('click', function() {
        submitAttendance('checkout');
    });

    function submitAttendance(actionType) {
        let identifier = $('#member_identifier').val();
        let alertBox = $('#alert-box');
        let checkoutContainer = $('#checkoutContainer');

        $.ajax({
            url: "{{ route('member.self-checkin.store') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                member_identifier: identifier,
                action_type: actionType
            },
            success: function(response) {
                alertBox.removeClass('d-none alert-danger alert-warning alert-info').addClass('alert-success');
                alertBox.text(response.message);

                if(response.status === 'already_checked_in') {
                    alertBox.removeClass('alert-success').addClass('alert-warning');
                    checkoutContainer.removeClass('d-none'); // चेक-आऊट बटन दाखवा
                } else {
                    checkoutContainer.addClass('d-none');
                    if(actionType === 'checkout') {
                        $('#selfCheckinForm')[0].reset();
                    }
                }
            },
            error: function(xhr) {
                alertBox.removeClass('d-none alert-success alert-warning alert-info').addClass('alert-danger');
                let errMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong!';
                alertBox.text(errMessage);
                checkoutContainer.addClass('d-none');
            }
        });
    }
</script>

</body>
</html>