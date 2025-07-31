@extends('layouts.chat-settings')

@section('title', 'Attendence | Settings')

@section('form-section')
    <x-chat-settings.settings-header :message="'Update your checkin and checkout daily and manage your attendance'"
        :heading="'Attendance Settings'" />

    <div class="settings-form">
        <!-- Last checkin details  -->
        <div class="form-section">
            <div class="section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentcolor"
                        d="M7 11c-1.1 0-2-.9-2-2V8c0-1.1.9-2 2-2s2 .9 2 2v1c0 1.1-.9 2-2 2m-2 6.993L9 18c.55 0 1-.45 1-1v-2c0-1.65-1.35-3-3-3s-3 1.35-3 3v2c0 .552.448.993 1 .993M19 18h-6a1 1 0 1 1 0-2h6a1 1 0 1 1 0 2m0-4h-6a1 1 0 1 1 0-2h6a1 1 0 1 1 0 2m0-4h-6a1 1 0 1 1 0-2h6a1 1 0 1 1 0 2" />
                    <path fill="currentcolor"
                        d="M22 2H2C.9 2 0 2.9 0 4v16c0 1.1.9 2 2 2h20c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2m0 17.5c0 .28-.22.5-.5.5h-19c-.28 0-.5-.22-.5-.5v-15c0-.28.22-.5.5-.5h19c.28 0 .5.22.5.5z" />
                </svg>
                Attendance Settings
            </div>

            <div class="form-group">
                <label>Last Checkin</label>
                <div class="password-fields">
                    <div class="form-group">
                        <input type="date" id="checkindate" class="form-control" value="{{ $checkin_date }}" disabled>
                    </div>
                    <div class="form-group">
                        <input type="time" id="checkintime" class="form-control" value="{{ $checkin_time }}" disabled>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Last Checkout</label>
                <div class="password-fields">
                    <div class="form-group">
                        <input type="time" id="checkouttime" class="form-control" value="{{ $checkout_time }}" disabled>
                    </div>
                    <div class="form-group">
                        <input type="text" id="workinghours" class="form-control"
                            value="{{ $worked_today ? $worked_today . ' hours' : 'Not Checked out' }}" disabled>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title">
                <svg width="20" height="20" viewBox="0 0 1024 1024" class="icon">
                    <path d="M241.371 548.571a358.4 285.257 90 1 0 570.515 0 358.4 285.257 90 1 0-570.515 0" fill="none" />
                    <path
                        d="M58.514 526.629c-14.628 0-21.943-14.629-21.943-29.258 0-36.571 14.629-117.028 65.829-204.8 7.314-14.628 21.943-14.628 36.571-7.314s21.943 29.257 7.315 36.572C102.4 409.6 87.77 475.429 87.77 504.686c0 14.628-14.628 21.943-29.257 21.943m102.4-241.372c-7.314 0-14.628 0-14.628-7.314-14.629-7.314-14.629-29.257-7.315-36.572 51.2-65.828 117.029-117.028 182.858-153.6 14.628-7.314 29.257 0 36.571 14.629s0 29.257-14.629 36.571c-58.514 29.258-109.714 73.143-160.914 131.658 0 7.314-7.314 14.628-21.943 14.628M424.23 109.714c-14.629 0-21.943-7.314-29.258-21.943 0-14.628 7.315-29.257 21.943-36.571C460.8 36.571 512 36.571 563.2 36.571c21.943 7.315 29.257 21.943 29.257 36.572S577.83 102.4 563.2 102.4c-43.886-7.314-87.771-7.314-138.971 7.314q10.971 0 0 0m351.085 51.2c-7.314 0-7.314 0-14.628-7.314C716.8 124.343 665.6 109.714 614.4 95.086c-7.314 7.314-21.943-7.315-14.629-21.943 0-14.629 14.629-21.943 29.258-21.943 58.514 14.629 109.714 29.257 160.914 65.829 14.628 7.314 14.628 21.942 7.314 36.571 0 7.314-14.628 7.314-21.943 7.314m219.429 438.857c-14.629 0-29.257-14.628-29.257-29.257C958.17 409.6 906.97 285.257 819.2 204.8c-14.629-7.314-14.629-29.257 0-36.571 7.314-14.629 29.257-14.629 36.571 0 95.086 87.771 153.6 226.742 160.915 402.285 7.314 14.629-7.315 29.257-21.943 29.257M826.514 914.286H819.2c-14.629-7.315-21.943-21.943-14.629-36.572 0 0 14.629-43.885 36.572-131.657 21.943-87.771 7.314-204.8 7.314-204.8 0-124.343-73.143-241.371-190.171-292.571-14.629-7.315-21.943-21.943-14.629-36.572 7.314-14.628 21.943-21.943 36.572-14.628C811.886 256 899.657 387.657 899.657 533.943c0 0 14.629 124.343-7.314 219.428-21.943 95.086-43.886 138.972-43.886 138.972q-10.97 21.943-21.943 21.943M336.457 277.943c-7.314 0-14.628-7.314-21.943-14.629-7.314-7.314 0-21.943 7.315-29.257 80.457-58.514 175.542-80.457 270.628-58.514 14.629 0 21.943 14.628 21.943 29.257s-14.629 21.943-29.257 21.943c-80.457-14.629-160.914 0-226.743 51.2zM65.83 694.857c-14.629 0-21.943-7.314-29.258-21.943 0-14.628 7.315-29.257 21.943-29.257 0 0 29.257-7.314 58.515-36.571 21.942-21.943 43.885-58.515 51.2-73.143 0-80.457 29.257-160.914 80.457-226.743 7.314-14.629 29.257-21.943 36.571-7.314 14.629 7.314 14.629 21.943 7.314 36.571-43.885 58.514-73.142 124.343-73.142 197.486v7.314c0 7.314-21.943 73.143-65.829 102.4-43.886 36.572-80.457 51.2-80.457 51.2z"
                        fill="currentcolor" />
                    <path
                        d="M124.343 789.943c-7.314 0-14.629-7.314-21.943-14.629-7.314-14.628-7.314-29.257 7.314-36.571 0 0 95.086-65.829 124.343-87.772 7.314-7.314 29.257-43.885 36.572-80.457 0-58.514 36.571-160.914 73.142-204.8 0 0 14.629-21.943 51.2-43.885 29.258-14.629 65.829-29.258 102.4-29.258 14.629-7.314 29.258 0 29.258 14.629s-7.315 29.257-21.943 29.257c-29.257 0-51.2 7.314-73.143 21.943-29.257 21.943-43.886 36.571-43.886 36.571C358.4 431.543 321.83 526.63 321.83 577.83v7.314c-7.315 21.943-29.258 87.771-58.515 117.028-21.943 14.629-117.028 80.458-124.343 87.772zM702.17 438.857c-7.314 0-14.628 0-21.942-7.314-21.943-36.572-58.515-65.829-102.4-80.457-14.629-7.315-21.943-21.943-14.629-36.572 0-14.628 21.943-21.943 36.571-14.628 51.2 21.943 102.4 58.514 131.658 95.085 7.314 14.629 7.314 29.258-7.315 36.572-7.314 7.314-14.628 7.314-21.943 7.314M724.114 716.8s-7.314 0 0 0c-21.943-7.314-29.257-21.943-29.257-36.571 7.314-21.943 7.314-36.572 14.629-43.886 7.314-29.257 7.314-95.086 7.314-95.086 0-14.628 0-36.571-7.314-51.2 0-14.628 7.314-29.257 21.943-36.571 14.628-7.315 29.257 7.314 36.571 21.943 7.314 21.942 7.314 43.885 7.314 65.828 0 0-7.314 73.143-7.314 102.4 0 7.314-7.314 21.943-7.314 43.886-14.629 14.628-21.943 29.257-36.572 29.257M665.6 928.914h-7.314c-14.629-7.314-21.943-21.943-14.629-36.571 0 0 21.943-80.457 43.886-153.6 0-14.629 21.943-21.943 36.571-21.943s21.943 14.629 21.943 36.571c-14.628 73.143-43.886 153.6-43.886 153.6-14.628 14.629-21.942 21.943-36.571 21.943"
                        fill="currentcolor" />
                    <path
                        d="M548.571 936.229c-21.942-7.315-29.257-21.943-29.257-36.572l80.457-329.143c14.629-43.885-14.628-87.771-58.514-102.4-43.886-7.314-95.086 21.943-102.4 65.829l-73.143 212.114L219.43 863.086c-14.629 14.628-36.572 14.628-43.886 0s-7.314-29.257 7.314-36.572L321.83 716.8l65.828-190.171c0-36.572 29.257-73.143 58.514-87.772 29.258-21.943 73.143-29.257 109.715-14.628C629.03 438.857 680.23 512 658.287 592.457L577.829 921.6c0 7.314-14.629 14.629-29.258 14.629"
                        fill="currentcolor" />
                    <path
                        d="M387.657 943.543H373.03c-14.629-7.314-14.629-21.943-7.315-36.572l80.457-146.285 43.886-204.8c0-14.629 14.629-21.943 29.257-21.943s21.943 14.628 21.943 29.257l-43.886 219.429-87.771 153.6s-14.629 7.314-21.943 7.314"
                        fill="currentcolor" />
                </svg>
                Actions
            </div>
            <div class="form-group form-actions" style="justify-content: start;">
                <div class="form-group">
                    <button class="btn btn-secondary" id="checkin_button" {{ $has_attendance_today ? 'disabled' : '' }}>
                        <span class="btn-text">Checkin</span>
                        <span class="inspinner" style="margin-left: 8px;">
                            <svg class="spin" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                    stroke-dasharray="60" stroke-dashoffset="20"></circle>
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="form-group">
                    <button class="btn btn-secondary" id="checkout_button" {{ ($has_attendance_today && !$checkout_time) ? '' : 'disabled' }}>
                        <span class="btn-text">Checkout</span>
                        <span class="outspinner" style="margin-left: 8px;">
                            <svg class="spin" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                    stroke-dasharray="60" stroke-dashoffset="20"></circle>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        @if (!$has_attendance_today)
            const attended_today = false;
        @else
            const attended_today = true;
        @endif
        let atSite = false;
        const checkinbtn = document.getElementById('checkin_button');
        const checkoutbtn = document.getElementById('checkout_button');
        const backButton = document.getElementById('backButton');
        const checkinspinner = document.querySelector('.inspinner');
        const checkoutspinner = document.querySelector('.outspinner');
        const targetLat = 31.418517;
        const targetLon = 73.115623;
        let locationFound = false;
        let watcherId = null;

        document.addEventListener('DOMContentLoaded', function () {
            checkinbtn.disabled = true;
            checkinspinner.style.display = 'inline-block';

            checkoutspinner.style.display = 'none';

            backButton.addEventListener('click', function () {
                window.location.href = '/chat';
            });
            checkinbtn.addEventListener('click', function () {
                checkinbtn.disabled = true;
                checkinspinner.style.display = 'inline-block';
                checkin_user();
            });

            checkoutbtn.addEventListener('click', function () {
                checkoutbtn.disabled = true;
                checkoutspinner.style.display = 'inline-block';
                if(attended_today) {
                    checkout_user();
                } else {
                    showNotificationToast(2, 'User has not checked in today!');
                }
            });

            function checkin_user() {
                if (confirm('Are you sure you want to checkin!')) {
                    const formData = new FormData();
                    if (!atSite) {
                        showNotificationToast(2, 'Shift to the wifi or change the network!');
                        return;
                    } else {
                        formData.append('note', 'User Checkin through location verification!')
                    }
                    fetch('/user-check-in', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showNotificationToast(1, 'User Checked In Successfully', 2000);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 3000)
                            } else {
                                showNotificationToast(2, data.message, 4000);
                                checkinspinner.style.display = 'none';
                                if (data.redirect) {
                                    setTimeout(() => {
                                        window.location.href = data.redirect;
                                    }, 4000);
                                }
                            }
                        })
                        .catch(err => {
                            showNotificationToast(3, 'Error while checking in', 5000);
                            console.error(err);
                            checkinbtn.disabled = false;
                            checkinspinner.style.display = 'none';
                        });
                }
            }

            function checkout_user() {
                if (confirm('Are you sure you want to check out!')) {
                    const formData = new FormData();
                    formData.append('out_method', 'User Checkout');
                    fetch('/check-out-user', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(res=> res.json())
                    .then(data => {
                        if(data.success) {
                            showNotificationToast(1, 'User has checked out! Logging out now!');
                            setTimeout(()=>{
                                window.location.href = '/logout';
                            }, 3000);
                        } else {
                            showNotificationToast(2, data.message, 5000);
                            checkoutbtn.disabled = false;
                            checkoutspinner.style.display = 'none';
                            if(data.redirect){
                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 5000);
                            }
                        }
                    })
                    .catch(err => {
                        showNotificationToast(3, 'Error while checking out user!', 5000);
                        console.error(err);
                        checkoutbtn.disabled = false;
                        checkoutspinner.style.display = 'none';
                    });
                }
            }

            locateonmap();
        });
    </script>
    <script>

        function getDistanceInMeters(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat / 2) ** 2 +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) ** 2;
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function locateonmap() {
            if (navigator.geolocation && !attended_today) {
                showNotificationToast(1, "Searching you");
                const timeout = setTimeout(() => {
                    if (!locationFound) {
                        showNotificationToast(2, 'Shift to the wifi or change the network!');
                        navigator.geolocation.clearWatch(watcherId);
                        checkinspinner.style.display = 'none';
                    }
                }, 10000);

                watcherId = navigator.geolocation.watchPosition(
                    function (position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        const accuracy = position.coords.accuracy;
                        const distance = getDistanceInMeters(lat, lon, targetLat, targetLon);
                        atSite = (accuracy <= 100 && distance <= 100);

                        if (accuracy <= 100 && !locationFound) {
                            locationFound = true;
                            clearTimeout(timeout);
                            navigator.geolocation.clearWatch(watcherId);
                            if (!attended_today) {
                                showNotificationToast(1, 'You can checkin now!')
                                checkinbtn.disabled = false;
                                checkinspinner.style.display = 'none';
                            }

                            // console.log("✅ Accurate location found:");
                            // console.log("Latitude:", lat);
                            // console.log("Longitude:", lon);
                            // console.log("Accuracy:", accuracy.toFixed(2) + "m");
                            // console.log("Distance to site:", distance.toFixed(2) + "m");
                            // console.log("At site:", atSite);
                        }
                    },
                    function (error) {
                        console.error("Location error:", error.message);
                        clearTimeout(timeout);
                        showNotificationToast(3, 'Error getting location!', 5000);
                        navigator.geolocation.clearWatch(watcherId);
                    },
                    {
                        enableHighAccuracy: true,
                        maximumAge: 0,
                        timeout: 10000
                    }
                );
            } else if(attended_today) {
                showNotificationToast(2, 'User already checked in!');
                checkinspinner.style.display = 'none';
            } else {
                console.error("Geolocation not supported by this browser.");
                showNotificationToast(3, 'Geolocation not supported by this browser', 5000);
                checkinspinner.style.display = 'none';
            }
        }
    </script>
@endsection