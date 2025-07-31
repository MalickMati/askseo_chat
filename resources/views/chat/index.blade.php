@extends('layouts.app')

@section('content')
    <x-chat_sidebar :currentUser=$currentUser :users=$allusers :groups=$allgroups></x-chat_sidebar>
    <x-chat_area></x-chat_area>
    <x-chat_sidebar_menu></x-chat_sidebar_menu>
    <x-chat_menu></x-chat_menu>
    <x-add_user_in_group_modal></x-add_user_in_group_modal>
    <x-group_members_modal></x-group_members_modal>

    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>
@endsection

@if (session()->has('user_type') && (session('user_type') === 'admin' || session('user_type') === 'moderator') || session('user_type') === 'super_admin')
    <script>
        const isAdmin = true;
    </script>

@else
    <script>
        const isAdmin = false;
    </script>
@endif

@section('js')
    <script>
        // Global variables
        let currentUserId = {{ $currentUser['id'] }};
        let usersMap = {
            @foreach ($allusers as $user)
                {{ $user['id'] }}: "{{ addslashes($user['username']) }}",
            @endforeach
        {{ $currentUser['id'] }}: "{{ addslashes($currentUser['username']) }}"
    };
    const icon = "{{ asset('favicon.ico') }}";

    </script>

    <script src="{{ asset('js/script.js') }}"></script>

    @if(session('error'))
        <script>
            showNotificationToast(2, "{{ session('error') }}", 10000);
        </script>
    @endif

@endsection