@extends('layouts.app')

@section('title', 'Login')

@push('styles')
    <link href="{{ asset('css/auth/login.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="login-page d-flex flex-column align-items-center justify-content-center px-3 py-4">

    <div class="d-flex align-items-end mb-3 brand-container">
        <h1 class="brand-title font-alike mb-0">TobaKoko</h1>
        <svg class="brand-leaf" viewBox="0 0 80 78" aria-hidden="true">
            <g stroke="#2f3b2a" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round">
                <path d="M18 74 C2 50 6 16 24 3 C42 18 44 52 18 74 Z" fill="#8dc63f"/>
                <path d="M18 74 C20 52 22 30 24 3" fill="none"/>
                <path d="M30 70 C34 44 56 26 78 24 C76 52 54 76 30 70 Z" fill="#b5d94a"/>
                <path d="M30 70 C46 54 62 38 78 24" fill="none"/>
            </g>
        </svg>
    </div>

    {{-- login --}}
    <main class="login-card w-100 d-flex flex-column">
        <h2 class="login-heading font-alike text-center">Login</h2>
        <p class="login-sub text-center">Silahkan masukkan Username dan Password</p>

        <div class="quick-demo-box text-center mb-3">
            <span class="quick-demo-title">Pilih Akses Cepat:</span>
            <div class="d-flex justify-content-center gap-2 mt-1">
                <button type="button" class="btn-demo-pill" id="demoAdminBtn" title="Isi otomatis akun admin">
                    <i class="bi bi-shield-check"></i> Admin
                </button>
                <button type="button" class="btn-demo-pill" id="demoSorterBtn" title="Isi otomatis akun sorter">
                    <i class="bi bi-camera"></i> Sorter
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small rounded-4 shadow-sm animate-pop" role="alert">
                <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="d-flex flex-column flex-grow-1" id="loginForm" novalidate>
            @csrf

            <div class="field-wrapper mb-1">
                <label for="username" class="form-label ms-2 d-flex justify-content-between align-items-center">
                    <span>Username</span>
                    <span class="field-hint" id="usernameHint">Contoh: admin / sorter</span>
                </label>
                <div class="position-relative">
                    <input id="username" name="username" type="text"
                           class="form-control rounded-pill field @error('username') is-invalid @enderror"
                           value="{{ old('username') }}" autocomplete="username" required autofocus
                           placeholder="Ketik username...">
                    <i class="bi bi-person field-icon"></i>
                </div>
                @error('username')
                    <div class="invalid-feedback ms-2 d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-wrapper mt-2">
                <label for="password" class="form-label ms-2">Password</label>
                <div class="position-relative">
                    <input id="password" name="password" type="password"
                           class="form-control rounded-pill field pe-5 @error('password') is-invalid @enderror"
                           autocomplete="current-password" required
                           placeholder="Ketik password...">
                    <button type="button" id="togglePassword" class="btn btn-link toggle-eye"
                            aria-label="Tampilkan password" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger small ms-2 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" id="submitBtn" class="btn btn-masuk rounded-pill mx-auto mt-auto d-flex align-items-center justify-content-center gap-2">
                <span class="btn-text">Masuk</span>
                <i class="bi bi-arrow-right-short btn-icon"></i>
            </button>
        </form>
    </main>
</div>
@endsection

@push('scripts')
<script>
    const pwd = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');
    const usernameInput = document.getElementById('username');
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');

    // Toggle Password Visibility
    toggle.addEventListener('click', () => {
        const show = pwd.type === 'password';
        pwd.type = show ? 'text' : 'password';
        toggle.innerHTML = show ? '<i class="bi bi-eye-slash-fill"></i>' : '<i class="bi bi-eye"></i>';
        toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        pwd.focus();
    });

    // Quick fill helper
    function setQuickCredentials(user) {
        usernameInput.value = user;
        pwd.value = '123456'; // demo password placeholder

        // Visual dynamic feedback
        usernameInput.classList.add('field-highlight');
        pwd.classList.add('field-highlight');
        setTimeout(() => {
            usernameInput.classList.remove('field-highlight');
            pwd.classList.remove('field-highlight');
        }, 600);

        submitBtn.focus();
    }

    document.getElementById('demoAdminBtn')?.addEventListener('click', () => setQuickCredentials('admin'));
    document.getElementById('demoSorterBtn')?.addEventListener('click', () => setQuickCredentials('sorter'));

    // Loading indicator on form submit
    loginForm.addEventListener('submit', () => {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span>Memproses...</span>
        `;
    });
</script>
@endpush
