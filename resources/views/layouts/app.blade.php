
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard') - SLOTIN</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #172554;
            --primary-light: #263b75;
            --accent: #f87b1b;
            --background: #f5f7fb;
            --sidebar-width: 250px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--background);
            font-family: Arial, sans-serif;
            color: #1e293b;
            margin: 0;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--primary);
            color: white;
            padding: 25px 16px;
            z-index: 1000;

            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* BRAND */
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 10px 30px;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            background: var(--accent);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        /* SIDEBAR MENU */
        .sidebar-label {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 20px 10px 12px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 13px;
            width: 100%;
            color: #cbd5e1;
            text-decoration: none;
            padding: 13px 14px;
            margin-bottom: 7px;
            border-radius: 9px;
            transition: .2s;
            font-size: 14px;
        }

        .sidebar-link i {
            font-size: 18px;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            color: white;
            background: var(--accent);
        }

        /* SYSTEM INFO */
        .system-info {
            padding: 12px 10px;
            font-size: 12px;
            color: #94a3b8;
        }

        .system-info span {
            color: white;
        }

        /* LOGOUT */
        .sidebar-bottom {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,.12);
        }

        .logout-form {
            width: 100%;
            margin: 0;
        }

        .logout-button {
            display: flex;
            align-items: center;
            gap: 13px;
            width: 100%;
            padding: 13px 14px;
            background: transparent;
            border: none;
            border-radius: 9px;
            color: #fca5a5;
            font-size: 14px;
            text-align: left;
            cursor: pointer;
            transition: .2s;
        }

        .logout-button i {
            font-size: 18px;
        }

        .logout-button:hover {
            background: rgba(239, 68, 68, .15);
            color: white;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* TOPBAR */
        .topbar {
            height: 75px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .page-title {
            font-size: 21px;
            font-weight: 700;
            margin: 0;
        }

        /* CONTENT */
        .content-wrapper {
            padding: 30px;
        }

        .stat-card,
        .section-card {
            background: white;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: 22px;
            height: 100%;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .stat-value {
            font-size: 27px;
            font-weight: 800;
            margin: 15px 0 4px;
        }

        .stat-label {
            color: #64748b;
            font-size: 13px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .zone-card {
            background: white;
            border: 1px solid #e9edf3;
            border-radius: 12px;
            padding: 18px;
            height: 100%;
        }

        .zone-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .table th {
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            white-space: nowrap;
        }

        .table td {
            font-size: 13px;
            vertical-align: middle;
        }

        .btn-primary-custom {
            background: var(--primary);
            color: white;
            border: none;
        }

        .btn-primary-custom:hover {
            background: var(--primary-light);
            color: white;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                min-height: auto;
            }

            .main-content {
                margin-left: 0;
            }

            .content-wrapper {
                padding: 18px;
            }

            .topbar {
                padding: 0 18px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <!-- BRAND -->
        <div class="brand">
            <div class="brand-icon">
                <i class="bi bi-p-square-fill"></i>
            </div>

            <div class="brand-name">SLOTIN</div>
        </div>

        <!-- MAIN MENU -->
        <div class="sidebar-label">Main Menu</div>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('scan.index') }}"
           class="sidebar-link {{ request()->routeIs('scan.*') ? 'active' : '' }}">
            <i class="bi bi-camera-video"></i>
            <span>Scan Parkir</span>
        </a>

        <a href="{{ route('zones.index') }}"
           class="sidebar-link {{ request()->routeIs('zones.*') ? 'active' : '' }}">
            <i class="bi bi-geo-alt"></i>
            <span>Data Zona</span>
        </a>

        <a href="{{ route('history.index') }}"
           class="sidebar-link {{ request()->routeIs('history.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat Scan</span>
        </a>

        <!-- SYSTEM -->
        <div class="sidebar-label">System</div>

        <div class="system-info">
            SLOTIN Smart Parking<br>
            <span>MVP Version 1.0</span>
        </div>

        <!-- LOGOUT -->
        <div class="sidebar-bottom">

            <form action="{{ route('logout') }}"
                  method="POST"
                  class="logout-form">

                @csrf

                <button type="submit" class="logout-button">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>

            </form>

        </div>

    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">

            <h5 class="page-title">
                @yield('page-title', 'Dashboard')
            </h5>

            <div class="d-flex align-items-center gap-3">

                <div class="text-end">
                    <div class="fw-bold small">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </div>

                    <div class="text-muted" style="font-size: 11px;">
                        Admin
                    </div>
                </div>

                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                     style="width: 40px; height: 40px;">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>

            </div>

        </header>

        <!-- PAGE CONTENT -->
        <div class="content-wrapper">
            @yield('content')
        </div>

    </main>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>
</html>
