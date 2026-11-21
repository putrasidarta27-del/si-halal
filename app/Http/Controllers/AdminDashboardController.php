<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display Admin LPH Dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Stat cards metrics
        $totalPengajuan = DB::table('pengajuan')->count();
        $menungguVerifikasi = DB::table('pengajuan')->where('status_pengajuan', 'Menunggu Verifikasi')->count();
        $sedangDiperiksa = DB::table('pengajuan')->where('status_pengajuan', 'Sedang Diperiksa')->count();
        $selesai = DB::table('pengajuan')->where('status_pengajuan', 'Selesai')->count();
        $perluPerbaikan = DB::table('pengajuan')->where('status_pengajuan', 'Perlu Perbaikan')->count();
        $ditugaskan = DB::table('pengajuan')->where('status_pengajuan', 'Ditugaskan')->count();

        // 2. Status distribution percentages (Guaranteed 100.0% total via largest remainder method)
        $statuses = [
            'selesai' => $selesai,
            'sedangDiperiksa' => $sedangDiperiksa,
            'ditugaskan' => $ditugaskan,
            'menungguVerifikasi' => $menungguVerifikasi,
            'perluPerbaikan' => $perluPerbaikan,
        ];

        $percentages = [];
        if ($totalPengajuan > 0) {
            $floors = [];
            $remainders = [];
            $sumFloors = 0;

            foreach ($statuses as $key => $count) {
                $raw = ($count / $totalPengajuan) * 100;
                $floor = floor($raw * 10) / 10;
                $floors[$key] = $floor;
                $remainders[$key] = $raw - $floor;
                $sumFloors += $floor;
            }

            // Number of 0.1 increments needed to reach 100.0
            $diffTenths = (int) round((100.0 - $sumFloors) * 10);
            arsort($remainders);

            $percentages = $floors;
            foreach ($remainders as $key => $rem) {
                if ($diffTenths <= 0) break;
                $percentages[$key] = round($percentages[$key] + 0.1, 1);
                $diffTenths--;
            }
        } else {
            foreach ($statuses as $key => $count) {
                $percentages[$key] = 0.0;
            }
        }

        $pctSelesai = $percentages['selesai'];
        $pctSedangDiperiksa = $percentages['sedangDiperiksa'];
        $pctDitugaskan = $percentages['ditugaskan'];
        $pctMenungguVerifikasi = $percentages['menungguVerifikasi'];
        $pctPerluPerbaikan = $percentages['perluPerbaikan'];
        $pctPerluRevisi = $pctPerluPerbaikan;
        $perluRevisi = $perluPerbaikan;

        // 3. Perlu Tindak Lanjut items
        $pendingPayment = DB::table('pembayaran')
            ->where('status_pembayaran', 'Belum Terverifikasi')
            ->count();

        $siapDitugaskan = DB::table('pengajuan')
            ->whereNotIn('id_pengajuan', DB::table('penugasan')->pluck('id_pengajuan'))
            ->count();

        $totalTindakLanjut = $pendingPayment + $siapDitugaskan;

        // 4. Latest SiHalal Synchronization
        $latestSync = DB::table('sinkronisasi_sihalal')
            ->orderBy('waktu_sinkronisasi', 'desc')
            ->first();

        // 5. Unread notifications
        $unreadNotifications = DB::table('notifikasi')
            ->where('id_penerima', $user->id_user)
            ->where('sudah_dibaca', 0)
            ->count();
        if ($unreadNotifications === 0) {
            $unreadNotifications = DB::table('notifikasi')->where('sudah_dibaca', 0)->count();
        }

        // 6. Latest Pengajuan list
        $latestPengajuan = DB::table('pengajuan')
            ->join('pelaku_usaha', 'pengajuan.id_pelaku_usaha', '=', 'pelaku_usaha.id_pelaku_usaha')
            ->leftJoin('produk', 'pengajuan.id_pengajuan', '=', 'produk.id_pengajuan')
            ->select(
                'pengajuan.id_pengajuan',
                'pengajuan.nomor_pengajuan',
                'pengajuan.tanggal_pengajuan',
                'pengajuan.status_pengajuan',
                'pelaku_usaha.nama_usaha',
                'produk.nama_produk'
            )
            ->orderBy('pengajuan.tanggal_pengajuan', 'desc')
            ->orderBy('pengajuan.id_pengajuan', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'user',
            'totalPengajuan',
            'menungguVerifikasi',
            'sedangDiperiksa',
            'selesai',
            'perluPerbaikan',
            'perluRevisi',
            'ditugaskan',
            'pctSelesai',
            'pctSedangDiperiksa',
            'pctDitugaskan',
            'pctMenungguVerifikasi',
            'pctPerluPerbaikan',
            'pctPerluRevisi',
            'pendingPayment',
            'siapDitugaskan',
            'totalTindakLanjut',
            'latestSync',
            'unreadNotifications',
            'latestPengajuan'
        ));
    }
}
