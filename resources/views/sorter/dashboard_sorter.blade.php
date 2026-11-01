@extends('layouts.sorter')

@section('title', 'Deteksi Daun')

@push('styles')
    <link href="{{ asset('css/sorter/dashboard_sorter.css') }}" rel="stylesheet">
@endpush

@section('sorter_content')
<section class="sorter-card">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="sorter-pill mb-0">
            <span class="status-dot is-online me-1"></span> Sorter 1
        </span>
        <span class="badge-status-mode">
            <i class="bi bi-broadcast me-1 text-success"></i> Sistem Siap
        </span>
    </div>

    <div class="detect-panel" id="detectDropZone">
        <div class="scan-overlay" id="scanOverlay" style="display: none;">
            <div class="scan-laser"></div>
            <div class="scan-badge">
                <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status"></span>
                <span>Memindai struktur daun tembakau...</span>
            </div>
        </div>

        <i class="bi bi-camera detect-icon" id="detectIcon"></i>
        <img id="detectPreview" class="detect-preview" alt="Pratinjau foto daun">

        <h2 class="detect-title font-alike" id="detectTitle">Deteksi Daun</h2>
        <p class="detect-sub" id="detectSub">
            Foto daun Tembakau<br>yang ingin anda deteksi
        </p>

        <div class="detect-result-box" id="detectResultBox" style="display: none;">
            <div class="result-header d-flex align-items-center justify-content-between">
                <span class="result-badge-grade" id="resGrade">Grade A • Matang Optimal</span>
                <span class="result-confidence" id="resAcc">96% Cocok</span>
            </div>

            <div class="progress-bar-wrap mt-2">
                <div class="progress-bar-fill" id="resProgress" style="width: 96%;"></div>
            </div>

            <div class="result-details-grid mt-3">
                <div class="detail-item">
                    <span class="detail-label">Warna Daun</span>
                    <strong class="detail-val" id="resColor">Kuning Keemasan</strong>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Kadar Air</span>
                    <strong class="detail-val" id="resMoisture">14% (Ideal)</strong>
                </div>
            </div>

            <div class="result-actions d-flex gap-2 mt-3">
                <button type="button" class="btn-res-action btn-res-retake" id="retakeBtn">
                    <i class="bi bi-arrow-repeat"></i> Foto Ulang
                </button>
                <button type="button" class="btn-res-action btn-res-save" id="saveResultBtn">
                    <i class="bi bi-check2-circle"></i> Simpan Hasil
                </button>
            </div>
        </div>

        <input type="file" id="cameraInput" accept="image/*" capture="environment" hidden>

        <div class="shutter-wrapper" id="shutterWrapper">
            <button type="button" id="shutterBtn" class="shutter" aria-label="Ambil foto" title="Tekan untuk mengambil foto daun"></button>
            <span class="shutter-hint">Tekan Shutter</span>
        </div>

        <button type="button" class="btn-demo-leaf mt-3" id="sampleLeafBtn">
            <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Gunakan Contoh Foto Daun
        </button>
    </div>

    <ol class="detect-steps">
        <li>Arahkan kamera ke daun yang ingin Anda deteksi.</li>
        <li>Pastikan daun mendapat pencahayaan yang cukup agar terlihat dengan jelas.</li>
        <li>Hindari foto yang blur atau goyang dengan menjaga kamera tetap stabil saat mengambil gambar.</li>
    </ol>
</section>
@endsection

@push('scripts')
<script>
    const input         = document.getElementById('cameraInput');
    const preview       = document.getElementById('detectPreview');
    const icon          = document.getElementById('detectIcon');
    const shutterBtn    = document.getElementById('shutterBtn');
    const shutterWrap   = document.getElementById('shutterWrapper');
    const scanOverlay   = document.getElementById('scanOverlay');
    const resultBox     = document.getElementById('detectResultBox');
    const detectTitle   = document.getElementById('detectTitle');
    const detectSub     = document.getElementById('detectSub');
    const sampleBtn     = document.getElementById('sampleLeafBtn');
    const retakeBtn     = document.getElementById('retakeBtn');
    const saveResultBtn = document.getElementById('saveResultBtn');
    const dropZone      = document.getElementById('detectDropZone');

    shutterBtn.addEventListener('click', () => input.click());
    retakeBtn?.addEventListener('click', () => input.click());

    ['dragenter', 'dragover'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropZone.classList.add('is-dragover');
        });
    });
    ['dragleave', 'drop'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropZone.classList.remove('is-dragover');
        });
    });
    dropZone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files && files[0] && files[0].type.startsWith('image/')) {
            processFile(files[0]);
        }
    });

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) return;
        processFile(file);
    });

    function processFile(file) {
        preview.src = URL.createObjectURL(file);
        showScanningState();
    }

    sampleBtn?.addEventListener('click', () => {
        const sampleSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300"><defs><radialGradient id="leafGrad" cx="50%" cy="40%" r="60%"><stop offset="0%" stop-color="%23d4e157"/><stop offset="50%" stop-color="%239ccc65"/><stop offset="100%" stop-color="%23558b2f"/></radialGradient></defs><rect width="100%" height="100%" fill="%232e3b2b"/><path d="M200 30 C120 70 80 180 200 270 C320 180 280 70 200 30 Z" fill="url(%23leafGrad)" stroke="%2333691e" stroke-width="4"/><path d="M200 30 Q200 150 200 270" stroke="%23f9fbe7" stroke-width="3" fill="none"/><path d="M200 90 Q160 110 140 130 M200 130 Q150 150 130 180 M200 180 Q160 200 145 225 M200 90 Q240 110 260 130 M200 130 Q250 150 270 180 M200 180 Q240 200 255 225" stroke="%23f9fbe7" stroke-width="2" fill="none" opacity="0.75"/><text x="200" y="285" fill="%23ffffff" font-size="14" font-family="sans-serif" text-anchor="middle">Contoh Sampel Daun Tembakau</text></svg>`;
        preview.src = sampleSvg;
        showScanningState();
    });

    function showScanningState() {
        preview.style.display = 'block';
        icon.style.display    = 'none';
        detectTitle.style.display = 'none';
        detectSub.style.display   = 'none';
        shutterWrap.style.display = 'none';
        sampleBtn.style.display   = 'none';
        resultBox.style.display   = 'none';

        scanOverlay.style.display = 'flex';

        setTimeout(() => {
            scanOverlay.style.display = 'none';
            resultBox.style.display   = 'block';
            showToast('Analisis selesai! Daun terdeteksi Grade A.');
        }, 1300);
    }

    saveResultBtn?.addEventListener('click', () => {
        showToast('Hasil sortir daun berhasil disimpan!');
    });

    function showToast(msg) {
        const toast = document.getElementById('tobaToast');
        const text  = document.getElementById('tobaToastMsg');
        if (!toast || !text) return;
        text.textContent = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3500);
    }
</script>
@endpush
