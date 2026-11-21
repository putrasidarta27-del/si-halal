@extends('layouts.dashboard')

@section('title', 'Sinkronisasi SiHalal • SI-HALAL LPH UINSA')

@section('extra-css')
<style>
    /* Sinkronisasi Page Layout */
    .sync-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .sync-page-title h2 {
        font-size: 25px;
        font-weight: 600;
        line-height: 36px;
        color: #203D2E;
    }

    .sync-page-title p {
        font-size: 12px;
        font-weight: 400;
        line-height: 17px;
        color: #697C70;
        margin-top: 2px;
    }

    .badge-prototype {
        background-color: #FCF6E8;
        color: #946B24;
        font-size: 10px;
        font-weight: 600;
        line-height: 15px;
        padding: 6px 12px;
        border-radius: 6px;
        letter-spacing: 0.05em;
    }

    /* Connection Card */
    .connection-card {
        background-color: #FFFFFF;
        border: 1px solid #E3EAE5;
        border-radius: 12px;
        padding: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .connection-left {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        flex: 1.2;
    }

    .connection-icon-box {
        width: 44px;
        height: 44px;
        background-color: #EDF3EF;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .connection-info h3 {
        font-size: 15px;
        font-weight: 600;
        line-height: 22px;
        color: #203D2E;
    }

    .connection-status-pill {
        display: inline-block;
        background-color: #FCF6E8;
        color: #946B24;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 5px;
        margin: 4px 0 8px 0;
    }

    .connection-info p {
        font-size: 11px;
        color: #697C70;
        line-height: 16px;
    }

    .connection-divider {
        width: 1px;
        height: 90px;
        background-color: #E3EAE5;
        flex-shrink: 0;
    }

    .connection-middle {
        flex: 1.3;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .connection-middle-top {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 500;
        color: #697C70;
        margin-bottom: 6px;
    }

    .connection-timestamp {
        font-size: 15px;
        font-weight: 600;
        line-height: 22px;
        color: #203D2E;
        margin-bottom: 4px;
    }

    .connection-summary-sub {
        font-size: 11px;
        color: #697C70;
    }

    .connection-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        flex: 1;
    }

    .btn-sync-now {
        background-color: #103F35;
        color: #FFFFFF;
        border: none;
        border-radius: 8px;
        height: 42px;
        padding: 0 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: background-color 0.15s, transform 0.05s;
        font-family: inherit;
    }

    .btn-sync-now:hover {
        background-color: #175246;
    }

    .btn-sync-now:active {
        transform: scale(0.98);
    }

    .btn-sync-subtext {
        font-size: 10px;
        color: #697C70;
        text-align: right;
    }

    /* Warning / Failure Banner */
    .warning-banner {
        background-color: #FCF6E8;
        border: 1px solid #EAD9B1;
        border-radius: 10px;
        padding: 18px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .warning-left {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .warning-circle {
        width: 28px;
        height: 28px;
        background-color: #F5E8C6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #946B24;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .warning-text h4 {
        font-size: 14px;
        font-weight: 600;
        color: #725321;
        margin-bottom: 4px;
    }

    .warning-text p {
        font-size: 12px;
        color: #7C6841;
        line-height: 18px;
    }

    .btn-manual-manage {
        background-color: #FFFFFF;
        border: 1px solid #D8C397;
        color: #103F35;
        border-radius: 8px;
        padding: 10px 18px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        white-space: nowrap;
        transition: background-color 0.15s;
    }

    .btn-manual-manage:hover {
        background-color: #FAF4E8;
    }

    /* Result 3 Cards */
    .results-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .results-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #203D2E;
    }

    .results-section-sub {
        font-size: 11px;
        color: #697C70;
    }

    .results-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 24px;
    }

    .result-card {
        background-color: #FFFFFF;
        border: 1px solid #E3EAE5;
        border-radius: 10px;
        height: 80px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .result-label {
        font-size: 12px;
        font-weight: 500;
        color: #697C70;
    }

    .result-number-group {
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-top: 2px;
    }

    .result-number {
        font-size: 27px;
        font-weight: 600;
        line-height: 35px;
        color: #203D2E;
    }

    .result-number.failed {
        color: #A44835;
    }

    .result-sub {
        font-size: 10px;
        color: #697C70;
    }

    .result-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Table Badges */
    .sync-status-selesai {
        background-color: #EDF5EC;
        color: #397453;
        font-size: 10px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 12px;
    }

    .sync-status-sebagian {
        background-color: #FCF6E8;
        color: #946B24;
        font-size: 10px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 5px;
    }

    .sync-status-gagal {
        background-color: #FBEFEA;
        color: #A44835;
        font-size: 10px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 5px;
    }

    /* Modal Styling */
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }

    .modal-backdrop.show {
        display: flex;
    }

    .modal-box {
        background-color: #FFFFFF;
        border-radius: 12px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        padding: 24px;
        position: relative;
    }

    .modal-close {
        position: absolute;
        top: 18px;
        right: 18px;
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #697C70;
    }

    /* Alert Success */
    .alert-success {
        background-color: #EDF5EC;
        border: 1px solid #C4E2CF;
        color: #286C4B;
        padding: 12px 18px;
        border-radius: 8px;
        font-size: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    @media (max-width: 1024px) {
        .connection-card {
            flex-direction: column;
            align-items: flex-start;
        }
        .connection-divider {
            display: none;
        }
        .connection-right {
            align-items: flex-start;
        }
        .warning-banner {
            flex-direction: column;
            align-items: flex-start;
        }
        .results-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')

    <!-- Success Message Notification -->
    @if (session('success'))
        <div class="alert-success">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.style.display='none'" style="background:none; border:none; color:inherit; cursor:pointer; font-weight:bold;">&times;</button>
        </div>
    @endif

    <!-- Page Title & Prototype Indicator -->
    <div class="sync-page-header">
        <div class="sync-page-title">
            <h2>Sinkronisasi Data SiHalal</h2>
            <p>Tarik dan perbarui data pengajuan SiHalal untuk mendukung proses pendampingan dan sertifikasi halal.</p>
        </div>
        <div class="badge-prototype">
            PROTOTYPE • DATA SIMULASI
        </div>
    </div>

    <!-- 1. Card: Status Koneksi SiHalal -->
    <div class="connection-card">
        <!-- Left: Status Koneksi -->
        <div class="connection-left">
            <div class="connection-icon-box">
                <img src="{{ asset('images/icon-sync-large.svg') }}" alt="Sync" width="24" height="24">
            </div>
            <div class="connection-info">
                <h3>Koneksi SiHalal</h3>
                <span class="connection-status-pill">Integrasi belum tersedia</span>
                <p>Belum terhubung ke layanan SiHalal.</p>
                <p>Tidak ada pertukaran data nyata pada prototype ini.</p>
            </div>
        </div>

        <!-- Vertical Divider -->
        <div class="connection-divider"></div>

        <!-- Middle: Sinkronisasi Terakhir -->
        <div class="connection-middle">
            <div class="connection-middle-top">
                <img src="{{ asset('images/icon-history.svg') }}" alt="History" width="16" height="16">
                <span>Sinkronisasi terakhir (simulasi)</span>
            </div>
            <div class="connection-timestamp">
                @if ($latestSync)
                    {{ \Carbon\Carbon::parse($latestSync->waktu_sinkronisasi)->isoFormat('D MMMM Y, HH.mm [WIB]') }}
                @else
                    Belum ada riwayat
                @endif
            </div>
            <div class="connection-summary-sub">
                @if ($latestSync)
                    @php
                        $totalData = $latestSync->jumlah_data_baru + $latestSync->jumlah_data_diperbarui + $latestSync->jumlah_data_gagal;
                        $successData = $latestSync->jumlah_data_baru + $latestSync->jumlah_data_diperbarui;
                    @endphp
                    @if ($latestSync->status_sinkronisasi === 'Selesai')
                        Selesai • {{ $successData }} data diproses
                    @elseif ($latestSync->status_sinkronisasi === 'Sebagian')
                        Selesai sebagian • {{ $successData }} dari {{ $totalData }} data diproses
                    @else
                        Gagal • Layanan tidak merespons
                    @endif
                @else
                    Sistem belum pernah dijalankan
                @endif
            </div>
        </div>

        <!-- Right: Action Button -->
        <div class="connection-right">
            <form method="POST" action="{{ route('admin.sinkronisasi.simulasi') }}">
                @csrf
                <button type="submit" class="btn-sync-now" title="Klik untuk melakukan simulasi pencatatan sinkronisasi baru">
                    <img src="{{ asset('images/icon-sync.svg') }}" alt="Sync" width="18" height="18" style="filter: brightness(0) invert(1);">
                    <span>Sinkronkan Sekarang</span>
                </button>
            </form>
            <div class="btn-sync-subtext">
                Simulasi pencatatan riwayat baru ke database
            </div>
        </div>
    </div>

    <!-- 2. Warning / Error Card: Integrasi Tidak Tersedia -->
    <div class="warning-banner">
        <div class="warning-left">
            <div class="warning-circle">!</div>
            <div class="warning-text">
                <h4>Integrasi SiHalal tidak tersedia</h4>
                <p>Contoh percobaan sinkronisasi otomatis belum dapat tersambung langsung ke server pusat SiHalal BPJPH.</p>
                <p>Data tersimpan di sistem lokal tetap aman. Lanjutkan pengelolaan secara mandiri melalui Data Pengajuan.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.pengajuan') }}" class="btn-manual-manage">
                <img src="{{ asset('images/icon-file.svg') }}" alt="File" width="16" height="16">
                <span>Kelola Data Secara Manual</span>
                <img src="{{ asset('images/icon-chevron.svg') }}" alt="Arrow" width="14" height="14" style="transform: rotate(-90deg);">
            </a>
        </div>
    </div>

    <!-- 3. Cards: Ringkasan Hasil Sinkronisasi -->
    <div class="results-section-header">
        <div class="results-section-title">Hasil pemrosesan terakhir</div>
        <div class="results-section-sub">
            @if ($latestSync)
                Sesi {{ \Carbon\Carbon::parse($latestSync->waktu_sinkronisasi)->isoFormat('D MMM Y • HH.mm [WIB]') }} • {{ $latestSync->jumlah_data_baru + $latestSync->jumlah_data_diperbarui + $latestSync->jumlah_data_gagal }} data
            @else
                Belum ada data pemrosesan
            @endif
        </div>
    </div>

    <div class="results-grid">
        <!-- Card 1: Data baru -->
        <div class="result-card">
            <div>
                <div class="result-label">Data baru</div>
                <div class="result-number-group">
                    <div class="result-number">{{ $latestSync->jumlah_data_baru ?? 0 }}</div>
                    <div class="result-sub">Pengajuan baru ditambahkan</div>
                </div>
            </div>
            <div class="result-icon-box" style="background-color: #EAF3ED;">
                <img src="{{ asset('images/card-sync-new.svg') }}" alt="Data baru" width="36" height="36">
            </div>
        </div>

        <!-- Card 2: Data diperbarui -->
        <div class="result-card">
            <div>
                <div class="result-label">Data diperbarui</div>
                <div class="result-number-group">
                    <div class="result-number">{{ $latestSync->jumlah_data_diperbarui ?? 0 }}</div>
                    <div class="result-sub">Pengajuan diperbarui</div>
                </div>
            </div>
            <div class="result-icon-box" style="background-color: #FBF3DF;">
                <img src="{{ asset('images/card-sync-updated.svg') }}" alt="Data diperbarui" width="36" height="36">
            </div>
        </div>

        <!-- Card 3: Gagal diproses -->
        <div class="result-card">
            <div>
                <div class="result-label">Gagal diproses</div>
                <div class="result-number-group">
                    <div class="result-number failed">{{ $latestSync->jumlah_data_gagal ?? 0 }}</div>
                    <div class="result-sub">Data tidak lengkap; perlu ditinjau</div>
                </div>
            </div>
            <div class="result-icon-box" style="background-color: #FBEFEA;">
                <img src="{{ asset('images/card-sync-failed.svg') }}" alt="Gagal" width="36" height="36">
            </div>
        </div>
    </div>

    <!-- 4. Table: Riwayat Sinkronisasi -->
    <div class="card">
        <div class="card-header-clean">
            <div class="card-title-group">
                <h3>Riwayat sinkronisasi</h3>
                <p>Contoh hasil sinkronisasi, bukan aktivitas SiHalal yang sebenarnya.</p>
            </div>
            <div class="badge-prototype">
                DATA CONTOH
            </div>
        </div>

        <div class="table-container">
            <table class="clean-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Jumlah data baru</th>
                        <th>Jumlah data diperbarui</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatSinkronisasi as $item)
                        <tr>
                            <td style="font-weight: 500; color: #203D2E;">
                                {{ \Carbon\Carbon::parse($item->waktu_sinkronisasi)->isoFormat('D MMM Y') }}
                            </td>
                            <td style="color: #203D2E;">
                                {{ \Carbon\Carbon::parse($item->waktu_sinkronisasi)->isoFormat('HH.mm [WIB]') }}
                            </td>
                            <td style="color: #203D2E;">
                                {{ $item->jumlah_data_baru }}
                            </td>
                            <td style="color: #203D2E;">
                                {{ $item->jumlah_data_diperbarui }}
                            </td>
                            <td>
                                @if ($item->status_sinkronisasi === 'Selesai')
                                    <span class="sync-status-selesai">Selesai</span>
                                @elseif ($item->status_sinkronisasi === 'Sebagian')
                                    <span class="sync-status-sebagian">Sebagian</span>
                                @else
                                    <span class="sync-status-gagal">Gagal</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn-action-pill" onclick="showDetailModal({{ json_encode($item) }})">
                                    <span style="color: #103F35; font-weight: 600;">Lihat detail</span>
                                    <img src="{{ asset('images/icon-chevron.svg') }}" alt="Detail" width="12" height="12" style="transform: rotate(-90deg);">
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: #7B8B7F;">
                                Belum ada riwayat sinkronisasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px; display: flex; justify-content: space-between; font-size: 10px; color: #697C70;">
            <div>Menampilkan {{ $riwayatSinkronisasi->count() }} sesi</div>
            <div>Seluruh waktu dalam WIB</div>
        </div>
    </div>

    <!-- Modal Detail Sesi -->
    <div class="modal-backdrop" id="detailModal">
        <div class="modal-box">
            <button type="button" class="modal-close" onclick="closeDetailModal()">&times;</button>
            <h3 style="font-size: 16px; font-weight: 600; color: #203D2E; margin-bottom: 14px;">Detail Sesi Sinkronisasi</h3>
            
            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 12px; color: #203B2E;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #EDF1EA; padding-bottom: 8px;">
                    <span style="color: #697C70;">ID Sesi:</span>
                    <strong id="modalId"></strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #EDF1EA; padding-bottom: 8px;">
                    <span style="color: #697C70;">Waktu Sinkronisasi:</span>
                    <strong id="modalTime"></strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #EDF1EA; padding-bottom: 8px;">
                    <span style="color: #697C70;">Status:</span>
                    <strong id="modalStatus"></strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #EDF1EA; padding-bottom: 8px;">
                    <span style="color: #697C70;">Jumlah Data Baru:</span>
                    <span id="modalBaru"></span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #EDF1EA; padding-bottom: 8px;">
                    <span style="color: #697C70;">Jumlah Data Diperbarui:</span>
                    <span id="modalDiperbarui"></span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #EDF1EA; padding-bottom: 8px;">
                    <span style="color: #697C70;">Jumlah Data Gagal:</span>
                    <span id="modalGagal"></span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px; padding-top: 4px;">
                    <span style="color: #697C70;">Catatan / Pesan Kesalahan:</span>
                    <div id="modalPesan" style="background-color: #F8FAF7; padding: 10px; border-radius: 6px; font-size: 11px; color: #555;"></div>
                </div>
            </div>

            <div style="margin-top: 20px; text-align: right;">
                <button type="button" class="btn-action-secondary" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>

@endsection

@section('extra-js')
<script>
    function showDetailModal(item) {
        document.getElementById('modalId').innerText = '#' + item.id_sinkronisasi;
        document.getElementById('modalTime').innerText = item.waktu_sinkronisasi;
        document.getElementById('modalStatus').innerText = item.status_sinkronisasi;
        document.getElementById('modalBaru').innerText = item.jumlah_data_baru + ' data';
        document.getElementById('modalDiperbarui').innerText = item.jumlah_data_diperbarui + ' data';
        document.getElementById('modalGagal').innerText = item.jumlah_data_gagal + ' data';
        document.getElementById('modalPesan').innerText = item.pesan_kesalahan ? item.pesan_kesalahan : 'Tidak ada pesan kesalahan. Pemrosesan normal.';
        
        document.getElementById('detailModal').classList.add('show');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.remove('show');
    }

    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDetailModal();
        }
    });
</script>
@endsection
