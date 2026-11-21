<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditorDashboardController extends Controller
{
    /**
     * Display Auditor Dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $auditorId = $user->id_user;

        // 1. Auditor personal assignment statistics
        $totalPenugasan = DB::table('penugasan')
            ->where('id_auditor', $auditorId)
            ->count();

        $penugasanSelesai = DB::table('penugasan')
            ->where('id_auditor', $auditorId)
            ->where('status_penugasan', 'Selesai')
            ->count();

        $penugasanDitugaskan = DB::table('penugasan')
            ->join('pengajuan', 'penugasan.id_pengajuan', '=', 'pengajuan.id_pengajuan')
            ->where('penugasan.id_auditor', $auditorId)
            ->where('pengajuan.status_pengajuan', 'Ditugaskan')
            ->count();

        $penugasanSedangDiperiksa = DB::table('penugasan')
            ->join('pengajuan', 'penugasan.id_pengajuan', '=', 'pengajuan.id_pengajuan')
            ->where('penugasan.id_auditor', $auditorId)
            ->where('pengajuan.status_pengajuan', 'Sedang Diperiksa')
            ->count();

        $penugasanPemeriksaanUlang = DB::table('penugasan')
            ->join('pengajuan', 'penugasan.id_pengajuan', '=', 'pengajuan.id_pengajuan')
            ->where('penugasan.id_auditor', $auditorId)
            ->where('pengajuan.status_pengajuan', 'Perlu Perbaikan')
            ->count();

        // 2. Unread notifications count
        $unreadNotifications = DB::table('notifikasi')
            ->where('id_penerima', $auditorId)
            ->where('sudah_dibaca', 0)
            ->count();

        // 3. Penugasan Terbaru (Personal assignments)
        $latestPenugasan = DB::table('penugasan')
            ->join('pengajuan', 'penugasan.id_pengajuan', '=', 'pengajuan.id_pengajuan')
            ->join('pelaku_usaha', 'pengajuan.id_pelaku_usaha', '=', 'pelaku_usaha.id_pelaku_usaha')
            ->leftJoin('produk', 'pengajuan.id_pengajuan', '=', 'produk.id_pengajuan')
            ->where('penugasan.id_auditor', $auditorId)
            ->select(
                'penugasan.id_penugasan',
                'penugasan.tanggal_penugasan',
                'penugasan.status_penugasan',
                'pengajuan.id_pengajuan',
                'pengajuan.nomor_pengajuan',
                'pengajuan.status_pengajuan',
                'pelaku_usaha.nama_usaha',
                'produk.nama_produk'
            )
            ->orderBy('penugasan.tanggal_penugasan', 'desc')
            ->orderBy('penugasan.id_penugasan', 'desc')
            ->limit(5)
            ->get();

        // 4. Aktivitas Pemeriksaan Terbaru
        $auditorPengajuanIds = DB::table('penugasan')
            ->where('id_auditor', $auditorId)
            ->pluck('id_pengajuan');

        $recentActivities = DB::table('riwayat_revisi')
            ->join('pengajuan', 'riwayat_revisi.id_pengajuan', '=', 'pengajuan.id_pengajuan')
            ->join('pelaku_usaha', 'pengajuan.id_pelaku_usaha', '=', 'pelaku_usaha.id_pelaku_usaha')
            ->whereIn('riwayat_revisi.id_pengajuan', $auditorPengajuanIds)
            ->select(
                'riwayat_revisi.id_riwayat',
                'riwayat_revisi.ringkasan',
                'riwayat_revisi.status_tahap',
                'riwayat_revisi.waktu_dicatat',
                'pengajuan.nomor_pengajuan',
                'pelaku_usaha.nama_usaha'
            )
            ->orderBy('riwayat_revisi.waktu_dicatat', 'desc')
            ->limit(3)
            ->get();

        if ($recentActivities->isEmpty()) {
            $recentActivities = DB::table('riwayat_status')
                ->join('pengajuan', 'riwayat_status.id_pengajuan', '=', 'pengajuan.id_pengajuan')
                ->join('pelaku_usaha', 'pengajuan.id_pelaku_usaha', '=', 'pelaku_usaha.id_pelaku_usaha')
                ->whereIn('riwayat_status.id_pengajuan', $auditorPengajuanIds)
                ->select(
                    'riwayat_status.id_riwayat_status as id_riwayat',
                    'riwayat_status.keterangan as ringkasan',
                    'riwayat_status.status_baru as status_tahap',
                    'riwayat_status.waktu_perubahan as waktu_dicatat',
                    'pengajuan.nomor_pengajuan',
                    'pelaku_usaha.nama_usaha'
                )
                ->orderBy('riwayat_status.waktu_perubahan', 'desc')
                ->limit(3)
                ->get();
        }

        // 5. Perlu Segera Ditindaklanjuti (Priority tasks for this auditor)
        $priorityTasks = DB::table('penugasan')
            ->join('pengajuan', 'penugasan.id_pengajuan', '=', 'pengajuan.id_pengajuan')
            ->join('pelaku_usaha', 'pengajuan.id_pelaku_usaha', '=', 'pelaku_usaha.id_pelaku_usaha')
            ->where('penugasan.id_auditor', $auditorId)
            ->whereIn('pengajuan.status_pengajuan', ['Ditugaskan', 'Perlu Perbaikan'])
            ->select(
                'penugasan.id_penugasan',
                'penugasan.tanggal_penugasan',
                'pengajuan.id_pengajuan',
                'pengajuan.nomor_pengajuan',
                'pengajuan.status_pengajuan',
                'pelaku_usaha.nama_usaha'
            )
            ->orderBy('penugasan.tanggal_penugasan', 'desc')
            ->limit(2)
            ->get();

        $priorityCount = $priorityTasks->count();

        return view('auditor.dashboard', compact(
            'user',
            'totalPenugasan',
            'penugasanSelesai',
            'penugasanDitugaskan',
            'penugasanSedangDiperiksa',
            'penugasanPemeriksaanUlang',
            'unreadNotifications',
            'latestPenugasan',
            'recentActivities',
            'priorityTasks',
            'priorityCount'
        ));
    }
}
