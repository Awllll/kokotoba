@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/sorter/layouts.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin/layouts.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="sorter-navbar d-flex align-items-center justify-content-between px-3 px-md-4">

    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-end text-decoration-none text-dark brand-link" aria-label="Beranda Admin TobaKoko">
        <span class="nav-brand font-alike">TobaKoko</span>
        <svg class="nav-leaf" viewBox="0 0 80 78" aria-hidden="true">
            <g stroke="#2f3b2a" stroke-width="3" stroke-linejoin="round" stroke-linecap="round">
                <path d="M18 74 C2 50 6 16 24 3 C42 18 44 52 18 74 Z" fill="#8dc63f"/>
                <path d="M18 74 C20 52 22 30 24 3" fill="none"/>
                <path d="M30 70 C34 44 56 26 78 24 C76 52 54 76 30 70 Z" fill="#b5d94a"/>
                <path d="M30 70 C46 54 62 38 78 24" fill="none"/>
            </g>
        </svg>
    </a>

    <div class="d-flex align-items-center gap-2">

        <nav class="d-none d-md-flex align-items-center gap-1" aria-label="Menu admin">
            <a class="nav-pill {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}"
               @if(request()->routeIs('admin.dashboard*')) aria-current="page" @endif>
                <i class="bi bi-bar-chart me-1"></i>Riwayat Sortir
            </a>
            <a class="nav-pill {{ request()->routeIs('admin.manajemen*') ? 'active' : '' }}"
               href="{{ route('admin.manajemen') }}"
               @if(request()->routeIs('admin.manajemen*')) aria-current="page" @endif>
                <i class="bi bi-people me-1"></i>Manajemen Sortir
            </a>
        </nav>

        <div class="nav-role-badge d-none d-sm-inline-flex align-items-center gap-2" aria-label="Peran: Admin">
            <span class="status-dot is-online" aria-hidden="true"></span>
            <span class="role-text">Admin</span>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="d-none d-md-block">
            @csrf
            <button type="submit" class="nav-pill nav-pill-danger border-0 bg-transparent" aria-label="Keluar dari akun admin">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>

        <div class="dropdown d-md-none">
            <button class="btn nav-dots" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false" aria-label="Buka menu navigasi">
                <i class="bi bi-three-dots-vertical"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end sorter-menu animate-pop">
                <li>
                    <a class="dropdown-item {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-bar-chart me-2"></i>Riwayat Sortir
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ request()->routeIs('admin.manajemen*') ? 'active' : '' }}"
                       href="{{ route('admin.manajemen') }}">
                        <i class="bi bi-people me-2"></i>Manajemen Sortir
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<main class="sorter-body">
    @yield('admin_content')
</main>

<div id="tobaToast" class="toba-toast" role="status" aria-live="polite">
    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
    <span id="tobaToastMsg">Berhasil</span>
</div>

<script>
    window.showToast = (msg) => {
        const t = document.getElementById('tobaToast');
        const m = document.getElementById('tobaToastMsg');
        if (!t || !m) return;
        m.textContent = msg;
        t.classList.add('show');
        clearTimeout(window.__toastTimer);
        window.__toastTimer = setTimeout(() => t.classList.remove('show'), 3500);
    };

    document.addEventListener('submit', (e) => {
        const b = e.target.querySelector('[type="submit"]');
        if (b) setTimeout(() => { b.disabled = true; }, 0);
    });
</script>
@endsection
