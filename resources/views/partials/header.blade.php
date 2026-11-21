@php
    $currentUser = Auth::user();
    $role = $currentUser->role ?? 'Admin LPH';
@endphp

<header class="header">
    <div class="header-title-box">
        @if ($role === 'Admin LPH')
            <h1>Dashboard</h1>
            <p>Administrasi pendampingan & sertifikasi halal</p>
        @else
            <h1>Dashboard Auditor</h1>
            <p>Pendampingan dan sertifikasi halal • Ruang Auditor</p>
        @endif
    </div>

    <div class="header-actions">
        <!-- Search Bar -->
        <div class="header-search">
            <img src="{{ asset('images/icon-search.svg') }}" alt="Cari" width="18" height="18">
            <input type="text" placeholder="{{ $role === 'Admin LPH' ? 'Cari pengajuan atau pelaku usaha...' : 'Cari penugasan saya...' }}">
        </div>

        <!-- Notification Bell -->
        <div class="header-bell" title="Notifikasi">
            <img src="{{ asset('images/icon-bell.svg') }}" alt="Notifikasi" width="23" height="23">
            @if(isset($unreadNotifications) && $unreadNotifications > 0)
                <span class="header-bell-dot"></span>
            @endif
        </div>

        <!-- Vertical Divider -->
        <div class="header-divider"></div>

        <!-- User Profile Dropdown Toggle -->
        <div class="header-profile" id="profileToggle">
            <div class="header-avatar">
                @if ($role === 'Admin LPH')
                    <img src="{{ asset('images/admin-avatar.svg') }}" alt="Avatar" width="38" height="38">
                @else
                    <img src="{{ asset('images/auditor-avatar.svg') }}" alt="Avatar" width="38" height="38">
                @endif
            </div>
            <div class="header-user-info">
                <span class="header-user-name">{{ $currentUser->nama }}</span>
                <span class="header-user-role">{{ $role === 'Admin LPH' ? 'Administrator' : 'Auditor' }}</span>
            </div>
            <div class="header-chevron">
                <img src="{{ asset('images/icon-chevron.svg') }}" alt="Chevron" width="17" height="17">
            </div>

            <!-- Profile Dropdown Content -->
            <div class="profile-dropdown" id="profileDropdown">
                <div class="profile-dropdown-header">
                    <div style="font-weight: 600; font-size: 13px; color: #1B3529;">{{ $currentUser->nama }}</div>
                    <div style="font-size: 11px; color: #6A7C70;">{{ $currentUser->email }}</div>
                    <div style="margin-top: 4px; display: inline-block; font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 4px; background: #EFF4EE; color: #2B6948;">
                        {{ $currentUser->role }}
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="profile-dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
