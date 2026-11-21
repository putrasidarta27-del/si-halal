@extends('layouts.dashboard')

@section('title', 'Dashboard Auditor • SI-HALAL')

@section('extra-css')
<style>
    /* Bottom Layout Grid */
    .auditor-bottom-grid {
        display: grid;
        grid-template-columns: 6fr 4fr;
        gap: 24px;
        margin-top: 24px;
    }

    /* Activity Timeline List */
    .activity-list {
        display: flex;
        flex-direction: column;
        margin-top: 14px;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 0;
        position: relative;
    }

    .activity-item:not(:last-child) {
        border-bottom: 1px solid #F0F3EE;
    }

    .activity-marker {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        margin-top: 6px;
        flex-shrink: 0;
    }

    .activity-details {
        flex-grow: 1;
    }

    .activity-title {
        font-size: 11px;
        font-weight: 500;
        line-height: 16px;
        color: #203B2E;
    }

    .activity-sub {
        font-size: 10px;
        font-weight: 400;
        line-height: 15px;
        color: #6A7C70;
        margin-top: 2px;
    }

    .activity-time {
        font-size: 10px;
        color: #6A7C70;
        margin-left: 12px;
        white-space: nowrap;
    }

    /* Priority Tasks */
    .priority-list {
        display: flex;
        flex-direction: column;
        margin-top: 14px;
    }

    .priority-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        gap: 12px;
    }

    .priority-item:not(:last-child) {
        border-bottom: 1px solid #EDF1EB;
    }

    .priority-info h4 {
        font-size: 12px;
        font-weight: 600;
        line-height: 17px;
        color: #203B2E;
    }

    .priority-info p {
        font-size: 10px;
        font-weight: 400;
        line-height: 15px;
        color: #6A7C70;
        margin-top: 2px;
    }

    @media (max-width: 1024px) {
        .auditor-bottom-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')

    <!-- Welcome Greeting & Date Banner -->
    <div class="welcome-row">
        <div class="welcome-text">
            <h2>Selamat datang, Auditor {{ $user->nama }}.</h2>
            <p>Ringkasan pemeriksaan halal untuk penugasan Anda. Hanya pengajuan yang ditugaskan kepada Anda yang ditampilkan.</p>
        </div>
        <div class="date-badge">
            <img src="{{ asset('images/icon-history.svg') }}" alt="Tanggal" width="16" height="16">
            <span>{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
            <img src="{{ asset('images/icon-chevron.svg') }}" alt="Chevron" width="14" height="14">
        </div>
    </div>

    <!-- 4 Personal Stat Cards -->
    <div class="stat-grid">
        <!-- Card 1: Total Penugasan -->
        <div class="stat-card">
            <div>
                <div class="stat-card-title">Total Penugasan</div>
                <div class="stat-card-number">{{ number_format($totalPenugasan, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card-sub">Termasuk {{ $penugasanSelesai }} pemeriksaan selesai</div>
            <div class="stat-card-icon" style="background-color: #EBF2EC;">
                <img src="{{ asset('images/card-total.svg') }}" alt="Total" width="37" height="37">
            </div>
        </div>

        <!-- Card 2: Ditugaskan -->
        <div class="stat-card">
            <div>
                <div class="stat-card-title">Ditugaskan</div>
                <div class="stat-card-number">{{ number_format($penugasanDitugaskan, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card-sub">Penugasan aktif</div>
            <div class="stat-card-icon" style="background-color: #FBF3DF;">
                <img src="{{ asset('images/card-pending.svg') }}" alt="Ditugaskan" width="37" height="37">
            </div>
        </div>

        <!-- Card 3: Sedang Diperiksa -->
        <div class="stat-card">
            <div>
                <div class="stat-card-title">Sedang Diperiksa</div>
                <div class="stat-card-number">{{ number_format($penugasanSedangDiperiksa, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card-sub">Pemeriksaan aktif Anda</div>
            <div class="stat-card-icon" style="background-color: #ECF4F8;">
                <img src="{{ asset('images/card-process.svg') }}" alt="Sedang Diperiksa" width="37" height="37">
            </div>
        </div>

        <!-- Card 4: Menunggu Pemeriksaan Ulang -->
        <div class="stat-card">
            <div>
                <div class="stat-card-title">Menunggu Pemeriksaan Ulang</div>
                <div class="stat-card-number">{{ number_format($penugasanPemeriksaanUlang, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card-sub">Dokumen perbaikan telah diterima</div>
            <div class="stat-card-icon" style="background-color: #FAF2DE;">
                <img src="{{ asset('images/card-repeat.svg') }}" alt="Pemeriksaan Ulang" width="37" height="37">
            </div>
        </div>
    </div>

    <!-- Table: Penugasan Terbaru -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <div class="card-title-group">
                <h3>
                    Penugasan Terbaru 
                    <span class="card-badge">{{ $latestPenugasan->count() }} terbaru</span>
                </h3>
                <p>Daftar pengajuan yang ditugaskan kepada Anda</p>
            </div>
            <a href="#semua-penugasan" style="font-size: 11px; font-weight: 600; color: #2B6948; display: flex; align-items: center; gap: 6px;">
                <span>Lihat semua penugasan</span>
                <img src="{{ asset('images/icon-chevron.svg') }}" alt="Next" width="12" height="12" style="transform: rotate(-90deg);">
            </a>
        </div>

        <div class="table-container">
            <table class="clean-table">
                <thead>
                    <tr>
                        <th>Nomor Pengajuan</th>
                        <th>Nama Pelaku Usaha</th>
                        <th>Produk</th>
                        <th>Tanggal Penugasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestPenugasan as $item)
                        <tr>
                            <td class="code-reg">{{ $item->nomor_pengajuan }}</td>
                            <td class="biz-name">{{ $item->nama_usaha }}</td>
                            <td class="product-name">{{ $item->nama_produk ?? '-' }}</td>
                            <td class="date-col">{{ \Carbon\Carbon::parse($item->tanggal_penugasan)->isoFormat('D MMM Y') }}</td>
                            <td>
                                @php
                                    $statusPillClass = match($item->status_pengajuan) {
                                        'Selesai' => 'status-selesai',
                                        'Sedang Diperiksa' => 'status-sedang-diperiksa',
                                        'Menunggu Verifikasi' => 'status-menunggu-verifikasi',
                                        'Perlu Perbaikan' => 'status-perlu-revisi',
                                        'Ditugaskan' => 'status-ditugaskan',
                                        default => 'status-ditugaskan'
                                    };
                                @endphp
                                <span class="status-pill {{ $statusPillClass }}">
                                    <span class="status-dot"></span>
                                    <span>{{ $item->status_pengajuan }}</span>
                                </span>
                            </td>
                            <td>
                                @if ($item->status_pengajuan === 'Ditugaskan')
                                    <button class="btn-action-secondary">Mulai Periksa</button>
                                @elseif ($item->status_pengajuan === 'Sedang Diperiksa')
                                    <button class="btn-action-secondary">Lanjutkan</button>
                                @elseif ($item->status_pengajuan === 'Perlu Perbaikan')
                                    <button class="btn-action-secondary">Periksa Ulang</button>
                                @else
                                    <button class="btn-action-pill">
                                        <img src="{{ asset('images/icon-detail.svg') }}" alt="Detail" width="14" height="14">
                                        <span>Detail</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: #7B8B7F;">
                                Belum ada penugasan pemeriksaan untuk Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px; display: flex; justify-content: space-between; font-size: 10px; color: #6A7C70;">
            <div>Menampilkan {{ $latestPenugasan->count() }} dari {{ number_format($totalPenugasan, 0, ',', '.') }} penugasan Anda</div>
            <div>Diperbarui {{ \Carbon\Carbon::now()->isoFormat('D MMM Y, HH.mm [WIB]') }}</div>
        </div>
    </div>

    <!-- Bottom Row: Aktivitas Terbaru & Prioritas Tindak Lanjut -->
    <div class="auditor-bottom-grid">
        <!-- Left: Aktivitas Pemeriksaan Terbaru -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header-clean">
                <div class="card-title-group">
                    <h3>Aktivitas Pemeriksaan Terbaru</h3>
                    <p>Aktivitas pada pengajuan dalam penugasan Anda</p>
                </div>
            </div>

            <div class="activity-list">
                @php
                    $colors = ['#C7A354', '#719AA9', '#286C4B'];
                @endphp
                @forelse ($recentActivities as $index => $act)
                    <div class="activity-item">
                        <span class="activity-marker" style="background-color: {{ $colors[$index % count($colors)] }};"></span>
                        <div class="activity-details">
                            <div class="activity-title">{{ $act->ringkasan ?? 'Pemeriksaan diperbarui' }}</div>
                            <div class="activity-sub">{{ $act->nomor_pengajuan }} • {{ $act->nama_usaha }}</div>
                        </div>
                        <div class="activity-time">
                            {{ \Carbon\Carbon::parse($act->waktu_dicatat)->isoFormat('D MMM Y') }}
                        </div>
                    </div>
                @empty
                    <div style="padding: 16px 0; font-size: 11px; color: #7B8B7F;">
                        Belum ada riwayat aktivitas terbaru.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Perlu Segera Ditindaklanjuti -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header-clean">
                <div class="card-title-group">
                    <h3>Perlu Segera Ditindaklanjuti</h3>
                    <p>Prioritas dari penugasan Anda</p>
                </div>
                <div class="card-badge" style="background-color: #FAF2DE; color: #89661E;">
                    {{ $priorityCount }}
                </div>
            </div>

            <div class="priority-list">
                @forelse ($priorityTasks as $task)
                    <div class="priority-item">
                        <div class="priority-info">
                            <h4>{{ $task->nama_usaha }}</h4>
                            <p>
                                {{ $task->nomor_pengajuan }} • 
                                @if ($task->status_pengajuan === 'Perlu Perbaikan')
                                    Dokumen perbaikan siap diperiksa
                                @else
                                    Mulai pemeriksaan penugasan
                                @endif
                            </p>
                        </div>
                        <div>
                            @if ($task->status_pengajuan === 'Perlu Perbaikan')
                                <button class="btn-action-primary">Periksa Ulang</button>
                            @else
                                <button class="btn-action-secondary">Mulai Periksa</button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="padding: 16px 0; font-size: 11px; color: #7B8B7F;">
                        Tidak ada penugasan mendesak yang perlu ditindaklanjuti saat ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Page Footer -->
    <div class="footer">
        <div>© 2026 LPH UIN Sunan Ampel Surabaya</div>
        <div>Sistem Informasi Pendampingan dan Sertifikasi Halal</div>
    </div>

@endsection
