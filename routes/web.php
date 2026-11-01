<?php

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* ---------- DATA CONTOH (backend mengganti dengan database) ---------- */
$sorters = collect([
    ['id' => 1, 'nama' => 'Sorter 1', 'username' => 'sorter1', 'aktif' => true],
    ['id' => 2, 'nama' => 'Sorter 2', 'username' => 'sorter2', 'aktif' => true],
    ['id' => 3, 'nama' => 'Sorter 3', 'username' => 'sorter3', 'aktif' => true],
    ['id' => 4, 'nama' => 'Sorter 4', 'username' => 'sorter4', 'aktif' => false],
]);

// Menghasilkan angka palsu untuk chart. Parameter: ?sorter=all|id&start=&end=
$ambilData = function (Request $request) {
    $end   = $request->filled('end') ? Carbon::parse($request->end) : now();
    $start = $request->filled('start') ? Carbon::parse($request->start) : $end->copy()->subDays(6);
    $seed  = (! $request->filled('sorter') || $request->sorter === 'all') ? 0 : (int) $request->sorter;

    $labels = [];
    $values = [];
    for ($d = $start->copy(); $d->lte($end) && count($labels) < 31; $d->addDay()) {
        $labels[] = $d->translatedFormat('d M');
        $values[] = (($d->day * 7 + $seed * 5) % 19 + 4) * ($seed ? 1 : 4);
    }
    return [$labels, $values];
};

/* ---------- Umum & login ---------- */
Route::redirect('/', '/login');
Route::view('/login', 'auth.login')->name('login');

Route::post('/login', function (Request $request) {
    $username = strtolower(trim($request->input('username')));

    if ($username === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($username === 'sorter') {
        return redirect()->route('sorter.dashboard');
    }
    return back()->withErrors(['username' => 'Username tidak ditemukan.'])->withInput();
})->name('login.post');

Route::post('/logout', fn () => redirect()->route('login'))->name('logout');

/* ---------- Sorter ---------- */
Route::prefix('sorter')->name('sorter.')->group(function () use ($ambilData) {
    Route::view('/dashboard', 'sorter.dashboard_sorter')->name('dashboard');
    Route::view('/riwayat', 'sorter.riwayat_sortir')->name('riwayat');

    Route::get('/riwayat/data', function (Request $request) use ($ambilData) {
        [$labels, $values] = $ambilData($request);
        return response()->json(['labels' => $labels, 'values' => $values]);
    })->name('riwayat.data');
});

/* ---------- Admin ---------- */
Route::prefix('admin')->name('admin.')->group(function () use ($sorters, $ambilData) {

    Route::get('/dashboard', fn () => view('admin.dashboard_admin', ['sorters' => $sorters]))
        ->name('dashboard');

    // Data chart
    Route::get('/riwayat/data', function (Request $request) use ($ambilData) {
        [$labels, $values] = $ambilData($request);
        return response()->json(['labels' => $labels, 'values' => $values]);
    })->name('riwayat.data');

    // Unduh data (contoh: CSV)
    Route::get('/riwayat/unduh', function (Request $request) use ($ambilData, $sorters) {
        [$labels, $values] = $ambilData($request);

        $nama = (! $request->filled('sorter') || $request->sorter === 'all')
            ? 'Semua Sorter'
            : ($sorters->firstWhere('id', (int) $request->sorter)['nama'] ?? 'Sorter');

        return response()->streamDownload(function () use ($labels, $values, $nama) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // agar terbaca benar di Excel
            fputcsv($out, ['Tanggal', 'Sorter', 'Jumlah']);
            foreach ($labels as $i => $label) {
                fputcsv($out, [$label, $nama, $values[$i]]);
            }
            fclose($out);
        }, 'riwayat-sortir.csv', ['Content-Type' => 'text/csv']);
    })->name('riwayat.unduh');

    // Manajemen sortir
    Route::get('/manajemen', fn () => view('admin.manajemen_sortir', ['sorters' => $sorters]))
        ->name('manajemen');

    // Sementara: hanya kembali dengan pesan. Backend mengganti dengan controller.
    Route::post('/sorter', fn () => back()->with('success', 'Akun ditambahkan (contoh, belum ke database).'))
        ->name('sorter.store');
    Route::put('/sorter/{id}', fn () => back()->with('success', 'Akun diperbarui (contoh, belum ke database).'))
        ->name('sorter.update');
    Route::patch('/sorter/{id}/status', fn (Request $r) => back()->with(
        'success',
        ($r->status === 'aktif' ? 'Akun diaktifkan' : 'Akun dinonaktifkan') . ' (contoh, belum ke database).'
    ))->name('sorter.status');
});
