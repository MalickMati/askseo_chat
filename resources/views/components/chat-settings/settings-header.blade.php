<div class="settings-header">
    <button class="back-button" id="backButton">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
        Back
    </button>
    <div class="logo">
        <img src="{{ asset('assets/images/logo.png') }}" alt="" style="height:25px;">
        ASKSEO
    </div>
    <h1>{{ $heading }}</h1>
    <p>{{ $message }}</p>
</div>