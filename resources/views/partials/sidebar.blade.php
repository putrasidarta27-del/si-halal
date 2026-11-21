@php
    $currentUser = Auth::user();
    $role = $currentUser->role ?? 'Admin LPH';
    $currentRoute = Route::currentRouteName();
@endphp

<aside class="sidebar">
    <!-- Brand Header -->
    <div class="sidebar-header">
        <div class="sidebar-gold-bar"></div>
        <div>
            <div class="sidebar-brand-title">LPH UINSA</div>
            <div class="sidebar-brand-sub">UIN Sunan Ampel Surabaya</div>
        </div>
    </div>

    <!-- Divider -->
    <div class="sidebar-divider"></div>

    <!-- Role-Based Section Title -->
    @if ($role === 'Admin LPH')
        <div class="sidebar-section-title">RUANG KERJA ADMIN</div>
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item {{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    @if ($currentRoute === 'admin.dashboard')
                        <div class="active-indicator"></div>
                    @endif
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-grid.svg') }}" alt="Dashboard" width="19" height="19">
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-nav-item {{ $currentRoute === 'admin.sinkronisasi' ? 'active' : '' }}">
                <a href="{{ route('admin.sinkronisasi') }}">
                    @if ($currentRoute === 'admin.sinkronisasi')
                        <div class="active-indicator"></div>
                    @endif
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-sync.svg') }}" alt="Sinkronisasi" width="19" height="19">
                    </span>
                    <span>Sinkronisasi SiHalal</span>
                </a>
            </li>
            <li class="sidebar-nav-item {{ request()->routeIs('admin.pengajuan*') ? 'active' : '' }}">
                <a href="{{ route('admin.pengajuan') }}">
                    @if (request()->routeIs('admin.pengajuan*'))
                        <div class="active-indicator"></div>
                    @endif
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-file.svg') }}" alt="Data Pengajuan" width="19" height="19">
                    </span>
                    <span>Data Pengajuan</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#dokumen">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-folder.svg') }}" alt="Dokumen Pengajuan" width="19" height="19">
                    </span>
                    <span>Dokumen Pengajuan</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#pembayaran">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-wallet.svg') }}" alt="Verifikasi Pembayaran" width="19" height="19">
                    </span>
                    <span>Verifikasi Pembayaran</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#penugasan">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-users.svg') }}" alt="Penugasan Auditor" width="19" height="19">
                    </span>
                    <span>Penugasan Auditor</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#monitoring">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-monitor.svg') }}" alt="Monitoring Pemeriksaan" width="19" height="19">
                    </span>
                    <span>Monitoring Pemeriksaan</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#riwayat">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-history.svg') }}" alt="Riwayat Revisi" width="19" height="19">
                    </span>
                    <span>Riwayat Revisi</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#notifikasi">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-bell.svg') }}" alt="Notifikasi" width="19" height="19">
                    </span>
                    <span>Notifikasi</span>
                    @if(isset($unreadNotifications) && $unreadNotifications > 0)
                        <span class="sidebar-nav-badge">{{ $unreadNotifications }}</span>
                    @endif
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#laporan">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-chart.svg') }}" alt="Laporan Pemeriksaan" width="19" height="19">
                    </span>
                    <span>Laporan Pemeriksaan</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#administrasi">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-settings.svg') }}" alt="Data Administrasi" width="19" height="19">
                    </span>
                    <span>Data Administrasi</span>
                </a>
            </li>
        </ul>
    @else
        <!-- Auditor Workspace -->
        <div class="sidebar-section-title">RUANG KERJA AUDITOR</div>
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item {{ $currentRoute === 'auditor.dashboard' ? 'active' : '' }}">
                <a href="{{ route('auditor.dashboard') }}">
                    @if ($currentRoute === 'auditor.dashboard')
                        <div class="active-indicator"></div>
                    @endif
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-grid.svg') }}" alt="Dashboard" width="19" height="19">
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#penugasan-saya">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-file.svg') }}" alt="Penugasan Saya" width="19" height="19">
                    </span>
                    <span>Penugasan Saya</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#pemeriksaan">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-monitor.svg') }}" alt="Pemeriksaan" width="19" height="19">
                    </span>
                    <span>Pemeriksaan</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#catatan-perbaikan">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-folder.svg') }}" alt="Catatan Perbaikan" width="19" height="19">
                    </span>
                    <span>Catatan Perbaikan</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#pemeriksaan-ulang">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-sync.svg') }}" alt="Pemeriksaan Ulang" width="19" height="19">
                    </span>
                    <span>Pemeriksaan Ulang</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#riwayat-revisi">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-history.svg') }}" alt="Riwayat Revisi" width="19" height="19">
                    </span>
                    <span>Riwayat Revisi</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#laporan-pemeriksaan">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-chart.svg') }}" alt="Laporan Pemeriksaan" width="19" height="19">
                    </span>
                    <span>Laporan Pemeriksaan</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#notifikasi">
                    <span class="sidebar-nav-icon">
                        <img src="{{ asset('images/icon-bell.svg') }}" alt="Notifikasi" width="19" height="19">
                    </span>
                    <span>Notifikasi</span>
                    @if(isset($unreadNotifications) && $unreadNotifications > 0)
                        <span class="sidebar-nav-badge">{{ $unreadNotifications }}</span>
                    @endif
                </a>
            </li>
        </ul>
    @endif

    <!-- Bottom Help & Footer -->
    <div class="sidebar-bottom">
        <div class="sidebar-help-card">
            <div class="sidebar-help-top">
                <img src="{{ asset('images/icon-help.svg') }}" alt="Bantuan" width="18" height="18">
                <span class="sidebar-help-title">Butuh bantuan?</span>
            </div>
            <div class="sidebar-help-sub">
                <span>Hubungi pengelola sistem</span>
                <img src="{{ asset('images/icon-arrow-sm.svg') }}" alt="Arrow" width="16" height="16">
            </div>
        </div>
        <div class="sidebar-footer-text">
            Sistem Internal LPH UINSA
        </div>
    </div>
</aside>
