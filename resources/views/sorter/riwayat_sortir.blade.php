@extends('layouts.sorter')

@section('title', 'Riwayat Sortir')

@push('styles')
    <link href="{{ asset('css/sorter/riwayat_sortir.css') }}" rel="stylesheet">
@endpush

@section('sorter_content')
<section class="riwayat-card">
    
    <div class="riwayat-top">
        <span class="riwayat-pill">
            <span class="status-dot is-online me-1"></span> Sorter 1
        </span>

        <div class="date-filter">
            <i class="bi bi-calendar3 date-icon" aria-hidden="true" title="Pilih rentang tanggal"></i>

            <label class="date-pill" title="Tanggal mulai">
                <span id="startLabel">Start</span>
                <i class="bi bi-caret-down-fill"></i>
                <input type="date" id="startDate" aria-label="Tanggal mulai">
            </label>

            <span class="date-separator">-</span>

            <label class="date-pill" title="Tanggal akhir">
                <span id="endLabel">End</span>
                <i class="bi bi-caret-down-fill"></i>
                <input type="date" id="endDate" aria-label="Tanggal akhir">
            </label>
        </div>
    </div>

    <div class="quick-presets-bar d-flex align-items-center gap-1 my-2">
        <span class="preset-label me-1"><i class="bi bi-funnel"></i> Rentang:</span>
        <button type="button" class="btn-preset active" data-days="7">7 Hari</button>
        <button type="button" class="btn-preset" data-days="14">14 Hari</button>
        <button type="button" class="btn-preset" data-days="30">30 Hari</button>
    </div>

    <div class="kpi-stats-grid mb-3">
        <div class="kpi-card">
            <div class="kpi-icon"><i class="bi bi-layers-fill"></i></div>
            <div>
                <span class="kpi-label">Total Daun</span>
                <strong class="kpi-val" id="kpiTotal">-</strong>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon"><i class="bi bi-graph-up"></i></div>
            <div>
                <span class="kpi-label">Rata-rata/Hari</span>
                <strong class="kpi-val" id="kpiAvg">-</strong>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon"><i class="bi bi-trophy-fill"></i></div>
            <div>
                <span class="kpi-label">Tertinggi</span>
                <strong class="kpi-val" id="kpiPeak">-</strong>
            </div>
        </div>
    </div>

    <div class="chart-panel position-relative">
        <div class="chart-loader" id="chartLoader" style="display: none;">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Memuat data...</span>
            </div>
            <span class="small mt-2 text-muted">Memuat grafik...</span>
        </div>

        <div class="chart-wrap">
            <canvas id="riwayatChart" aria-label="Grafik riwayat sortir" role="img"></canvas>
        </div>
        <p class="chart-empty" id="chartEmpty" hidden>Belum ada data pada rentang tanggal ini.</p>
    </div>

    <p class="riwayat-footer">
        <span class="footer-brand font-alike">TobaKoko</span> • Sistem Sortir Cerdas
    </p>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const DATA_URL = @json(route('sorter.riwayat.data'));

    const startInput = document.getElementById('startDate');
    const endInput   = document.getElementById('endDate');
    const startLabel = document.getElementById('startLabel');
    const endLabel   = document.getElementById('endLabel');
    const emptyMsg   = document.getElementById('chartEmpty');
    const loader     = document.getElementById('chartLoader');
    const kpiTotal   = document.getElementById('kpiTotal');
    const kpiAvg     = document.getElementById('kpiAvg');
    const kpiPeak    = document.getElementById('kpiPeak');
    const canvas     = document.getElementById('riwayatChart');

    const ctx = canvas.getContext('2d');
    let gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, '#8dc63f');
    gradient.addColorStop(1, '#3f7a45');

    const chart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Jumlah daun',
                data: [],
                backgroundColor: gradient,
                hoverBackgroundColor: '#a2db53',
                borderRadius: 8,
                borderSkipped: false,
                maxBarThickness: 42,
                categoryPercentage: 1,
                barPercentage: 0.8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 650,
                easing: 'easeOutQuart',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(28, 42, 26, 0.95)',
                    padding: 12,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        title: (items) => `📅 ${items[0].label}`,
                        label: (item) => `🍃 ${item.parsed.y} Daun Tembakau`,
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { color: 'rgba(47, 59, 42, 0.25)', width: 1.5 },
                    ticks: {
                        display: true,
                        color: '#445642',
                        font: { size: 10, weight: '500' },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 7
                    },
                },
                y: {
                    display: true,
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.04)' },
                    border: { display: false },
                    ticks: {
                        color: '#6e7f6c',
                        font: { size: 10 },
                        precision: 0
                    }
                },
            },
        },
    });

    const fmt = (v) => new Date(v + 'T00:00:00')
        .toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });

    function updateKPIs(values) {
        if (!values || values.length === 0) {
            kpiTotal.textContent = '0';
            kpiAvg.textContent   = '0';
            kpiPeak.textContent  = '0';
            return;
        }
        const total = values.reduce((a, b) => a + b, 0);
        const avg   = Math.round(total / values.length);
        const peak  = Math.max(...values);

        animateCounter(kpiTotal, total);
        animateCounter(kpiAvg, avg);
        animateCounter(kpiPeak, peak);
    }

    function animateCounter(elem, target) {
        let current = 0;
        const step = Math.max(1, Math.ceil(target / 20));
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                elem.textContent = target.toLocaleString('id-ID');
                clearInterval(timer);
            } else {
                elem.textContent = current.toLocaleString('id-ID');
            }
        }, 18);
    }

    async function loadChart() {
        const params = new URLSearchParams();
        if (startInput.value) params.set('start', startInput.value);
        if (endInput.value)   params.set('end', endInput.value);

        if (loader) loader.style.display = 'flex';

        try {
            const res = await fetch(`${DATA_URL}?${params}`, {
                headers: { 'Accept': 'application/json' },
            });
            if (!res.ok) throw new Error(res.status);
            const json = await res.json();

            chart.data.labels = json.labels;
            chart.data.datasets[0].data = json.values;
            chart.update();

            updateKPIs(json.values);

            emptyMsg.hidden = json.values && json.values.length > 0;
        } catch (e) {
            console.error('Gagal memuat chart:', e);
            emptyMsg.textContent = 'Gagal memuat data grafik.';
            emptyMsg.hidden = false;
        } finally {
            if (loader) loader.style.display = 'none';
        }
    }

    function onDateChange() {
        if (startInput.value && endInput.value && startInput.value > endInput.value) {
            [startInput.value, endInput.value] = [endInput.value, startInput.value];
        }
        startLabel.textContent = startInput.value ? fmt(startInput.value) : 'Start';
        endLabel.textContent   = endInput.value   ? fmt(endInput.value)   : 'End';
        document.querySelectorAll('.btn-preset').forEach(b => b.classList.remove('active'));

        loadChart();
    }

    startInput.addEventListener('change', onDateChange);
    endInput.addEventListener('change', onDateChange);

    function setPreset(days) {
        const end = new Date();
        const start = new Date();
        start.setDate(end.getDate() - (days - 1));

        const toIso = (d) => d.toISOString().split('T')[0];
        startInput.value = toIso(start);
        endInput.value   = toIso(end);

        startLabel.textContent = fmt(startInput.value);
        endLabel.textContent   = fmt(endInput.value);

        loadChart();
    }

    document.querySelectorAll('.btn-preset').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.btn-preset').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            setPreset(parseInt(btn.dataset.days, 10));
        });
    });

    document.querySelector('.date-icon')?.addEventListener('click', () => {
        startInput.showPicker?.() ?? startInput.focus();
    });

    setPreset(7);
</script>
@endpush
