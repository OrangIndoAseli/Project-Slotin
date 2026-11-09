
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard') - SLOTIN</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }

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

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

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

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
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

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">
                <i class="bi bi-p-square-fill"></i>
            </div>
            <div class="brand-name">SLOTIN</div>
        </div>

        <div class="sidebar-label">Main Menu</div>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid"></i>
            Dashboard
        </a>

        <a href="{{ route('scan.index') }}"
           class="sidebar-link {{ request()->routeIs('scan.*') ? 'active' : '' }}">
            <i class="bi bi-camera-video"></i>
            Scan Parkir
        </a>

        <a href="{{ route('zones.index') }}"
           class="sidebar-link {{ request()->routeIs('zones.*') ? 'active' : '' }}">
            <i class="bi bi-geo-alt"></i>
            Data Zona
        </a>

        <a href="{{ route('history.index') }}"
           class="sidebar-link {{ request()->routeIs('history.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            Riwayat Scan
        </a>

        <div class="sidebar-label mt-5">System</div>

        <div class="px-3 py-3 small text-white-50">
            SLOTIN Smart Parking<br>
            <span class="text-white">MVP Version 1.0</span>
        </div>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <h5 class="page-title">@yield('page-title', 'Dashboard')</h5>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="fw-bold small">Administrator</div>
                    <div class="text-muted" style="font-size: 11px;">Admin</div>
                </div>

                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                     style="width: 40px; height: 40px;">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
            @yield('content')
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>
</html>
