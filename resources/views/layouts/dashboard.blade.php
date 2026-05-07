<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Dashboard') - Our Care</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #6b46c1;
            --sidebar-text: #f3f4f6;
            --sidebar-hover: #7c3aed;
            --sidebar-active: #a78bfa;
            --main-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --accent: #674cbf;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--main-bg);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        .dashboard-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed {
            width: 80px;
            transform: none;
            overflow: hidden;
        }

        .sidebar.collapsed .sidebar-header {
            padding: 14px 10px;
            display: flex;
            justify-content: center;
        }

        .sidebar.collapsed .sidebar-header h2,
        .sidebar.collapsed .sidebar-header p,
        .sidebar.collapsed .nav-section-title {
            display: none;
        }

        .sidebar.collapsed .sidebar-nav {
            padding: 10px 0;
        }

        .sidebar.collapsed .nav-item {
            display: flex;
            align-items: center;
            font-size: 0;
            padding: 12px 0;
            justify-content: center;
        }

        .sidebar.collapsed .nav-item i {
            margin-right: 0;
            font-size: 18px;
        }

        .main-content.collapsed {
            margin-left: 80px;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--sidebar-hover);
            background: linear-gradient(135deg, var(--sidebar-bg), var(--sidebar-hover));
        }

        .sidebar-logo {
            width: 200px;
            max-width: 100%;
            height: auto;
            display: block;
            transition: width 0.3s ease;
        }

        .sidebar.collapsed .sidebar-logo {
            width: 54px;
        }

        .sidebar-header h2 {
            color: var(--sidebar-text);
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-section {
            margin-bottom: 0;
        }

        .nav-section-title {
            display: none;
            padding: 0 20px 10px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 0.5px;
        }

        .nav-item {
            display: block;
            padding: 12px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            position: relative;
        }

        .nav-item:hover {
            background: var(--sidebar-hover);
            border-left-color: var(--sidebar-active);
            color: #ffffff;
        }

        .nav-item.active {
            background: var(--sidebar-active);
            border-left-color: #ffffff;
            color: #ffffff;
            font-weight: 600;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.15);
        }

        .sidebar.collapsed .nav-item.active::after {
            display: none;
            content: none;
        }

        .nav-item i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .top-bar {
            background: var(--card-bg);
            padding: 15px 30px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .menu-toggle {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .menu-toggle:hover {
            background: var(--main-bg);
            color: var(--text-primary);
        }

        .sidebar-toggle-icon {
            width: 28px;
            height: 28px;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }

        .sidebar-toggle-icon .toggle-arrow {
            transition: transform 0.2s ease;
            transform-origin: center;
        }

        .sidebar.collapsed ~ .main-content .sidebar-toggle-icon .toggle-arrow {
            transform: rotate(180deg);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-menu {
            position: relative;
            font-family: inherit;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
        }

        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: 200px;
            z-index: 1000;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-family: inherit;
            color: var(--text-primary);
        }

        .user-dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f1f1;
        }

        .user-dropdown-name {
            display: block;
            color: var(--text-primary);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.35;
        }

        .user-dropdown-role {
            display: block;
            margin-top: 2px;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 400;
            line-height: 1.35;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: 10px 16px;
            border: none;
            background: none;
            color: var(--text-primary);
            text-align: left;
            text-decoration: none;
            font-family: inherit;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.4;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            color: var(--accent);
        }

        .user-details h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .user-details p {
            margin: 0;
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: capitalize;
        }

        .logout-btn {
            padding: 8px 16px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #c82333;
            color: white;
        }

        /* Dashboard Content */
        .dashboard-content {
            padding: 15px 30px;
            max-width: 1600px;
            margin: 0 auto;
        }

        .dashboard-header {
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .dashboard-header p {
            display: none;
            font-size: 16px;
            color: var(--text-secondary);
        }

        /* Stats Cards */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        .stat-icon {
            font-size: 40px;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent), #7c4dff);
            border-radius: 12px;
            color: white;
        }

        .stat-info h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: var(--accent);
            margin: 0;
        }

        /* Action Buttons */
        .dashboard-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .action-btn.primary {
            background: var(--accent);
            color: white;
        }

        .action-btn.primary:hover {
            background: #5a3fa3;
            transform: translateY(-1px);
        }

        .action-btn.secondary {
            background: var(--success);
            color: white;
        }

        .action-btn.secondary:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .dashboard-stats {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 20px;
            }

            .dashboard-content {
                padding: 20px;
            }

            .top-bar {
                padding: 15px 20px;
            }

            .user-info {
                gap: 10px;
            }

            .user-details h4 {
                font-size: 14px;
            }

            .dashboard-actions {
                flex-direction: column;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .dashboard-header h1 {
                font-size: 24px;
            }

            .stat-icon {
                width: 60px;
                height: 60px;
                font-size: 30px;
            }

            .stat-number {
                font-size: 24px;
            }
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('logo3.png') }}" alt="Our Care Logo" class="sidebar-logo">
            </div>

            <nav class="sidebar-nav">
                @php
                    $sessionUser = session('user', []);
                    $accountType = $sessionUser['accounttype'] ?? 'client';
                    $currentPath = request()->path();
                @endphp

                <!-- Common Navigation -->
                @if($accountType !== 'healthcare_worker')
                    <div class="nav-section">
                        <a href="{{ $accountType === 'client' ? url('/client-dashboard') : url('/dashboard') }}" class="nav-item {{ ($accountType === 'client' && request()->is('client-dashboard')) || ($accountType !== 'client' && request()->is('dashboard')) ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </div>
                @endif

                <!-- Role-specific Navigation -->
                @if($accountType === 'healthcare_worker')
                    <div class="nav-section">
                        <a href="{{ url('/healthcare-jobs') }}" class="nav-item {{ request()->is('healthcare-jobs') || request()->is('healthcare-jobs-details/*') ? 'active' : '' }}">
                            <i class="fas fa-briefcase"></i>
                            Job Board
                        </a>
                        <a href="{{ url('/applications') }}" class="nav-item {{ request()->is('applications') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            Applications
                        </a>
                    </div>
                @elseif($accountType === 'admin')
                    <div class="nav-section">
                        <a href="{{ url('/admin-registrations') }}" class="nav-item {{ request()->is('admin-registrations') ? 'active' : '' }}">
                            <i class="fas fa-user-check"></i>
                            Approval List
                        </a>
                        <a href="{{ url('/admin/applications') }}" class="nav-item {{ request()->is('admin/applications') || request()->is('admin/applications/*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            Applications
                        </a>
                        <a href="{{ url('/admin/interviews') }}" class="nav-item {{ request()->is('admin/interviews') || request()->is('admin/interviews/*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check"></i>
                            Interviews
                        </a>
                        <a href="{{ url('/admin/finalization') }}" class="nav-item {{ request()->is('admin/finalization') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-check"></i>
                            Finalization
                        </a>
                        <a href="{{ url('/admin/endorsements/create') }}" class="nav-item {{ request()->is('admin/endorsements*') ? 'active' : '' }}">
                            <i class="fas fa-award"></i>
                            Endorsements
                        </a>
                        <a href="{{ url('/admin/settings') }}" class="nav-item {{ request()->is('admin/settings') || request()->is('admin/settings/*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                    </div>
                @elseif($accountType === 'client')
                    <!-- Client navigation -->
                    <div class="nav-section">
                        @if(isset($sessionUser['approved']) && $sessionUser['approved'] == 1)
                            <a href="{{ url('/client/job-postings/create') }}" class="nav-item {{ request()->is('client/job-postings/create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle"></i>
                                Post Job
                            </a>
                        @endif
                        <a href="{{ url('/client/job-postings') }}" class="nav-item {{ request()->is('client/job-postings') || request()->is('client/job-postings/*/edit') ? 'active' : '' }}">
                            <i class="fas fa-list"></i>
                            My Jobs
                        </a>
                        <a href="{{ url('/client/endorsed-workers') }}" class="nav-item {{ request()->is('client/endorsed-workers') ? 'active' : '' }}">
                            <i class="fas fa-award"></i>
                            Endorsed Workers
                        </a>
                    </div>
                @endif

                <!-- Profile Section -->
                <div class="nav-section">
                    @if($accountType === 'healthcare_worker')
                        <a href="{{ url('/healthcare-profile') }}" class="nav-item {{ request()->is('healthcare-profile') ? 'active' : '' }}">
                            <i class="fas fa-user"></i>
                            My Profile
                        </a>
                    @endif
                </div>
            </nav>
        </aside>

        <!-- Mobile Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Top Bar -->
            <header class="top-bar">
                <button class="menu-toggle" id="menuToggle" type="button" aria-label="Collapse sidebar" title="Collapse sidebar">
                    <svg class="sidebar-toggle-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="4" y="5" width="16" height="14" rx="2"></rect>
                        <path d="M15 5v14"></path>
                        <path class="toggle-arrow" d="M10 9l-3 3 3 3"></path>
                    </svg>
                </button>

                <div class="user-menu" id="userMenuContainer">
                    @php
                        $sessionUser = session('user', []);
                        $displayName = $sessionUser['fullname'] ?? $sessionUser['email'] ?? 'User';
                        $accountTypeLabel = ucfirst(str_replace('_', ' ', $sessionUser['accounttype'] ?? '')) ?: 'Unknown';
                    @endphp

                    <div class="user-avatar" id="userAvatar">
                        {{ strtoupper(substr($displayName, 0, 1)) }}
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-dropdown-header">
                            <span class="user-dropdown-name">{{ $displayName }}</span>
                            <span class="user-dropdown-role">{{ $accountTypeLabel }}</span>
                        </div>
                        @if($accountType === 'healthcare_worker')
                            <a href="{{ url('/healthcare-profile') }}" class="dropdown-item">Your profile</a>
                        @else
                            <a href="{{ url('/profile') }}" class="dropdown-item">Profile</a>
                        @endif
                        <a href="{{ $accountType === 'admin' ? url('/admin/settings') : url('/settings') }}" class="dropdown-item">Settings</a>
                        <form method="POST" action="{{ url('/logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="dropdown-item">Log out</button>
                        </form>
                    </div>
                </div>
            </header>

            @php
                $sessionUser = session('user', []);
                $showPendingApprovalWarning = ($sessionUser['accounttype'] ?? '') === 'client' && isset($sessionUser['approved']) && $sessionUser['approved'] == 0;
            @endphp

            @if($showPendingApprovalWarning)
                <div class="approval-alert" style="margin: 20px 30px 0; padding: 16px 20px; border-radius: 12px; background: #fff4e5; border: 1px solid #ffddb3; color: #8a6d3b; font-weight: 600;">
                    Your account is still pending admin approval. You can browse the dashboard, but some actions may remain restricted until approval is granted.
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </main>
    </div>

    <script>
        // Sidebar toggle control
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.getElementById('mainContent');

        menuToggle.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                sidebarOverlay.classList.toggle('active');
            } else {
                const isCollapsed = sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('collapsed');
                menuToggle.setAttribute('aria-label', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
                menuToggle.setAttribute('title', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
            }
        });

        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
        });

        // Close sidebar on window resize if desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
            }
        });

        // Highlight active navigation item
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navItems = document.querySelectorAll('.nav-item');

            navItems.forEach(item => {
                if (item.getAttribute('href') === currentPath) {
                    item.classList.add('active');
                }
            });

            // user dropdown
            const avatar = document.getElementById('userAvatar');
            const dropdown = document.getElementById('userDropdown');

            if (avatar && dropdown) {
                avatar.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                });

                // close when clicking outside
                document.addEventListener('click', function(event) {
                    if (!avatar.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>
