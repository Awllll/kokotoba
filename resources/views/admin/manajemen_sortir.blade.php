@extends('layouts.admin')

@section('title', 'Manajemen Sortir')

@push('styles')
    <link href="{{ asset('css/admin/manajemen_sortir.css') }}" rel="stylesheet">
@endpush

@section('admin_content')
<section class="admin-card">

    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
        <div class="min-w-0">
            <h1 class="page-card-title font-alike mb-0">Manajemen Sortir</h1>
            <p class="page-card-sub mb-0">Kelola akun dan hak akses petugas sortir</p>
        </div>
        <button type="button" class="btn-add-top flex-shrink-0" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah akun
        </button>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2 px-3 small rounded-4 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
        </div>
    @endif

    <div class="manage-toolbar mb-3">
        <div class="position-relative search-input-box mb-2">
            <i class="bi bi-search search-icon" aria-hidden="true"></i>
            <input type="search" id="searchSorter" class="search-input"
                   placeholder="Cari nama atau username..." autocomplete="off"
                   aria-label="Cari sorter">
            <button type="button" id="clearSearch" class="btn-clear-search" hidden aria-label="Hapus pencarian">
                <i class="bi bi-x-circle-fill"></i>
            </button>
        </div>

        <div class="filter-tabs d-flex flex-wrap align-items-center gap-2" role="group" aria-label="Filter status">
            <button type="button" class="filter-tab active" data-filter="all" aria-pressed="true">
                Semua <span class="tab-badge">{{ $sorters->count() }}</span>
            </button>
            <button type="button" class="filter-tab" data-filter="aktif" aria-pressed="false">
                Aktif <span class="tab-badge">{{ $sorters->where('aktif', true)->count() }}</span>
            </button>
            <button type="button" class="filter-tab" data-filter="nonaktif" aria-pressed="false">
                Nonaktif <span class="tab-badge">{{ $sorters->where('aktif', false)->count() }}</span>
            </button>
        </div>
    </div>

    <div class="manage-list" id="sorterList">
        @foreach ($sorters as $s)
            <div class="manage-row"
                 data-status="{{ $s['aktif'] ? 'aktif' : 'nonaktif' }}"
                 data-nama="{{ strtolower($s['nama']) }}"
                 data-username="{{ strtolower($s['username']) }}">

                <div class="d-flex align-items-center gap-2 min-w-0">
                    <div class="sorter-avatar {{ $s['aktif'] ? 'is-active' : '' }}" aria-hidden="true">
                        {{ strtoupper(substr($s['nama'], 0, 1)) }}{{ substr($s['nama'], -1) }}
                    </div>
                    <div class="min-w-0">
                        <span class="manage-name d-block">{{ $s['nama'] }}</span>
                        <span class="sorter-uname d-block">&#64;{{ $s['username'] }}</span>
                    </div>
                </div>

                <div class="manage-actions">
                    <button type="button" class="mini-pill js-edit"
                            data-url="{{ route('admin.sorter.update', $s['id']) }}"
                            data-username="{{ $s['username'] }}"
                            aria-label="Edit akun {{ $s['nama'] }}">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>

                    {{-- Satu ketukan membuka konfirmasi; label menunjukkan status sekarang --}}
                    <button type="button" class="mini-pill js-status {{ $s['aktif'] ? '' : 'is-off' }}"
                            data-url="{{ route('admin.sorter.status', $s['id']) }}"
                            data-status="{{ $s['aktif'] ? 'nonaktif' : 'aktif' }}"
                            data-nama="{{ $s['nama'] }}"
                            aria-label="{{ $s['aktif'] ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $s['nama'] }}">
                        <span class="status-dot {{ $s['aktif'] ? 'is-online' : '' }}"></span>
                        {{ $s['aktif'] ? 'Aktif' : 'Nonaktif' }}
                    </button>
                </div>
            </div>
        @endforeach

        <div class="empty-search-state text-center py-4" id="emptySearch" hidden>
            <i class="bi bi-search text-muted fs-3 mb-2 d-block"></i>
            <p class="text-muted small mb-0">Tidak ada sorter yang cocok dengan kata kunci atau filter.</p>
        </div>
    </div>
</section>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="editForm" class="modal-content toba-modal">
            @csrf
            @method('PUT')
            <h2 class="toba-modal-title" id="editTitle">Edit akun sorter</h2>

            <label for="editUsername" class="toba-label">Username</label>
            <input id="editUsername" name="username" type="text" class="toba-input"
                   autocomplete="off" required>

            <label for="editPassword" class="toba-label">Password</label>
            <div class="position-relative">
                <input id="editPassword" name="password" type="password" class="toba-input pe-5"
                       placeholder="Kosongkan jika tidak diubah" autocomplete="new-password">
                <button type="button" class="btn btn-link modal-pwd-toggle" data-target="editPassword"
                        aria-label="Tampilkan password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>

            <div class="toba-actions">
                <button type="button" class="btn-toba btn-toba-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn-toba btn-toba-primary">Simpan perubahan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.sorter.store') }}" class="modal-content toba-modal">
            @csrf
            <input type="hidden" name="form" value="add">
            <h2 class="toba-modal-title" id="addTitle">Tambah akun sorter</h2>

            <label for="addUsername" class="toba-label">Username</label>
            <input id="addUsername" name="username" type="text" class="toba-input"
                   value="{{ old('form') === 'add' ? old('username') : '' }}"
                   placeholder="Contoh: sorter5" autocomplete="off" required>

            <label for="addPassword" class="toba-label">Password</label>
            <div class="position-relative">
                <input id="addPassword" name="password" type="password" class="toba-input pe-5"
                       placeholder="Masukkan password" autocomplete="new-password" required>
                <button type="button" class="btn btn-link modal-pwd-toggle" data-target="addPassword"
                        aria-label="Tampilkan password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>

            <div class="toba-actions">
                <button type="button" class="btn-toba btn-toba-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn-toba btn-toba-primary">Simpan akun</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form method="POST" id="statusForm" class="modal-content toba-modal toba-confirm">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" id="statusInput">

            <div class="mb-2 text-center" id="confirmIcon" aria-hidden="true"></div>
            <p class="confirm-title" id="confirmTitle">Apakah Anda yakin?</p>
            <p class="confirm-text text-center" id="confirmText"></p>

            <div class="toba-actions justify-content-center">
                <button type="button" class="btn-toba btn-toba-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn-toba btn-toba-primary" id="confirmSubmitBtn">Ya, lanjutkan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    @if (session('success'))
        document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success'))));
    @endif

    const editModal    = new bootstrap.Modal(document.getElementById('editModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

    @if ($errors->any() && old('form') === 'add')
        new bootstrap.Modal(document.getElementById('addModal')).show();
    @endif

    document.querySelectorAll('.js-edit').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.getElementById('editForm').action = btn.dataset.url;
            document.getElementById('editUsername').value = btn.dataset.username;
            document.getElementById('editPassword').value = '';
            editModal.show();
        });
    });

    document.querySelectorAll('.js-status').forEach((btn) => {
        btn.addEventListener('click', () => {
            const aktif  = btn.dataset.status === 'aktif';
            const submit = document.getElementById('confirmSubmitBtn');

            document.getElementById('statusForm').action = btn.dataset.url;
            document.getElementById('statusInput').value = btn.dataset.status;

            document.getElementById('confirmIcon').innerHTML = aktif
                ? '<i class="bi bi-check-circle-fill text-success fs-1"></i>'
                : '<i class="bi bi-exclamation-triangle-fill text-danger fs-1"></i>';

            document.getElementById('confirmText').textContent =
                `Akun "${btn.dataset.nama}" akan ${aktif ? 'diaktifkan kembali' : 'dinonaktifkan'}.`;

            submit.className   = 'btn-toba ' + (aktif ? 'btn-toba-primary' : 'btn-toba-danger');
            submit.textContent = aktif ? 'Ya, aktifkan' : 'Ya, nonaktifkan';
            submit.disabled    = false;

            confirmModal.show();
        });
    });

    const searchInput = document.getElementById('searchSorter');
    const clearBtn    = document.getElementById('clearSearch');
    const rows        = document.querySelectorAll('.manage-row');
    const emptyState  = document.getElementById('emptySearch');
    const filterTabs  = document.querySelectorAll('.filter-tab');
    let currentFilter = 'all';

    function filterRows() {
        const query = searchInput.value.toLowerCase().trim();
        clearBtn.hidden = !query;

        let visible = 0;
        rows.forEach((row) => {
            const okFilter = currentFilter === 'all' || row.dataset.status === currentFilter;
            const okQuery  = !query || row.dataset.nama.includes(query) || row.dataset.username.includes(query);
            row.hidden = !(okFilter && okQuery);
            if (!row.hidden) visible++;
        });
        emptyState.hidden = visible > 0;
    }

    searchInput.addEventListener('input', filterRows);
    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        searchInput.focus();
        filterRows();
    });

    filterTabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            filterTabs.forEach((t) => {
                const on = t === tab;
                t.classList.toggle('active', on);
                t.setAttribute('aria-pressed', on);
            });
            currentFilter = tab.dataset.filter;
            filterRows();
        });
    });

    document.querySelectorAll('.modal-pwd-toggle').forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.target);
            if (!target) return;
            const show = target.type === 'password';
            target.type = show ? 'text' : 'password';
            btn.innerHTML = show ? '<i class="bi bi-eye-slash-fill"></i>' : '<i class="bi bi-eye"></i>';
            btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });

    filterRows();
</script>
@endpush
