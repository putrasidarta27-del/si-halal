<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SinkronisasiController extends Controller
{
    /**
     * Tampilkan halaman Sinkronisasi SiHalal.
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil riwayat sinkronisasi dari database
        $riwayatSinkronisasi = DB::table('sinkronisasi_sihalal')
            ->orderBy('waktu_sinkronisasi', 'desc')
            ->get();

        // Sesi sinkronisasi terbaru
        $latestSync = $riwayatSinkronisasi->first();

        // Notifikasi belum dibaca untuk sidebar
        $unreadNotifications = DB::table('notifikasi')
            ->where('id_penerima', $user->id_user)
            ->where('sudah_dibaca', 0)
            ->count();
        if ($unreadNotifications === 0) {
            $unreadNotifications = DB::table('notifikasi')->where('sudah_dibaca', 0)->count();
        }

        return view('admin.sinkronisasi', compact(
            'user',
            'riwayatSinkronisasi',
            'latestSync',
            'unreadNotifications'
        ));
    }

    /**
     * Simulasi pencatatan sinkronisasi SiHalal.
     * Mencatat satu baris baru ke tabel sinkronisasi_sihalal dengan id_user = admin yang login.
     * Menggunakan nilai ENUM persis: 'Selesai', 'Sebagian', atau 'Gagal'.
     */
    public function syncSimulation(Request $request)
    {
        $user = Auth::user();

        // Menentukan hasil simulasi secara realistis
        $statusOptions = ['Selesai', 'Sebagian'];
        $status = $statusOptions[array_rand($statusOptions)];

        $jumlahBaru = rand(2, 10);
        $jumlahDiperbarui = rand(5, 20);
        $jumlahGagal = ($status === 'Sebagian') ? rand(1, 3) : 0;
        $pesanKesalahan = ($status === 'Sebagian') ? 'Satu atau lebih data tidak lengkap, perlu ditinjau admin.' : null;

        // Query berparameter melalui query builder Laravel
        DB::table('sinkronisasi_sihalal')->insert([
            'id_user' => $user->id_user,
            'waktu_sinkronisasi' => now(),
            'jumlah_data_baru' => $jumlahBaru,
            'jumlah_data_diperbarui' => $jumlahDiperbarui,
            'jumlah_data_gagal' => $jumlahGagal,
            'status_sinkronisasi' => $status,
            'pesan_kesalahan' => $pesanKesalahan,
        ]);

        return redirect()->route('admin.sinkronisasi')
            ->with('success', 'Simulasi sinkronisasi berhasil dicatat! Status: ' . $status . ' (' . $jumlahBaru . ' data baru, ' . $jumlahDiperbarui . ' diperbarui).');
    }

    /**
     * Halaman Data Pengajuan (tujuan dari tombol Kelola Data Secara Manual).
     */
    public function dataPengajuan()
    {
        return redirect()->route('admin.pengajuan');
    }
}
