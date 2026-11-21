@extends('layouts.dashboard')

@section('title', 'Dashboard Admin LPH • SI-HALAL')

@section('extra-css')
<style>
    /* Middle Layout Grid */
    .middle-grid {
        display: grid;
        grid-template-columns: 7fr 4fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    /* Distribution / Status summary */
    .status-summary-box {
        display: flex;
        align-items: center;
        gap: 32px;
        margin-top: 20px;
    }

    .donut-chart-container {
        position: relative;
        width: 132px;
        height: 132px;
        flex-shrink: 0;
    }

    .donut-chart-container svg {
        transform: rotate(-90deg);
        border-radius: 50%;
    }

    .donut-center-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .donut-number {
        font-size: 22px;
        font-weight: 600;
        line-height: 28px;
        color: #203C2D;
    }

    .donut-label {
        font-size: 10px;
        font-weight: 400;
        line-height: 15px;
        color: #7C8B7E;
    }

    .status-distribution-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
        flex-grow: 1;
    }

    .distribution-row {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 11px;
    }

    .distribution-legend {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 140px;
        color: #657A6A;
    }

    .legend-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .distribution-count {
        font-size: 12px;
        font-weight: 600;
        color: #203A2D;
        width: 40px;
        text-align: right;
    }

    .distribution-bar-bg {
        flex: 1;
        height: 5px;
        background-color: #F0F4EF;
        border-radius: 3px;
        overflow: hidden;
    }

    .distribution-bar-fill {
        height: 100%;
        border-radius: 3px;
    }

    .distribution-pct {
        font-size: 10px;
        font-weight: 500;
        color: #849184;
        width: 44px;
        text-align: right;
    }

    /* Action Checklist Items */
    .action-item-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 14px;
    }

    .action-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 8px;
        transition: background 0.15s;
        cursor: pointer;
    }

    .action-item:hover {
        background-color: #F8FAF7;
    }

    .action-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .action-text h4 {
        font-size: 12px;
        font-weight: 500;
        color: #3C5543;
    }

    .action-text p {
        font-size: 10px;
        color: #7B8B7F;
        margin-top: 2px;
    }

    .action-arrow {
        margin-left: auto;
        opacity: 0.6;
    }

    .sync-footer {
        border-top: 1px solid #EDF1EB;
        padding-top: 14px;
        margin-top: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 10px;
        color: #78907C;
    }

    .sync-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #41805A;
    }

    @media (max-width: 1024px) {
        .middle-grid {
            grid-template-columns: 1fr;
        }
        .status-summary-box {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')

    <!-- Welcome Greeting & Date Banner -->
    <div class="welcome-row">
        <div class="welcome-text">
            <h2>Selamat Datang, {{ $user->nama }}.</h2>
            <p>Pantau pengajuan dan kelola proses pemeriksaan halal dalam satu tempat.</p>
        </div>
        <div class="date-badge">
            <img src="{{ asset('images/icon-history.svg') }}" alt="Tanggal" width="16" height="16">
            <span>{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
            <img src="{{ asset('images/icon-chevron.svg') }}" alt="Chevron" width="14" height="14">
        </div>
    </div>

    <!-- 4 Stat Cards -->
    <div class="stat-grid">
        <!-- Card 1: Jumlah Pengajuan -->
        <div class="stat-card">
            <div>
                <div class="stat-card-title">Jumlah Pengajuan</div>
                <div class="stat-card-number">{{ number_format($totalPengajuan, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card-sub">Seluruh pengajuan terdaftar</div>
            <div class="stat-card-icon" style="background-color: #EBF2EC;">
                <img src="{{ asset('images/card-total.svg') }}" alt="Total" width="37" height="37">
            </div>
        </div>

        <!-- Card 2: Menunggu Verifikasi -->
        <div class="stat-card">
            <div>
                <div class="stat-card-title">Menunggu Verifikasi</div>
                <div class="stat-card-number">{{ number_format($menungguVerifikasi, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card-sub">Perlu ditinjau admin</div>
            <div class="stat-card-icon" style="background-color: #FBF3DF;">
                <img src="{{ asset('images/card-pending.svg') }}" alt="Menunggu Verifikasi" width="37" height="37">
            </div>
        </div>

        <!-- Card 3: Sedang Diperiksa -->
        <div class="stat-card">
            <div>
                <div class="stat-card-title">Sedang Diperiksa</div>
                <div class="stat-card-number">{{ number_format($sedangDiperiksa, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card-sub">Dalam proses pemeriksaan</div>
            <div class="stat-card-icon" style="background-color: #ECF4F8;">
                <img src="{{ asset('images/card-process.svg') }}" alt="Sedang Diperiksa" width="37" height="37">
            </div>
        </div>

        <!-- Card 4: Selesai -->
        <div class="stat-card">
            <div>
                <div class="stat-card-title">Selesai</div>
                <div class="stat-card-number">{{ number_format($selesai, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card-sub">Pemeriksaan telah selesai</div>
            <div class="stat-card-icon" style="background-color: #EAF4EE;">
                <img src="{{ asset('images/card-done.svg') }}" alt="Selesai" width="37" height="37">
            </div>
        </div>
    </div>

    <!-- Middle Row: Status Pemeriksaan & Perlu Tindak Lanjut -->
    <div class="middle-grid">
        <!-- Left: Status Pemeriksaan -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header-clean">
                <div class="card-title-group">
                    <h3>Ringkasan Status Pemeriksaan</h3>
                    <p>Distribusi seluruh pengajuan yang terdaftar</p>
                </div>
                <div class="card-badge" style="background-color: #F5F7F4; color: #637B69; font-weight: 500;">
                    Semua waktu
                </div>
            </div>

            <div class="status-summary-box">
                <!-- SVG Donut Chart -->
                @php
                    $circumference = 314; // 2 * pi * r (r=50)
                    $dashSelesai = ($pctSelesai / 100) * $circumference;
                    $dashDiperiksa = ($pctSedangDiperiksa / 100) * $circumference;
                    $dashDitugaskan = ($pctDitugaskan / 100) * $circumference;
                    $dashMenunggu = ($pctMenungguVerifikasi / 100) * $circumference;
                    $dashPerbaikan = ($pctPerluPerbaikan / 100) * $circumference;

                    $offsetSelesai = 0;
                    $offsetDiperiksa = -$dashSelesai;
                    $offsetDitugaskan = -($dashSelesai + $dashDiperiksa);
                    $offsetMenunggu = -($dashSelesai + $dashDiperiksa + $dashDitugaskan);
                    $offsetPerbaikan = -($dashSelesai + $dashDiperiksa + $dashDitugaskan + $dashMenunggu);
                @endphp
                <div class="donut-chart-container">
                    <svg width="132" height="132" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="50" fill="transparent" stroke="#F0F4EF" stroke-width="12"></circle>
                        <!-- Segment Selesai -->
                        <circle cx="60" cy="60" r="50" fill="transparent" stroke="#286C4B" stroke-width="12"
                            stroke-dasharray="{{ $dashSelesai }} {{ $circumference }}" stroke-dashoffset="{{ $offsetSelesai }}"></circle>
                        <!-- Segment Sedang Diperiksa -->
                        <circle cx="60" cy="60" r="50" fill="transparent" stroke="#719AA9" stroke-width="12"
                            stroke-dasharray="{{ $dashDiperiksa }} {{ $circumference }}" stroke-dashoffset="{{ $offsetDiperiksa }}"></circle>
                        <!-- Segment Ditugaskan -->
                        <circle cx="60" cy="60" r="50" fill="transparent" stroke="#89661E" stroke-width="12"
                            stroke-dasharray="{{ $dashDitugaskan }} {{ $circumference }}" stroke-dashoffset="{{ $offsetDitugaskan }}"></circle>
                        <!-- Segment Menunggu Verifikasi -->
                        <circle cx="60" cy="60" r="50" fill="transparent" stroke="#CDAA58" stroke-width="12"
                            stroke-dasharray="{{ $dashMenunggu }} {{ $circumference }}" stroke-dashoffset="{{ $offsetMenunggu }}"></circle>
                        <!-- Segment Perlu Perbaikan -->
                        <circle cx="60" cy="60" r="50" fill="transparent" stroke="#C58E79" stroke-width="12"
                            stroke-dasharray="{{ $dashPerbaikan }} {{ $circumference }}" stroke-dashoffset="{{ $offsetPerbaikan }}"></circle>
                    </svg>
                    <div class="donut-center-text">
                        <div class="donut-number">{{ number_format($totalPengajuan, 0, ',', '.') }}</div>
                        <div class="donut-label">Pengajuan</div>
                    </div>
                </div>

                <!-- Legends and Bars -->
                <div class="status-distribution-list">
                    <!-- Selesai -->
                    <div class="distribution-row">
                        <div class="distribution-legend">
                            <span class="legend-dot" style="background-color: #286C4B;"></span>
                            <span>Selesai</span>
                        </div>
                        <div class="distribution-count">{{ $selesai }}</div>
                        <div class="distribution-bar-bg">
                            <div class="distribution-bar-fill" style="width: {{ $pctSelesai }}%; background-color: #286C4B;"></div>
                        </div>
                        <div class="distribution-pct">{{ str_replace('.', ',', $pctSelesai) }}%</div>
                    </div>

                    <!-- Sedang diperiksa -->
                    <div class="distribution-row">
                        <div class="distribution-legend">
                            <span class="legend-dot" style="background-color: #719AA9;"></span>
                            <span>Sedang diperiksa</span>
                        </div>
                        <div class="distribution-count">{{ $sedangDiperiksa }}</div>
                        <div class="distribution-bar-bg">
                            <div class="distribution-bar-fill" style="width: {{ $pctSedangDiperiksa }}%; background-color: #719AA9;"></div>
                        </div>
                        <div class="distribution-pct">{{ str_replace('.', ',', $pctSedangDiperiksa) }}%</div>
                    </div>

                    <!-- Ditugaskan -->
                    <div class="distribution-row">
                        <div class="distribution-legend">
                            <span class="legend-dot" style="background-color: #89661E;"></span>
                            <span>Ditugaskan</span>
                        </div>
                        <div class="distribution-count">{{ $ditugaskan }}</div>
                        <div class="distribution-bar-bg">
                            <div class="distribution-bar-fill" style="width: {{ $pctDitugaskan }}%; background-color: #89661E;"></div>
                        </div>
                        <div class="distribution-pct">{{ str_replace('.', ',', $pctDitugaskan) }}%</div>
                    </div>

                    <!-- Menunggu verifikasi -->
                    <div class="distribution-row">
                        <div class="distribution-legend">
                            <span class="legend-dot" style="background-color: #CDAA58;"></span>
                            <span>Menunggu verifikasi</span>
                        </div>
                        <div class="distribution-count">{{ $menungguVerifikasi }}</div>
                        <div class="distribution-bar-bg">
                            <div class="distribution-bar-fill" style="width: {{ $pctMenungguVerifikasi }}%; background-color: #CDAA58;"></div>
                        </div>
                        <div class="distribution-pct">{{ str_replace('.', ',', $pctMenungguVerifikasi) }}%</div>
                    </div>

                    <!-- Perlu Perbaikan -->
                    <div class="distribution-row">
                        <div class="distribution-legend">
                            <span class="legend-dot" style="background-color: #C58E79;"></span>
                            <span>Perlu Perbaikan</span>
                        </div>
                        <div class="distribution-count">{{ $perluPerbaikan }}</div>
                        <div class="distribution-bar-bg">
                            <div class="distribution-bar-fill" style="width: {{ $pctPerluPerbaikan }}%; background-color: #C58E79;"></div>
                        </div>
                        <div class="distribution-pct">{{ str_replace('.', ',', $pctPerluPerbaikan) }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Perlu Tindak Lanjut -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header-clean">
                <div class="card-title-group">
                    <h3>Perlu Tindak Lanjut</h3>
                    <p>Prioritas pekerjaan hari ini</p>
                </div>
                <div class="card-badge" style="background-color: #FAF2DE; color: #997323;">
                    {{ $totalTindakLanjut }}
                </div>
            </div>

            <div class="action-item-list">
                <!-- Action 1: Verifikasi pembayaran -->
                <div class="action-item">
                    <div class="action-icon-box" style="background-color: #FCF6E6;">
                        <img src="{{ asset('images/icon-pay.svg') }}" alt="Pembayaran" width="22" height="22">
                    </div>
                    <div class="action-text">
                        <h4>Verifikasi pembayaran</h4>
                        <p>{{ $pendingPayment }} pengajuan menunggu konfirmasi</p>
                    </div>
                    <div class="action-arrow">
                        <img src="{{ asset('images/icon-chevron.svg') }}" alt="Arrow" width="16" height="16" style="transform: rotate(-90deg);">
                    </div>
                </div>

                <!-- Action 2: Penugasan auditor -->
                <div class="action-item">
                    <div class="action-icon-box" style="background-color: #EDF4EF;">
                        <img src="{{ asset('images/icon-assign.svg') }}" alt="Penugasan" width="22" height="22">
                    </div>
                    <div class="action-text">
                        <h4>Penugasan auditor</h4>
                        <p>{{ $siapDitugaskan }} pengajuan siap ditugaskan</p>
                    </div>
                    <div class="action-arrow">
                        <img src="{{ asset('images/icon-chevron.svg') }}" alt="Arrow" width="16" height="16" style="transform: rotate(-90deg);">
                    </div>
                </div>
            </div>

            <!-- Sync Indicator Footer -->
            <div class="sync-footer">
                <span class="sync-dot"></span>
                <span>SiHalal tersinkron • {{ $latestSync ? \Carbon\Carbon::parse($latestSync->waktu_sinkronisasi)->isoFormat('D MMM Y, HH.mm [WIB]') : 'Belum tersinkron' }}</span>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Pengajuan Terbaru -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header-clean">
            <div class="card-title-group">
                <h3>
                    Pengajuan Terbaru 
                    <span class="card-badge">{{ $latestPengajuan->count() }}</span>
                </h3>
            </div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 8px; border: 1px solid #E1E8DF; border-radius: 6px; padding: 6px 12px; font-size: 10px; font-weight: 500; color: #5C755F;">
                    <span>Semua status</span>
                    <img src="{{ asset('images/icon-chevron.svg') }}" alt="Filter" width="12" height="12">
                </div>
                <a href="#lihat-semua" style="font-size: 11px; font-weight: 600; color: #2B6948; display: flex; align-items: center; gap: 6px;">
                    <span>Lihat semua</span>
                    <img src="{{ asset('images/icon-chevron.svg') }}" alt="Next" width="12" height="12" style="transform: rotate(-90deg);">
                </a>
            </div>
        </div>

        <div class="table-container">
            <table class="clean-table">
                <thead>
                    <tr>
                        <th>Nomor Pengajuan</th>
                        <th>Nama Pelaku Usaha</th>
                        <th>Produk</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestPengajuan as $pengajuan)
                        <tr>
                            <td class="code-reg">{{ $pengajuan->nomor_pengajuan }}</td>
                            <td class="biz-name">{{ $pengajuan->nama_usaha }}</td>
                            <td class="product-name">{{ $pengajuan->nama_produk ?? '-' }}</td>
                            <td class="date-col">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->isoFormat('D MMM Y') }}</td>
                            <td>
                                @php
                                    $statusClass = match($pengajuan->status_pengajuan) {
                                        'Selesai' => 'status-selesai',
                                        'Sedang Diperiksa' => 'status-sedang-diperiksa',
                                        'Menunggu Verifikasi' => 'status-menunggu-verifikasi',
                                        'Perlu Perbaikan' => 'status-perlu-revisi',
                                        'Ditugaskan' => 'status-ditugaskan',
                                        default => 'status-menunggu-verifikasi'
                                    };
                                @endphp
                                <span class="status-pill {{ $statusClass }}">
                                    <span class="status-dot"></span>
                                    <span>{{ $pengajuan->status_pengajuan }}</span>
                                </span>
                            </td>
                            <td>
                                <a href="#detail-{{ $pengajuan->id_pengajuan }}" class="btn-action-pill">
                                    <img src="{{ asset('images/icon-detail.svg') }}" alt="Detail" width="14" height="14">
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: #7B8B7F;">
                                Belum ada data pengajuan terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px; font-size: 10px; color: #869281;">
            Menampilkan {{ $latestPengajuan->count() }} dari {{ number_format($totalPengajuan, 0, ',', '.') }} pengajuan
        </div>
    </div>

@endsection
