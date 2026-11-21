<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SI-HALAL • LPH UINSA')</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-green: #103F35;
            --primary-dark: #1D4B3D;
            --active-green: #285B47;
            --accent-gold: #D3AF61;
            --accent-gold-light: #FAF2DE;
            --accent-gold-text: #997323;
            --bg-light: #F5F8F7;
            --bg-card: #FFFFFF;
            --border-card: #E4EAE6;
            --border-light: #E5EBE6;
            --border-row: #EDF1EA;
            --text-dark: #1B3529;
            --text-title: #203D2E;
            --text-regular: #203B2E;
            --text-muted: #6A7C70;
            --text-muted-light: #7B897E;
            --text-sidebar-muted: #BDD0C8;
            --text-sidebar-nav: #C5D6CE;
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 248px;
            background-color: var(--primary-green);
            color: #FFFFFF;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 25px 24px 20px 24px;
            display: flex;
            align-items: flex-start;
            gap: 13px;
        }

        .sidebar-gold-bar {
            width: 3px;
            height: 45px;
            background-color: var(--accent-gold);
            border-radius: 2px;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .sidebar-brand-title {
            font-size: 23px;
            font-weight: 700;
            line-height: 33px;
            color: #FFFFFF;
        }

        .sidebar-brand-sub {
            font-size: 10px;
            font-weight: 500;
            line-height: 15px;
            color: var(--text-sidebar-muted);
        }

        .sidebar-divider {
            height: 1px;
            background-color: #31594C;
            margin: 0 24px 18px 24px;
        }

        .sidebar-section-title {
            font-size: 10px;
            font-weight: 600;
            line-height: 15px;
            letter-spacing: 0.05em;
            color: #94B0A3;
            padding: 0 24px 10px 24px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex-grow: 1;
        }

        .sidebar-nav-item a {
            display: flex;
            align-items: center;
            height: 42px;
            padding: 0 12px;
            border-radius: 8px;
            position: relative;
            transition: all 0.15s ease-in-out;
            color: var(--text-sidebar-nav);
            font-size: 12px;
            font-weight: 500;
            line-height: 17px;
        }

        .sidebar-nav-item a:hover {
            background-color: rgba(40, 91, 71, 0.4);
            color: #FFFFFF;
        }

        .sidebar-nav-item.active a {
            background-color: var(--active-green);
            color: #FFFFFF;
            font-weight: 600;
        }

        .sidebar-nav-item.active .active-indicator {
            position: absolute;
            left: 0;
            top: 11px;
            width: 3px;
            height: 20px;
            background-color: #DBB968;
            border-radius: 2px;
        }

        .sidebar-nav-icon {
            width: 19px;
            height: 19px;
            margin-right: 12px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-nav-badge {
            margin-left: auto;
            background-color: #D5B467;
            color: #183F31;
            font-size: 11px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 5px;
            line-height: 16px;
        }

        .sidebar-bottom {
            padding: 16px;
            margin-top: auto;
        }

        .sidebar-help-card {
            background-color: var(--primary-dark);
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            position: relative;
            margin-bottom: 12px;
        }

        .sidebar-help-top {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .sidebar-help-title {
            font-size: 12px;
            font-weight: 600;
            color: #FFFFFF;
        }

        .sidebar-help-sub {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #B7CFC1;
        }

        .sidebar-footer-text {
            font-size: 10px;
            color: #9BB8A9;
            padding: 0 8px;
            line-height: 15px;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: 248px;
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header Styling */
        .header {
            height: 88px;
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .header-title-box h1 {
            font-size: 20px;
            font-weight: 600;
            line-height: 29px;
            color: #1C342A;
        }

        .header-title-box p {
            font-size: 11px;
            font-weight: 400;
            line-height: 16px;
            color: #6E8075;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-search {
            width: 300px;
            height: 42px;
            background-color: #F6F8F6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 0 14px;
            gap: 10px;
        }

        .header-search input {
            border: none;
            background: transparent;
            outline: none;
            font-size: 11px;
            color: var(--text-dark);
            width: 100%;
            font-family: inherit;
        }

        .header-search input::placeholder {
            color: #819087;
        }

        .header-bell {
            position: relative;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
        }

        .header-bell-dot {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 6px;
            height: 6px;
            background-color: #C7A354;
            border-radius: 50%;
        }

        .header-divider {
            width: 1px;
            height: 34px;
            background-color: #E1E8E3;
        }

        .header-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            cursor: pointer;
            user-select: none;
            padding: 6px 8px;
            border-radius: 8px;
            transition: background 0.15s;
        }

        .header-profile:hover {
            background-color: #F6F8F6;
        }

        .header-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background-color: #EAF2EC;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .header-user-info {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .header-user-name {
            font-size: 12px;
            font-weight: 600;
            line-height: 17px;
            color: #203A2D;
        }

        .header-user-role {
            font-size: 10px;
            font-weight: 400;
            line-height: 15px;
            color: #7B897E;
        }

        .header-chevron {
            width: 17px;
            height: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        /* User Dropdown Menu */
        .profile-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 220px;
            background: #FFFFFF;
            border: 1px solid var(--border-card);
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            padding: 8px 0;
            display: none;
            z-index: 200;
        }

        .profile-dropdown.show {
            display: block;
        }

        .profile-dropdown-header {
            padding: 8px 16px 10px 16px;
            border-bottom: 1px solid var(--border-light);
            margin-bottom: 4px;
        }

        .profile-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 16px;
            font-size: 12px;
            color: var(--text-dark);
            width: 100%;
            background: none;
            border: none;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.15s;
        }

        .profile-dropdown-item:hover {
            background-color: #F5F8F7;
            color: #B91C1C;
        }

        /* Content Area */
        .content {
            padding: 24px 32px 40px 32px;
            flex-grow: 1;
        }

        /* Welcome Banner */
        .welcome-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .welcome-text h2 {
            font-size: 25px;
            font-weight: 600;
            line-height: 36px;
            color: #1B3529;
        }

        .welcome-text p {
            font-size: 12px;
            font-weight: 400;
            line-height: 17px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .date-badge {
            background-color: #FFFFFF;
            border-radius: 7px;
            height: 36px;
            padding: 0 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--border-card);
            font-size: 12px;
            font-weight: 500;
            color: #435E4D;
        }

        /* Stat Cards */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .stat-card {
            background-color: #FFFFFF;
            border: 1px solid #E3EAE5;
            border-radius: 10px;
            height: 102px;
            padding: 14px 18px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .stat-card-title {
            font-size: 12px;
            font-weight: 500;
            line-height: 17px;
            color: #677B6D;
        }

        .stat-card-number {
            font-size: 29px;
            font-weight: 600;
            line-height: 38px;
            color: #203D2E;
        }

        .stat-card-sub {
            font-size: 10px;
            font-weight: 400;
            line-height: 15px;
            color: #869287;
        }

        .stat-card-icon {
            position: absolute;
            right: 18px;
            top: 17px;
            width: 37px;
            height: 37px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Shared Card Component */
        .card {
            background: #FFFFFF;
            border: 1px solid var(--border-card);
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            margin-bottom: 24px;
        }

        .card-header-clean {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .card-title-group h3 {
            font-size: 15px;
            font-weight: 600;
            line-height: 22px;
            color: #203B2E;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title-group p {
            font-size: 11px;
            font-weight: 400;
            line-height: 16px;
            color: #7B8B7F;
            margin-top: 2px;
        }

        .card-badge {
            background-color: #EFF4EE;
            color: #386346;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 5px;
        }

        /* Clean Table */
        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table.clean-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        table.clean-table thead tr {
            background-color: #F5F8F4;
            height: 34px;
        }

        table.clean-table thead th {
            font-size: 10px;
            font-weight: 500;
            line-height: 15px;
            color: #7B8A7D;
            padding: 8px 16px;
            border: none;
        }

        table.clean-table tbody tr {
            height: 38px;
            border-bottom: 1px solid #EDF1EA;
            transition: background 0.15s;
        }

        table.clean-table tbody tr:nth-child(even) {
            background-color: #FBFCFA;
        }

        table.clean-table tbody tr:hover {
            background-color: #F1F6F3;
        }

        table.clean-table td {
            padding: 8px 16px;
            font-size: 11px;
            line-height: 16px;
            color: var(--text-dark);
            vertical-align: middle;
        }

        .code-reg {
            color: #54735B;
            font-weight: 500;
        }

        .biz-name {
            font-weight: 500;
            color: #334D3B;
        }

        .product-name {
            color: #7B897C;
        }

        .date-col {
            color: #7B897C;
        }

        /* Status Pills */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 500;
            line-height: 15px;
            width: fit-content;
        }

        .status-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
        }

        .status-selesai {
            background-color: #EDF5EE;
            color: #41805A;
        }
        .status-selesai .status-dot { background-color: #41805A; }

        .status-sedang-diperiksa {
            background-color: #EDF4F8;
            color: #527E91;
        }
        .status-sedang-diperiksa .status-dot { background-color: #527E91; }

        .status-menunggu-verifikasi {
            background-color: #FBF3DF;
            color: #9C772E;
        }
        .status-menunggu-verifikasi .status-dot { background-color: #9C772E; }

        .status-perlu-revisi, .status-perlu-perbaikan {
            background-color: #FBEEE9;
            color: #AB715B;
        }
        .status-perlu-revisi .status-dot, .status-perlu-perbaikan .status-dot { background-color: #AB715B; }

        .status-ditugaskan {
            background-color: #FAF2DE;
            color: #89661E;
        }
        .status-ditugaskan .status-dot { background-color: #89661E; }

        /* Action Buttons / Badges in Table */
        .btn-action-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 5px;
            color: #2B6948;
            background: transparent;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-action-pill:hover {
            background-color: #EAF2EC;
        }

        .btn-action-primary {
            background-color: #103F35;
            color: #FFFFFF;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-action-primary:hover {
            background-color: #175246;
        }

        .btn-action-secondary {
            background-color: #F5F8F4;
            color: #2B6948;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 600;
            border: 1px solid #E1E8DF;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-action-secondary:hover {
            background-color: #EBF2EC;
        }

        /* Footer */
        .footer {
            margin-top: 24px;
            padding-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10px;
            color: #7C8B80;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                width: 72px;
            }
            .sidebar-brand-title, .sidebar-brand-sub, .sidebar-section-title, .sidebar-help-card, .sidebar-footer-text, .sidebar-nav-item span {
                display: none;
            }
            .main-wrapper {
                margin-left: 72px;
            }
            .sidebar-nav-item a {
                justify-content: center;
                padding: 0;
            }
            .sidebar-nav-icon {
                margin-right: 0;
            }
        }
    </style>
    @yield('extra-css')
</head>
<body>

    <!-- Shared Sidebar -->
    @include('partials.sidebar')

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Shared Header -->
        @include('partials.header')

        <!-- Main Content -->
        <main class="content">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profileToggle = document.getElementById('profileToggle');
            const profileDropdown = document.getElementById('profileDropdown');

            if (profileToggle && profileDropdown) {
                profileToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('show');
                });

                document.addEventListener('click', function (e) {
                    if (!profileToggle.contains(e.target)) {
                        profileDropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>
    @yield('extra-js')
</body>
</html>
