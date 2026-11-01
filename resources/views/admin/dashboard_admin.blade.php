@extends('layouts.admin')

@section('title', 'Riwayat Sortir Admin')

@push('styles')
    <link href="{{ asset('css/sorter/riwayat_sortir.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin/dashboard_admin.css') }}" rel="stylesheet">
@endpush

@section('admin_content')
<section class="admin-card">

    <div class="admin-overview-grid mb-3">
        <div class="admin-kpi-box">
            <span class="overview-label">Total Daun Terdata</span>
            <strong class="overview-val text-success" id="adminTotalLeaves">-</strong>
            <span class="overview-sub"><i class="bi bi-activity text-success me-1"></i>Semua Sorter</span>
        </div>
        <div class="admin-kpi-box">
            <span class="overview-label">Sorter Aktif</span>
            <strong class="overview-val">{{ $sorters->where('aktif', true)->count() }} <small class="text-muted fs-6">/ {{ $sorters->count() }}</small></strong>
            <span class="overview-sub"><i class="bi bi-people-fill text-primary me-1"></i>Tim Sortir</span>
        </div>
        <div class="admin-kpi-box">
            <span class="overview-label">Rata-rata/Hari</span>
            <strong class="overview-val" id="adminDailyAvg">-</strong>
            <span class="overview-sub"><i class="bi bi-graph-up-arrow text-success me-1"></i>Estimasi Harian</span>
        </div>
    </div>

    <div data-block="semua" class="chart-block-card">
        <div class="block-header d-flex flex-wrap align-items-center justify-content-between mb-2 gap-2">
            <span class="block-title">
                <i class="bi bi-bar-chart-fill text-success me-1" aria-hidden="true"></i>Total Seluruh Sorter
            </span>

            <div class="filter-row">
                <div class="date-filter d-flex align-items-center gap-1">
                    <button type="button" class="date-icon-btn js-date-icon" aria-label="Buka kalender tanggal mulai">
                        <i class="bi bi-calendar3 date-icon" aria-hidden="true"></i>
                    </button>
                    <label class="date-pill" title="Tanggal mulai">
                        <span class="js-start-label">Start</span>
                        <i class="bi bi-caret-down-fill ms-1" aria-hidden="true"></i>
                        <input type="date" class="js-start" aria-label="Tanggal mulai">
                    </label>
                    <span class="date-separator">-</span>
                    <label class="date-pill" title="Tanggal akhir">
                        <span class="js-end-label">End</span>
                        <i class="bi bi-caret-down-fill ms-1" aria-hidden="true"></i>
                        <input type="date" class="js-end" aria-label="Tanggal akhir">
                    </label>
                </div>
            </div>
        </div>

        <div class="preset-row mb-2">
            <span class="preset-label small text-muted"><i class="bi bi-funnel me-1"></i>Preset:</span>
            <button type="button" class="btn-preset active js-preset-all" data-days="7" aria-pressed="true">7 Hari</button>
            <button type="button" class="btn-preset js-preset-all" data-days="14" aria-pressed="false">14 Hari</button>
            <button type="button" class="btn-preset js-preset-all" data-days="30" aria-pressed="false">30 Hari</button>
        </div>

        <div class="chart-panel position-relative">
            <div class="chart-wrap">
                <canvas class="js-canvas" role="img" aria-label="Grafik keseluruhan sortir tembakau"></canvas>
            </div>
            <p class="chart-empty js-empty" hidden>Belum ada data pada rentang tanggal ini.</p>
        </div>
    </div>

    <div data-block="per-sorter" class="admin-section chart-block-card mt-3">

        <div class="block-header d-flex flex-wrap align-items-center justify-content-between mb-2 gap-2">
            <span class="block-title">
                <i class="bi bi-person-check-fill text-success me-1" aria-hidden="true"></i>Performa Individu
            </span>

            {{-- Pilih sorter --}}
            <div class="dropdown">
                <button class="pill-select" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Pilih sorter untuk dilihat grafiknya">
                    <span id="sorterLabel">{{ $sorters->first()['nama'] }}</span>
                    <i class="bi bi-caret-down-fill ms-1" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu pill-menu animate-pop">
                    @foreach ($sorters as $s)
                        <li>
                            <button type="button" class="dropdown-item js-pick-sorter d-flex align-items-center justify-content-between"
                                    data-id="{{ $s['id'] }}" data-nama="{{ $s['nama'] }}">
                                <span>{{ $s['nama'] }}</span>
                                @if($s['aktif'])
                                    <span class="status-dot is-online ms-2" aria-label="Aktif"></span>
                                @else
                                    <span class="status-dot ms-2" aria-label="Nonaktif"></span>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="chart-panel position-relative">
            <div class="chart-wrap">
                <canvas class="js-canvas" role="img" aria-label="Grafik perorangan sorter"></canvas>
            </div>
            <p class="chart-empty js-empty" hidden>Belum ada data pada rentang tanggal ini.</p>
        </div>

        <div class="bottom-row">
            <div class="date-filter d-flex align-items-center gap-1">
                <label class="date-pill" title="Tanggal mulai">
                    <span class="js-start-label">Start</span>
                    <i class="bi bi-caret-down-fill ms-1" aria-hidden="true"></i>
                    <input type="date" class="js-start" aria-label="Tanggal mulai perorangan">
                </label>
                <span class="date-separator">-</span>
                <label class="date-pill" title="Tanggal akhir">
                    <span class="js-end-label">End</span>
                    <i class="bi bi-caret-down-fill ms-1" aria-hidden="true"></i>
                    <input type="date" class="js-end" aria-label="Tanggal akhir perorangan">
                </label>
            </div>

            <div class="dropdown dropup">
                <button class="pill-select btn-download-accent" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Unduh data riwayat sortir">
                    <i class="bi bi-download me-1" aria-hidden="true"></i> Unduh Data
                </button>
                <ul class="dropdown-menu dropdown-menu-end pill-menu animate-pop">
                    <li class="dropdown-header small text-muted">Unduh CSV Sorter:</li>
                    @foreach ($sorters as $s)
                        <li>
                            <button type="button" class="dropdown-item js-unduh" data-sorter="{{ $s['id'] }}">
                                <i class="bi bi-file-earmark-spreadsheet me-1" aria-hidden="true"></i> {{ $s['nama'] }}
                            </button>
                        </li>
                    @endforeach
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <button type="button" class="dropdown-item js-unduh fw-bold text-success" data-sorter="all">
                            <i class="bi bi-file-earmark-zip me-1" aria-hidden="true"></i> SEMUA SORTER (ALL)
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const DATA_URL     = @json(route('admin.riwayat.data'));
    const DOWNLOAD_URL = @json(route('admin.riwayat.unduh'));

    const adminTotal = document.getElementById('adminTotalLeaves');
    const adminAvg   = document.getElementById('adminDailyAvg');

    const fmt = (v) => new Date(v + 'T00:00:00')
        .toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });

    function makeChart(canvas, isAll = false) {
        const ctx = canvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        if (isAll) {
            gradient.addColorStop(0, '#8dc63f');
            gradient.addColorStop(1, '#346d3a');
        } else {
            gradient.addColorStop(0, '#a5d6a7');
            gradient.addColorStop(1, '#43a047');
        }

        return new Chart(canvas, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Jumlah daun',
                    data: [],
                    backgroundColor: gradient,
                    hoverBackgroundColor: '#c8e6c9',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 38,
                    categoryPercentage: 1,
                    barPercentage: 0.82,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 600,
                    easing: 'easeOutQuart',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(28, 42, 26, 0.95)',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            title: (items) => `📅 Tanggal: ${items[0].label}`,
                            label: (item) => `🍃 Disortir: ${item.parsed.y} Daun`,
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { color: 'rgba(0,0,0,0.15)', width: 1 },
                        ticks: {
                            display: true,
                            color: '#526550',
                            font: { size: 10 },
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        ticks: { color: '#7a8e78', font: { size: 10 } }
                    },
                },
            },
        });
    }

    function initBlock(block, getSorter, isAll = false) {
        const start   = block.querySelector('.js-start');
        const end     = block.querySelector('.js-end');
        const sLbl    = block.querySelector('.js-start-label');
        const eLbl    = block.querySelector('.js-end-label');
        const empty   = block.querySelector('.js-empty');
        const iconBtn = block.querySelector('.js-date-icon');
        const chart   = makeChart(block.querySelector('.js-canvas'), isAll);

        async function load() {
            const p = new URLSearchParams({ sorter: getSorter() });
            if (start.value) p.set('start', start.value);
            if (end.value)   p.set('end', end.value);

            try {
                const res = await fetch(`${DATA_URL}?${p}`, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error(res.status);
                const json = await res.json();

                chart.data.labels = json.labels;
                chart.data.datasets[0].data = json.values;
                chart.update();
                empty.textContent = 'Belum ada data pada rentang tanggal ini.';
                empty.hidden = json.values.length > 0;

                if (isAll && json.values && json.values.length > 0) {
                    const sum = json.values.reduce((a, b) => a + b, 0);
                    const avg = Math.round(sum / json.values.length);
                    if (adminTotal) adminTotal.textContent = sum.toLocaleString('id-ID');
                    if (adminAvg)   adminAvg.textContent   = avg.toLocaleString('id-ID');
                }
            } catch (err) {
                console.error('Gagal memuat data:', err);
                empty.textContent = 'Data gagal dimuat.';
                empty.hidden = false;
            }
        }

        function onChange() {
            if (start.value && end.value && start.value > end.value) {
                [start.value, end.value] = [end.value, start.value];
            }
            sLbl.textContent = start.value ? fmt(start.value) : 'Start';
            eLbl.textContent = end.value   ? fmt(end.value)   : 'End';
            load();
        }

        start.addEventListener('change', onChange);
        end.addEventListener('change', onChange);

        iconBtn?.addEventListener('click', () => {
            start.showPicker?.() ?? start.focus();
        });

        const today = new Date();
        const prev = new Date();
        prev.setDate(today.getDate() - 6);
        const toIso = (d) => d.toISOString().split('T')[0];
        start.value = toIso(prev);
        end.value   = toIso(today);
        sLbl.textContent = fmt(start.value);
        eLbl.textContent = fmt(end.value);

        load();
        return {
            load,
            setDates: (s, e) => {
                start.value = s;
                end.value   = e;
                sLbl.textContent = fmt(s);
                eLbl.textContent = fmt(e);
                load();
            },
            range: () => ({ start: start.value, end: end.value })
        };
    }

    const semuaBlock = initBlock(document.querySelector('[data-block="semua"]'), () => 'all', true);

    document.querySelectorAll('.js-preset-all').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.js-preset-all').forEach((b) => {
                const on = b === btn;
                b.classList.toggle('active', on);
                b.setAttribute('aria-pressed', on);
            });
            const days = parseInt(btn.dataset.days, 10);
            const today = new Date();
            const prev = new Date();
            prev.setDate(today.getDate() - (days - 1));
            const toIso = (d) => d.toISOString().split('T')[0];
            semuaBlock.setDates(toIso(prev), toIso(today));
        });
    });

    let currentSorter = @json($sorters->first()['id']);
    const perSorter = initBlock(document.querySelector('[data-block="per-sorter"]'), () => currentSorter, false);

    document.querySelectorAll('.js-pick-sorter').forEach((btn) => {
        btn.addEventListener('click', () => {
            currentSorter = btn.dataset.id;
            document.getElementById('sorterLabel').textContent = btn.dataset.nama;
            perSorter.load();
        });
    });

    document.querySelectorAll('.js-unduh').forEach((btn) => {
        btn.addEventListener('click', () => {
            const { start, end } = perSorter.range();
            const p = new URLSearchParams({ sorter: btn.dataset.sorter });
            if (start) p.set('start', start);
            if (end)   p.set('end', end);

            window.showToast?.('Menyiapkan dan mengunduh berkas CSV...');
            window.location.href = `${DOWNLOAD_URL}?${p}`;
        });
    });
</script>
@endpush
