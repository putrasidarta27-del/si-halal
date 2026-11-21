@extends('layouts.dashboard')

@section('title', 'Data Pengajuan • SI-HALAL LPH UINSA')

@section('extra-css')
<style>
    /* Breadcrumb */
    .breadcrumb-row {
        font-size: 12px;
        color: #6A7C70;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .breadcrumb-row a {
        color: #203B2E;
        text-decoration: none;
    }
    .breadcrumb-row a:hover {
        color: #103F35;
        text-decoration: underline;
    }
    .breadcrumb-separator {
        color: #A3B5AA;
        font-size: 11px;
    }

    /* Filter & Search Bar */
    .filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 20px 18px 20px;
        flex-wrap: wrap;
    }
    .search-box {
        flex: 1;
        min-width: 280px;
        height: 42px;
        background: #FFFFFF;
        border: 1px solid #E3EAE5;
        border-radius: 7px;
        display: flex;
        align-items: center;
        padding: 0 14px;
        gap: 10px;
    }
    .search-box input {
        border: none;
        outline: none;
        width: 100%;
        font-family: inherit;
        font-size: 12px;
        color: #203B2E;
    }
    .search-box input::placeholder {
        color: #7C8B80;
    }
    .filter-select {
        height: 42px;
        padding: 0 16px;
        background: #FFFFFF;
        border: 1px solid #E3EAE5;
        border-radius: 7px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 500;
        color: #203B2E;
        outline: none;
        cursor: pointer;
    }
    .btn-tambah-manual {
        height: 42px;
        padding: 0 18px;
        background: #103F35;
        color: #FFFFFF;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: background 0.15s ease;
        white-space: nowrap;
    }
    .btn-tambah-manual:hover {
        background: #195247;
        color: #FFFFFF;
    }

    /* Detail / Action Buttons in Table */
    .btn-table-detail {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        background-color: #EDF4EF;
        color: #103F35;
        font-size: 11px;
        font-weight: 500;
        border-radius: 5px;
        text-decoration: none;
        transition: background 0.15s;
    }
    .btn-table-detail:hover {
        background-color: #DEECE2;
    }
    .btn-table-edit {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        background-color: #FFFFFF;
        color: #103F35;
        border: 1px solid #E3EAE5;
        font-size: 11px;
        font-weight: 500;
        border-radius: 5px;
        text-decoration: none;
        transition: all 0.15s;
    }
    .btn-table-edit:hover {
        background-color: #F6F8F6;
        border-color: #CBD9D0;
    }

    /* Pagination */
    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-top: 1px solid #EDF1EA;
        font-size: 11px;
        color: #6A7C70;
        flex-wrap: wrap;
        gap: 12px;
    }
    .pagination-links {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 500;
        color: #6A7C70;
        background: #FFFFFF;
        border-radius: 5px;
        text-decoration: none;
        min-width: 28px;
        height: 28px;
        transition: all 0.15s;
    }
    .page-btn:hover:not(.disabled) {
        background: #F0F4F1;
        color: #103F35;
    }
    .page-btn.active {
        background: #103F35;
        color: #FFFFFF;
        font-weight: 600;
    }
    .page-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Detail Page Styles */
    .identity-card {
        background: #FFFFFF;
        border: 1px solid var(--border-card);
        border-radius: 10px;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        flex-wrap: wrap;
        gap: 20px;
    }
    .identity-block {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .identity-code {
        font-size: 13px;
        font-weight: 600;
        color: #103F35;
    }
    .identity-name {
        font-size: 16px;
        font-weight: 600;
        color: #203B2E;
    }
    .identity-label {
        font-size: 10px;
        color: #6A7C70;
        font-weight: 400;
    }
    .identity-val {
        font-size: 12px;
        font-weight: 500;
        color: #203B2E;
    }

    /* Tabs Styling */
    .tabs-header {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #E3EAE5;
        padding: 0 24px;
        background: #FFFFFF;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .tab-nav-item {
        padding: 16px 20px;
        font-size: 12px;
        font-weight: 500;
        color: #6A7C70;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: all 0.15s;
        display: inline-block;
    }
    .tab-nav-item:hover {
        color: #103F35;
    }
    .tab-nav-item.active {
        color: #103F35;
        font-weight: 600;
        border-bottom-color: #103F35;
        background-color: #F0F6F1;
    }

    /* Detail Grid Layout */
    .tab-body {
        padding: 24px;
        background: #FFFFFF;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .section-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .section-title-row h4 {
        font-size: 15px;
        font-weight: 600;
        color: #203B2E;
    }
    .section-title-row p {
        font-size: 11px;
        color: #6A7C70;
        margin-top: 2px;
    }
    .info-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
        padding-bottom: 14px;
        margin-bottom: 14px;
        border-bottom: 1px solid #EDF1EE;
    }
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .info-label {
        font-size: 11px;
        font-weight: 500;
        color: #6A7C70;
    }
    .info-value {
        font-size: 13px;
        font-weight: 500;
        color: #203B2E;
    }
    .detail-footer-strip {
        padding: 14px 24px;
        background-color: #F6F8F6;
        border-radius: 0 0 12px 12px;
        font-size: 10px;
        color: #6A7C70;
        border-top: 1px solid #E3EAE5;
    }

    /* Form Styles */
    .form-card {
        background: #FFFFFF;
        border: 1px solid var(--border-card);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .form-card-header {
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #EDF1EE;
    }
    .form-card-header h3 {
        font-size: 15px;
        font-weight: 600;
        color: #203B2E;
    }
    .form-card-header p {
        font-size: 11px;
        color: #6A7C70;
        margin-top: 2px;
    }
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 16px;
    }
    .form-grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
        margin-bottom: 16px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 16px;
    }
    .form-label {
        font-size: 11px;
        font-weight: 600;
        color: #203B2E;
    }
    .form-label .required-star {
        color: #B91C1C;
    }
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #D5DFD8;
        border-radius: 7px;
        font-family: inherit;
        font-size: 12px;
        color: #203B2E;
        background: #FFFFFF;
        outline: none;
        transition: border 0.15s;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: #103F35;
        box-shadow: 0 0 0 2px rgba(16, 63, 53, 0.1);
    }
    .form-input.is-invalid, .form-select.is-invalid, .form-textarea.is-invalid {
        border-color: #DC2626;
        background-color: #FEF2F2;
    }
    .invalid-feedback {
        font-size: 10px;
        color: #DC2626;
        margin-top: 3px;
        font-weight: 500;
    }
    .form-input[readonly] {
        background-color: #F5F8F6;
        color: #526B58;
        font-weight: 600;
        cursor: not-allowed;
    }
    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

    /* Error Banner */
    .alert-danger-box {
        background-color: #FEF2F2;
        border: 1px solid #FCA5A5;
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 24px;
        color: #991B1B;
        font-size: 12px;
    }
    .alert-danger-box ul {
        margin-top: 8px;
        padding-left: 20px;
    }

    /* Success Confirmation Screen (Frame 04.6) */
    .success-card {
        background: #FFFFFF;
        border: 1px solid var(--border-card);
        border-radius: 12px;
        padding: 40px 32px;
        max-width: 680px;
        margin: 20px auto 40px auto;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .success-icon-wrap {
        width: 64px;
        height: 64px;
        margin: 0 auto 20px auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .success-title {
        font-size: 22px;
        font-weight: 700;
        color: #103F35;
        margin-bottom: 8px;
    }
    .success-subtitle {
        font-size: 13px;
        color: #6A7C70;
        line-height: 20px;
        max-width: 480px;
        margin: 0 auto 24px auto;
    }
    .success-summary-box {
        background: #F8FAF9;
        border: 1px solid #E3EAE5;
        border-radius: 10px;
        padding: 20px;
        text-align: left;
        margin-bottom: 28px;
    }
    .success-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #EDF1EE;
        font-size: 12px;
    }
    .success-summary-row:last-child {
        border-bottom: none;
    }
    .summary-key {
        color: #6A7C70;
        font-weight: 500;
    }
    .summary-val {
        color: #203B2E;
        font-weight: 600;
    }
    .success-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-grid-2, .form-grid-3, .info-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }
</style>
@endsection

@section('content')

    {{-- ========================================================================= --}}
    {{-- 1. VIEW MODE: DAFTAR PENGAJUAN (Frame 04) --}}
    {{-- ========================================================================= --}}
    @if ($viewMode === 'index')

        <!-- Page Header -->
        <div class="welcome-row">
            <div class="welcome-text">
                <h2>Data Pengajuan</h2>
                <p>Kelola data pelaku usaha, produk, dan bahan pengajuan sertifikasi halal</p>
            </div>
            <div class="date-badge">
                <img src="{{ asset('images/icon-history.svg') }}" alt="Tanggal" width="16" height="16">
                <span>Data sistem • {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
            </div>
        </div>

        @if (session('error'))
            <div style="background-color: #FEF2F2; border: 1px solid #F87171; color: #991B1B; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 12px;">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div style="background-color: #EDF5EE; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 12px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- Card: Daftar Pengajuan -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="card-header-clean" style="padding: 18px 20px 10px 20px; margin-bottom: 0;">
                <div class="card-title-group">
                    <h3>
                        Daftar Pengajuan
                        <span class="card-badge">{{ number_format($totalCount ?? $pengajuanList->total(), 0, ',', '.') }}</span>
                    </h3>
                </div>
                <div style="font-size: 10px; color: #7B8B7F;">
                    Data tersimpan di sistem lokal LPH UINSA
                </div>
            </div>

            <!-- Filter & Search Form -->
            <form action="{{ route('admin.pengajuan') }}" method="GET" class="filter-bar">
                <div class="search-box">
                    <img src="{{ asset('images/icon-search.svg') }}" alt="Search" width="18" height="18">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nomor pengajuan atau nama pelaku usaha">
                </div>

                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="" {{ empty($status) ? 'selected' : '' }}>Semua status</option>
                    @foreach (config('pilihan.status_pengajuan', ['Menunggu Verifikasi', 'Ditugaskan', 'Sedang Diperiksa', 'Perlu Perbaikan', 'Selesai']) as $st)
                        <option value="{{ $st }}" {{ ($status ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>

                <a href="{{ route('admin.pengajuan.create') }}" class="btn-tambah-manual">
                    <span>+  Tambah Data Manual</span>
                </a>
            </form>

            <!-- Table -->
            <div class="table-container">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th style="padding-left: 20px; width: 170px;">Nomor Pengajuan</th>
                            <th style="width: 230px;">Nama Pelaku Usaha</th>
                            <th>Nama Produk</th>
                            <th style="width: 140px;">Tanggal Pengajuan</th>
                            <th style="width: 170px;">Status Pengajuan</th>
                            <th style="padding-right: 20px; width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuanList as $p)
                            <tr>
                                <td style="padding-left: 20px;">
                                    <span class="code-reg" style="font-weight: 600; color: #103F35;">{{ $p->nomor_pengajuan }}</span>
                                </td>
                                <td class="biz-name" style="font-weight: 500; color: #203B2E;">
                                    {{ $p->nama_usaha }}
                                </td>
                                <td class="product-name" style="color: #4A5B50;">
                                    {{ $p->nama_produk ?? '-' }}
                                </td>
                                <td class="date-col">
                                    {{ \Carbon\Carbon::parse($p->tanggal_pengajuan)->isoFormat('D MMM Y') }}
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($p->status_pengajuan) {
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
                                        <span>{{ $p->status_pengajuan }}</span>
                                    </span>
                                </td>
                                <td style="padding-right: 20px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <a href="{{ route('admin.pengajuan.show', $p->id_pengajuan) }}" class="btn-table-detail">
                                            Detail
                                        </a>
                                        <a href="{{ route('admin.pengajuan.edit', $p->id_pengajuan) }}" class="btn-table-edit">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 36px 20px; color: #7B8B7F;">
                                    Tidak ada data pengajuan yang sesuai dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="pagination-bar">
                <div>
                    Menampilkan {{ $pengajuanList->firstItem() ?? 0 }}–{{ $pengajuanList->lastItem() ?? 0 }} dari {{ $pengajuanList->total() }} pengajuan
                </div>

                @if ($pengajuanList->hasPages())
                    <div class="pagination-links">
                        @if ($pengajuanList->onFirstPage())
                            <span class="page-btn disabled">Sebelumnya</span>
                        @else
                            <a href="{{ $pengajuanList->previousPageUrl() }}" class="page-btn">Sebelumnya</a>
                        @endif

                        @foreach ($pengajuanList->getUrlRange(1, $pengajuanList->lastPage()) as $page => $url)
                            @if ($page == $pengajuanList->currentPage())
                                <span class="page-btn active">{{ $page }}</span>
                            @elseif ($page <= 3 || $page > $pengajuanList->lastPage() - 2 || abs($page - $pengajuanList->currentPage()) <= 1)
                                <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                            @elseif ($page == 4 && $pengajuanList->currentPage() > 5)
                                <span class="page-btn disabled">…</span>
                            @endif
                        @endforeach

                        @if ($pengajuanList->hasMorePages())
                            <a href="{{ $pengajuanList->nextPageUrl() }}" class="page-btn">Berikutnya</a>
                        @else
                            <span class="page-btn disabled">Berikutnya</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    {{-- ========================================================================= --}}
    {{-- 2. VIEW MODE: DETAIL PENGAJUAN (Frames 04.1, 04.2, 04.3) --}}
    {{-- ========================================================================= --}}
    @elseif ($viewMode === 'show')

        <!-- Breadcrumb -->
        <div class="breadcrumb-row">
            <a href="{{ route('admin.pengajuan') }}">Data Pengajuan</a>
            <span class="breadcrumb-separator">/</span>
            <span style="color: #103F35; font-weight: 600;">{{ $pengajuan->nomor_pengajuan }}</span>
            <span class="breadcrumb-separator">/</span>
            <span>Detail pengajuan</span>
        </div>

        <!-- Page Header -->
        <div class="welcome-row">
            <div class="welcome-text">
                <h2>Detail Pengajuan</h2>
                <p>Tinjau data pelaku usaha, produk, dan bahan dalam satu pengajuan.</p>
            </div>
            <div>
                <a href="{{ route('admin.pengajuan') }}" class="btn-action-secondary" style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 12px;">
                    <img src="{{ asset('images/icon-arrow-sm.svg') }}" alt="Back" width="14" height="14" style="transform: rotate(180deg);">
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        <!-- Card: Identitas Pengajuan -->
        <div class="identity-card">
            <div class="identity-block">
                <span class="identity-code">{{ $pengajuan->nomor_pengajuan }}</span>
                <span class="identity-name">{{ $pelaku->nama_usaha ?? '-' }}</span>
            </div>

            <div class="identity-block">
                <span class="identity-label">Tanggal Pengajuan</span>
                <span class="identity-val">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->isoFormat('D MMMM Y') }}</span>
            </div>

            <div class="identity-block">
                <span class="identity-label">Status Pengajuan</span>
                <div>
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
                </div>
            </div>
        </div>

        <!-- Card: Rincian Pengajuan Tabs -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="tabs-header">
                <a href="{{ route('admin.pengajuan.show', [$pengajuan->id_pengajuan, 'tab' => 'pelaku_usaha']) }}" class="tab-nav-item {{ $activeTab === 'pelaku_usaha' ? 'active' : '' }}">
                    Data Pelaku Usaha
                </a>
                <a href="{{ route('admin.pengajuan.show', [$pengajuan->id_pengajuan, 'tab' => 'produk']) }}" class="tab-nav-item {{ $activeTab === 'produk' ? 'active' : '' }}">
                    Data Produk
                </a>
                <a href="{{ route('admin.pengajuan.show', [$pengajuan->id_pengajuan, 'tab' => 'bahan']) }}" class="tab-nav-item {{ $activeTab === 'bahan' ? 'active' : '' }}">
                    Data Bahan
                </a>
            </div>

            <div class="tab-body">
                {{-- TAB 1: DATA PELAKU USAHA (04.1) --}}
                @if ($activeTab === 'pelaku_usaha')
                    <div class="section-title-row">
                        <div>
                            <h4>Informasi Pelaku Usaha</h4>
                            <p>Identitas dan kontak pelaku usaha yang mengajukan sertifikasi.</p>
                        </div>
                        <a href="{{ route('admin.pengajuan.edit', $pengajuan->id_pengajuan) }}" class="btn-action-primary" style="padding: 8px 16px; font-size: 11px; display: inline-flex; align-items: center; gap: 6px;">
                            <span>Edit Data</span>
                        </a>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <span class="info-label">Nama Pelaku Usaha</span>
                            <span class="info-value">{{ $pelaku->nama_usaha ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nama Penanggung Jawab</span>
                            <span class="info-value">{{ $pelaku->nama_penanggung_jawab ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <span class="info-label">Nomor Induk Berusaha (NIB)</span>
                            <span class="info-value">{{ $pelaku->nib ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Skala Usaha</span>
                            <span class="info-value">{{ $pelaku->skala_usaha ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <span class="info-label">Email Usaha</span>
                            <span class="info-value">{{ $pelaku->email ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nomor Telepon</span>
                            <span class="info-value">{{ $pelaku->no_telepon ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="info-row" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                        <div class="info-item">
                            <span class="info-label">Alamat Usaha</span>
                            <span class="info-value">{{ $pelaku->alamat ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Kota / Provinsi</span>
                            <span class="info-value">{{ $pelaku->kota_provinsi ?? '-' }}</span>
                        </div>
                    </div>

                {{-- TAB 2: DATA PRODUK (04.2) --}}
                @elseif ($activeTab === 'produk')
                    <div class="section-title-row">
                        <div>
                            <h4>Informasi Produk</h4>
                            <p>Rincian produk yang didaftarkan untuk sertifikasi halal.</p>
                        </div>
                        <a href="{{ route('admin.pengajuan.edit', $pengajuan->id_pengajuan) }}" class="btn-action-primary" style="padding: 8px 16px; font-size: 11px; display: inline-flex; align-items: center; gap: 6px;">
                            <span>Edit Data</span>
                        </a>
                    </div>

                    @if ($produk)
                        <div class="info-row">
                            <div class="info-item">
                                <span class="info-label">Nama Produk</span>
                                <span class="info-value">{{ $produk->nama_produk }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Merek Dagang</span>
                                <span class="info-value">{{ $produk->merek_dagang ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-item">
                                <span class="info-label">Jenis Produk</span>
                                <span class="info-value">{{ $produk->jenis_produk ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Varian Produk</span>
                                <span class="info-value">{{ $produk->varian_produk ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-item">
                                <span class="info-label">Kemasan / Berat Bersih</span>
                                <span class="info-value">{{ $produk->kemasan_berat_bersih ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Lokasi Produksi</span>
                                <span class="info-value">{{ $produk->lokasi_produksi ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="info-row" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0; grid-template-columns: 1fr;">
                            <div class="info-item">
                                <span class="info-label">Deskripsi Produk</span>
                                <span class="info-value">{{ $produk->deskripsi ?? '-' }}</span>
                            </div>
                        </div>
                    @else
                        <div style="padding: 24px; text-align: center; color: #7B8B7F; font-size: 12px;">
                            Belum ada data produk untuk pengajuan ini.
                        </div>
                    @endif

                {{-- TAB 3: DATA BAHAN (04.3) --}}
                @elseif ($activeTab === 'bahan')
                    <div class="section-title-row">
                        <div>
                            <h4>Informasi Bahan</h4>
                            <p>Daftar bahan yang digunakan dalam proses pembuatan produk.</p>
                        </div>
                        <a href="{{ route('admin.pengajuan.edit', $pengajuan->id_pengajuan) }}" class="btn-action-primary" style="padding: 8px 16px; font-size: 11px; display: inline-flex; align-items: center; gap: 6px;">
                            <span>Edit Data</span>
                        </a>
                    </div>

                    <div class="table-container" style="border: 1px solid #EDF1EE; border-radius: 8px; margin-top: 8px;">
                        <table class="clean-table">
                            <thead>
                                <tr>
                                    <th style="padding-left: 16px; width: 220px;">Nama Bahan</th>
                                    <th style="width: 220px;">Merek / Produsen</th>
                                    <th style="width: 170px;">Jenis Bahan</th>
                                    <th style="padding-right: 16px;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bahanList as $b)
                                    <tr>
                                        <td style="padding-left: 16px; font-weight: 600; color: #203B2E;">
                                            {{ $b->nama_bahan }}
                                        </td>
                                        <td style="color: #4A5B50;">
                                            {{ $b->merek_produsen ?? '-' }}
                                        </td>
                                        <td>
                                            @php
                                                $badgeBg = match($b->jenis_bahan) {
                                                    'Bahan Baku' => '#EDF5EE',
                                                    'Bahan Penolong' => '#FAF2DE',
                                                    'Bahan Tambahan' => '#EDF4F8',
                                                    default => '#F5F8F6'
                                                };
                                                $badgeColor = match($b->jenis_bahan) {
                                                    'Bahan Baku' => '#41805A',
                                                    'Bahan Penolong' => '#89661E',
                                                    'Bahan Tambahan' => '#527E91',
                                                    default => '#6A7C70'
                                                };
                                            @endphp
                                            <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 500; background-color: {{ $badgeBg }}; color: {{ $badgeColor }};">
                                                {{ $b->jenis_bahan ?? '-' }}
                                            </span>
                                        </td>
                                        <td style="padding-right: 16px; color: #7B8B7F; font-size: 11px;">
                                            {{ $b->keterangan ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 24px; color: #7B8B7F; font-size: 12px;">
                                            Belum ada data bahan yang didaftarkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="detail-footer-strip">
                Data pengajuan sertifikasi halal • Dikelola oleh Admin LPH UINSA (Sumber: {{ $pengajuan->sumber_data }})
            </div>
        </div>

    {{-- ========================================================================= --}}
    {{-- 3. VIEW MODE: FORM TAMBAH / EDIT DATA (Frames 04.4 & 04.5) --}}
    {{-- ========================================================================= --}}
    @elseif ($viewMode === 'form')

        <!-- Breadcrumb -->
        <div class="breadcrumb-row">
            <a href="{{ route('admin.pengajuan') }}">Data Pengajuan</a>
            <span class="breadcrumb-separator">/</span>
            @if ($isEdit)
                <a href="{{ route('admin.pengajuan.show', $pengajuan->id_pengajuan) }}">{{ $nomorPengajuan }}</a>
                <span class="breadcrumb-separator">/</span>
                <span>Edit data pengajuan</span>
            @else
                <span>Tambah Data Manual</span>
            @endif
        </div>

        <!-- Page Header -->
        <div class="welcome-row">
            <div class="welcome-text">
                <h2>{{ $isEdit ? 'Edit Data Pengajuan' : 'Tambah Data Pengajuan Manual' }}</h2>
                <p>{{ $isEdit ? 'Perbarui informasi pelaku usaha, produk, dan bahan pengajuan sertifikasi.' : 'Lengkapi data pelaku usaha, produk, dan bahan secara manual ke dalam sistem.' }}</p>
            </div>
            <div>
                <a href="{{ $isEdit ? route('admin.pengajuan.show', $pengajuan->id_pengajuan) : route('admin.pengajuan') }}" class="btn-action-secondary" style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 12px;">
                    <img src="{{ asset('images/icon-arrow-sm.svg') }}" alt="Back" width="14" height="14" style="transform: rotate(180deg);">
                    <span>Batal</span>
                </a>
            </div>
        </div>

        <!-- Validation Error Alert Banner -->
        @if (isset($errors) && $errors->any())
            <div class="alert-danger-box">
                <div style="font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <span>Terdapat beberapa isian wajib yang belum lengkap atau tidak valid:</span>
                </div>
                <ul>
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $isEdit ? route('admin.pengajuan.update', $pengajuan->id_pengajuan) : route('admin.pengajuan.store') }}" method="POST">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <!-- CARD 1: INFORMASI PENGAJUAN & PELAKU USAHA -->
            <div class="form-card">
                <div class="form-card-header">
                    <h3>1. Informasi Pengajuan & Pelaku Usaha</h3>
                    <p>Nomor registrasi sistem dan identitas pelaku usaha pemohon</p>
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label class="form-label">Nomor Pengajuan</label>
                        <input type="text" class="form-input" value="{{ $nomorPengajuan }}" readonly>
                        <span style="font-size: 10px; color: #7B8B7F;">Otomatis dibuat oleh sistem (format REG-2026-NNNNN)</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Pengajuan <span class="required-star">*</span></label>
                        <input type="date" name="tanggal_pengajuan" class="form-input @error('tanggal_pengajuan') is-invalid @enderror" value="{{ old('tanggal_pengajuan', $pengajuan->tanggal_pengajuan ?? date('Y-m-d')) }}" required>
                        @error('tanggal_pengajuan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    @if ($isEdit)
                        <div class="form-group">
                            <label class="form-label">Status Pengajuan</label>
                            <select name="status_pengajuan" class="form-select @error('status_pengajuan') is-invalid @enderror">
                                @php
                                    $statusList = config('pilihan.status_pengajuan', ['Menunggu Verifikasi', 'Ditugaskan', 'Sedang Diperiksa', 'Perlu Perbaikan', 'Selesai']);
                                    $currStatus = old('status_pengajuan', $pengajuan->status_pengajuan ?? '');
                                    if (!empty($currStatus) && !in_array($currStatus, $statusList)) {
                                        array_unshift($statusList, $currStatus);
                                    }
                                @endphp
                                @foreach ($statusList as $st)
                                    <option value="{{ $st }}" {{ $currStatus === $st ? 'selected' : '' }}>
                                        {{ $st }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_pengajuan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <div class="form-group">
                            <label class="form-label">Status Awal</label>
                            <input type="text" class="form-input" value="Menunggu Verifikasi" readonly>
                            <span style="font-size: 10px; color: #7B8B7F;">Status awal otomatis: Menunggu Verifikasi</span>
                        </div>
                    @endif
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Pelaku Usaha / Usaha <span class="required-star">*</span></label>
                        <input type="text" name="nama_usaha" class="form-input @error('nama_usaha') is-invalid @enderror" value="{{ old('nama_usaha', $pelaku->nama_usaha ?? '') }}" placeholder="Contoh: CV Berkah Pangan Nusantara" required>
                        @error('nama_usaha') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Penanggung Jawab <span class="required-star">*</span></label>
                        <input type="text" name="nama_penanggung_jawab" class="form-input @error('nama_penanggung_jawab') is-invalid @enderror" value="{{ old('nama_penanggung_jawab', $pelaku->nama_penanggung_jawab ?? '') }}" placeholder="Contoh: Ahmad Fauzi" required>
                        @error('nama_penanggung_jawab') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label class="form-label">Nomor Induk Berusaha (NIB)</label>
                        <input type="text" name="nib" inputmode="numeric" pattern="[0-9]*" maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-input @error('nib') is-invalid @enderror" value="{{ old('nib', $pelaku->nib ?? '') }}" placeholder="Hanya angka, maks 20 digit">
                        @error('nib') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Skala Usaha</label>
                        <select name="skala_usaha" class="form-select @error('skala_usaha') is-invalid @enderror">
                            <option value="">-- Pilih Skala Usaha --</option>
                            @php
                                $skalaList = config('pilihan.skala_usaha', ['Usaha Mikro', 'Usaha Kecil', 'Usaha Menengah']);
                                $currSkala = old('skala_usaha', $pelaku->skala_usaha ?? '');
                                if (!empty($currSkala) && !in_array($currSkala, $skalaList)) {
                                    array_unshift($skalaList, $currSkala);
                                }
                            @endphp
                            @foreach ($skalaList as $sk)
                                <option value="{{ $sk }}" {{ $currSkala === $sk ? 'selected' : '' }}>{{ $sk }}</option>
                            @endforeach
                        </select>
                        @error('skala_usaha') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Usaha</label>
                        @php
                            $jenisUsahaList = config('pilihan.jenis_usaha', ['Makanan Olahan', 'Minuman', 'Bakery dan Roti', 'Katering', 'Bumbu dan Saus', 'Daging Olahan', 'Lainnya']);
                            $oldJenisUsahaVal = old('jenis_usaha', $pelaku->jenis_usaha ?? '');
                            $isJenisUsahaCustom = !empty($oldJenisUsahaVal) && !in_array($oldJenisUsahaVal, $jenisUsahaList);
                            if ($isJenisUsahaCustom && $oldJenisUsahaVal !== 'Lainnya') {
                                array_unshift($jenisUsahaList, $oldJenisUsahaVal);
                            }
                        @endphp
                        <select name="jenis_usaha" id="jenis_usaha_select" class="form-select @error('jenis_usaha') is-invalid @enderror">
                            <option value="">-- Pilih Jenis Usaha --</option>
                            @foreach ($jenisUsahaList as $ju)
                                <option value="{{ $ju }}" {{ $oldJenisUsahaVal === $ju ? 'selected' : '' }}>{{ $ju }}</option>
                            @endforeach
                        </select>
                        <div id="jenis_usaha_custom_wrap" style="display: {{ $oldJenisUsahaVal === 'Lainnya' ? 'block' : 'none' }}; margin-top: 6px;">
                            <input type="text" name="jenis_usaha_custom" id="jenis_usaha_custom" class="form-input @error('jenis_usaha_custom') is-invalid @enderror" value="{{ old('jenis_usaha_custom') }}" placeholder="Ketik jenis usaha lainnya...">
                            @error('jenis_usaha_custom') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        @error('jenis_usaha') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Email Pelaku Usaha</label>
                        <input type="email" name="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email', $pelaku->email ?? '') }}" placeholder="nama@usaha.com">
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor Telepon / WhatsApp</label>
                        <input type="tel" name="no_telepon" maxlength="20" class="form-input @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon', $pelaku->no_telepon ?? '') }}" placeholder="Contoh: 081234567890">
                        @error('no_telepon') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Alamat Usaha</label>
                        <input type="text" name="alamat" class="form-input @error('alamat') is-invalid @enderror" value="{{ old('alamat', $pelaku->alamat ?? '') }}" placeholder="Jl. Raya No. XX, Kecamatan">
                        @error('alamat') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kota / Provinsi</label>
                        @php
                            $kotaList = config('pilihan.kota_provinsi', []);
                            $oldKotaVal = old('kota_provinsi', $pelaku->kota_provinsi ?? '');
                            $isKotaCustom = !empty($oldKotaVal) && !in_array($oldKotaVal, $kotaList);
                            if ($isKotaCustom && $oldKotaVal !== 'Lainnya') {
                                array_unshift($kotaList, $oldKotaVal);
                            }
                        @endphp
                        <select name="kota_provinsi" id="kota_provinsi_select" class="form-select @error('kota_provinsi') is-invalid @enderror">
                            <option value="">-- Pilih Kabupaten / Kota --</option>
                            @foreach ($kotaList as $kt)
                                <option value="{{ $kt }}" {{ $oldKotaVal === $kt ? 'selected' : '' }}>{{ $kt }}</option>
                            @endforeach
                        </select>
                        <div id="kota_provinsi_custom_wrap" style="display: {{ $oldKotaVal === 'Lainnya' ? 'block' : 'none' }}; margin-top: 6px;">
                            <input type="text" name="kota_provinsi_custom" id="kota_provinsi_custom" class="form-input @error('kota_provinsi_custom') is-invalid @enderror" value="{{ old('kota_provinsi_custom') }}" placeholder="Ketik nama kabupaten/kota dan provinsi lainnya...">
                            @error('kota_provinsi_custom') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        @error('kota_provinsi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- CARD 2: DATA PRODUK -->
            <div class="form-card">
                <div class="form-card-header">
                    <h3>2. Data Produk</h3>
                    <p>Rincian produk yang didaftarkan untuk pengujian sertifikasi halal</p>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Produk <span class="required-star">*</span></label>
                        <input type="text" name="nama_produk" class="form-input @error('nama_produk') is-invalid @enderror" value="{{ old('nama_produk', $produk->nama_produk ?? '') }}" placeholder="Contoh: Keripik Pisang Gurih" required>
                        @error('nama_produk') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Merek Dagang</label>
                        <input type="text" name="merek_dagang" class="form-input @error('merek_dagang') is-invalid @enderror" value="{{ old('merek_dagang', $produk->merek_dagang ?? '') }}" placeholder="Contoh: Pisang Enak">
                        @error('merek_dagang') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Jenis Produk <span class="required-star">*</span></label>
                        @php
                            $jenisProdukList = config('pilihan.jenis_produk', []);
                            $oldJenisProdukVal = old('jenis_produk', $produk->jenis_produk ?? '');
                            $isJenisProdukCustom = !empty($oldJenisProdukVal) && !in_array($oldJenisProdukVal, $jenisProdukList);
                            if ($isJenisProdukCustom && $oldJenisProdukVal !== 'Lainnya') {
                                array_unshift($jenisProdukList, $oldJenisProdukVal);
                            }
                        @endphp
                        <select name="jenis_produk" id="jenis_produk_select" class="form-select @error('jenis_produk') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Produk --</option>
                            @foreach ($jenisProdukList as $jp)
                                <option value="{{ $jp }}" {{ $oldJenisProdukVal === $jp ? 'selected' : '' }}>{{ $jp }}</option>
                            @endforeach
                        </select>
                        <div id="jenis_produk_custom_wrap" style="display: {{ $oldJenisProdukVal === 'Lainnya' ? 'block' : 'none' }}; margin-top: 6px;">
                            <input type="text" name="jenis_produk_custom" id="jenis_produk_custom" class="form-input @error('jenis_produk_custom') is-invalid @enderror" value="{{ old('jenis_produk_custom') }}" placeholder="Ketik jenis produk lainnya...">
                            @error('jenis_produk_custom') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        @error('jenis_produk') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Varian Produk</label>
                        <input type="text" name="varian_produk" class="form-input @error('varian_produk') is-invalid @enderror" value="{{ old('varian_produk', $produk->varian_produk ?? '') }}" placeholder="Contoh: Original, Cokelat, Pedas Manis">
                        @error('varian_produk') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Kemasan & Berat Bersih</label>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <input type="text" name="kemasan_jenis" class="form-input" style="flex: 1;" placeholder="Bentuk kemasan (mis. Standing pouch, Botol)" value="{{ old('kemasan_jenis', $kemasanJenis ?? '') }}">
                            <input type="number" name="kemasan_angka" step="any" min="0" class="form-input @error('kemasan_angka') is-invalid @enderror" style="width: 100px;" placeholder="Angka" value="{{ old('kemasan_angka', $kemasanAngka ?? '') }}">
                            <select name="kemasan_satuan" class="form-select" style="width: 85px;">
                                @foreach (config('pilihan.satuan_kemasan', ['g', 'kg', 'ml', 'L', 'pcs']) as $u)
                                    <option value="{{ $u }}" {{ old('kemasan_satuan', $kemasanSatuan ?? 'g') === $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span style="font-size: 10px; color: #7B8B7F;">Isikan bentuk kemasan (opsional) serta isian angka dan satuan volume/berat.</span>
                        @error('kemasan_angka') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Lokasi Produksi</label>
                        <input type="text" name="lokasi_produksi" class="form-input @error('lokasi_produksi') is-invalid @enderror" value="{{ old('lokasi_produksi', $produk->lokasi_produksi ?? '') }}" placeholder="Alamat fasilitas pengolahan / dapur produksi">
                        @error('lokasi_produksi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Produk</label>
                    <textarea name="deskripsi" class="form-textarea @error('deskripsi') is-invalid @enderror" rows="3" placeholder="Jelaskan karakteristik umum atau proses pembuatan produk...">{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea>
                    @error('deskripsi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- CARD 3: DATA BAHAN -->
            <div class="form-card">
                <div class="form-card-header">
                    <h3>3. Data Bahan</h3>
                    <p>Bahan baku atau bahan penolong yang digunakan dalam komposisi produk</p>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Bahan <span class="required-star">*</span></label>
                        <input type="text" name="nama_bahan" class="form-input @error('nama_bahan') is-invalid @enderror" value="{{ old('nama_bahan', $bahan->nama_bahan ?? '') }}" placeholder="Contoh: Pisang Kepok Segar" required>
                        @error('nama_bahan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Merek / Produsen</label>
                        <input type="text" name="merek_produsen" class="form-input @error('merek_produsen') is-invalid @enderror" value="{{ old('merek_produsen', $bahan->merek_produsen ?? '') }}" placeholder="Contoh: Petani lokal / PT Boga Halal">
                        @error('merek_produsen') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Jenis Bahan <span class="required-star">*</span></label>
                        @php
                            $jenisBahanList = config('pilihan.jenis_bahan', ['Bahan Baku', 'Bahan Penolong', 'Bahan Tambahan', 'Lainnya']);
                            $oldJenisBahanVal = old('jenis_bahan', $bahan->jenis_bahan ?? 'Bahan Baku');
                            $isJenisBahanCustom = !empty($oldJenisBahanVal) && !in_array($oldJenisBahanVal, $jenisBahanList);
                            if ($isJenisBahanCustom && $oldJenisBahanVal !== 'Lainnya') {
                                array_unshift($jenisBahanList, $oldJenisBahanVal);
                            }
                        @endphp
                        <select name="jenis_bahan" id="jenis_bahan_select" class="form-select @error('jenis_bahan') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Bahan --</option>
                            @foreach ($jenisBahanList as $jb)
                                <option value="{{ $jb }}" {{ $oldJenisBahanVal === $jb ? 'selected' : '' }}>{{ $jb }}</option>
                            @endforeach
                        </select>
                        <div id="jenis_bahan_custom_wrap" style="display: {{ $oldJenisBahanVal === 'Lainnya' ? 'block' : 'none' }}; margin-top: 6px;">
                            <input type="text" name="jenis_bahan_custom" id="jenis_bahan_custom" class="form-input @error('jenis_bahan_custom') is-invalid @enderror" value="{{ old('jenis_bahan_custom') }}" placeholder="Ketik jenis bahan lainnya...">
                            @error('jenis_bahan_custom') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        @error('jenis_bahan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan Bahan</label>
                        <input type="text" name="keterangan_bahan" class="form-input @error('keterangan_bahan') is-invalid @enderror" value="{{ old('keterangan_bahan', $bahan->keterangan ?? '') }}" placeholder="Contoh: Bahan utama tanpa bahan pengawet sintetis">
                        @error('keterangan_bahan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="form-actions">
                <a href="{{ $isEdit ? route('admin.pengajuan.show', $pengajuan->id_pengajuan) : route('admin.pengajuan') }}" class="btn-action-secondary" style="padding: 10px 20px; font-size: 13px;">
                    Batal
                </a>
                <button type="submit" class="btn-action-primary" style="padding: 10px 24px; font-size: 13px;">
                    Simpan Data Pengajuan
                </button>
            </div>
        </form>

    {{-- ========================================================================= --}}
    {{-- 4. VIEW MODE: KONFIRMASI BERHASIL DISIMPAN (Frame 04.6) --}}
    {{-- ========================================================================= --}}
    @elseif ($viewMode === 'success')

        <!-- Breadcrumb -->
        <div class="breadcrumb-row">
            <a href="{{ route('admin.pengajuan') }}">Data Pengajuan</a>
            <span class="breadcrumb-separator">/</span>
            <span style="color: #103F35; font-weight: 600;">{{ $pengajuan->nomor_pengajuan }}</span>
            <span class="breadcrumb-separator">/</span>
            <span>Konfirmasi berhasil</span>
        </div>

        <div class="success-card">
            <div class="success-icon-wrap">
                <img src="{{ asset('images/icon-check-circle.svg') }}" alt="Berhasil" width="60" height="60">
            </div>

            <h2 class="success-title">Data Pengajuan Berhasil Disimpan</h2>
            <p class="success-subtitle">
                @if (session('status_saved') === 'updated')
                    Pembaruan data pengajuan <strong>{{ $pengajuan->nomor_pengajuan }}</strong> telah berhasil disimpan ke dalam database sistem internal LPH UINSA.
                @else
                    Data pengajuan baru dengan nomor registrasi <strong>{{ $pengajuan->nomor_pengajuan }}</strong> telah berhasil dicatat ke dalam database sistem internal LPH UINSA.
                @endif
            </p>

            <div class="success-summary-box">
                <div class="success-summary-row">
                    <span class="summary-key">Nomor Pengajuan</span>
                    <span class="summary-val" style="color: #103F35;">{{ $pengajuan->nomor_pengajuan }}</span>
                </div>
                <div class="success-summary-row">
                    <span class="summary-key">Nama Pelaku Usaha</span>
                    <span class="summary-val">{{ $pelaku->nama_usaha ?? '-' }}</span>
                </div>
                <div class="success-summary-row">
                    <span class="summary-key">Nama Produk</span>
                    <span class="summary-val">{{ $produk->nama_produk ?? '-' }}</span>
                </div>
                <div class="success-summary-row">
                    <span class="summary-key">Tanggal Pengajuan</span>
                    <span class="summary-val">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->isoFormat('D MMMM Y') }}</span>
                </div>
                <div class="success-summary-row">
                    <span class="summary-key">Sumber Data</span>
                    <span class="summary-val">
                        <span style="font-size: 10px; font-weight: 600; color: #526B58; background-color: #EDF3EF; padding: 2px 8px; border-radius: 4px;">
                            {{ $pengajuan->sumber_data }}
                        </span>
                    </span>
                </div>
                <div class="success-summary-row">
                    <span class="summary-key">Status Pengajuan</span>
                    <span class="summary-val">
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
                    </span>
                </div>
            </div>

            <div class="success-actions">
                <a href="{{ route('admin.pengajuan.show', $pengajuan->id_pengajuan) }}" class="btn-action-primary" style="padding: 10px 22px; font-size: 13px;">
                    Lihat Detail Pengajuan
                </a>
                <a href="{{ route('admin.pengajuan') }}" class="btn-action-secondary" style="padding: 10px 22px; font-size: 13px;">
                    Kembali ke Daftar Pengajuan
                </a>
            </div>
        </div>

    @endif

@endsection

@section('extra-js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fungsi helper untuk toggle input custom saat opsi "Lainnya" dipilih
        function setupDropdownLainnya(selectId, wrapId, inputId) {
            const selectEl = document.getElementById(selectId);
            const wrapEl = document.getElementById(wrapId);
            const inputEl = document.getElementById(inputId);

            if (!selectEl || !wrapEl) return;

            selectEl.addEventListener('change', function () {
                if (this.value === 'Lainnya') {
                    wrapEl.style.display = 'block';
                    if (inputEl) inputEl.focus();
                } else {
                    wrapEl.style.display = 'none';
                    if (inputEl) inputEl.value = '';
                }
            });
        }

        setupDropdownLainnya('kota_provinsi_select', 'kota_provinsi_custom_wrap', 'kota_provinsi_custom');
        setupDropdownLainnya('jenis_usaha_select', 'jenis_usaha_custom_wrap', 'jenis_usaha_custom');
        setupDropdownLainnya('jenis_produk_select', 'jenis_produk_custom_wrap', 'jenis_produk_custom');
        setupDropdownLainnya('jenis_bahan_select', 'jenis_bahan_custom_wrap', 'jenis_bahan_custom');
    });
</script>
@endsection
