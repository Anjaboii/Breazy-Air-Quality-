<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Breazy - Air Quality Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6bff;
            --secondary-color: #2c3e50;
            --background-color: #f5f5f5;
            --text-color: #333;
            --white: #ffffff;
            --sensor-color: #4a6bff;
        }

        /* Custom Navbar Styles */
        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
            color: var(--white) !important;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .navbar-nav .nav-link {
            color: var(--white) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 4px;
            transition: all 0.3s ease;
            margin: 0 0.25rem;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
        }

        .navbar-nav .btn-link {
            color: var(--white) !important;
            text-decoration: none;
            padding: 0.5rem 1rem;
            background: none;
            border: none;
            cursor: pointer;
        }

        .navbar-nav .btn-link:hover {
            text-decoration: underline;
        }

        .navbar-toggler {
            border-color: rgba(255,255,255,0.5) !important;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }

        /* Notification styles */
        .notification-container {
            display: flex;
            align-items: center;
            margin-left: 1rem;
        }

        .notification-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin-left: 1rem;
            background: none;
            border: none;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .notification-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .notification-btn:active {
            transform: translateY(0);
        }

        .notification-btn img {
            width: 24px;
            height: 24px;
            filter: brightness(0) invert(1);
        }

        .notification-count {
            position: absolute;
            top: 0;
            right: 0;
            background-color: #e74c3c;
            color: white;
            border-radius: 50%;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: bold;
            transform: translate(30%, -30%);
        }

        /* Notification dropdown */
        .notification-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            width: 350px;
            max-height: 500px;
            overflow-y: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1050;
            display: none;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item.unread {
            background-color: #f8f9fa;
        }

        .notification-item-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .notification-item-time {
            font-size: 12px;
            color: #6c757d;
        }

        .notification-footer {
            padding: 10px;
            text-align: center;
            border-top: 1px solid #eee;
        }

        /* Adjust content below fixed navbar */
        body {
            padding-top: 72px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="logo/homeB.png" alt="Breazy Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact Us</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <div class="notification-container">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin Panel</a>
                            </li>
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="nav-link btn btn-link">Logout</button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Admin Login</a>
                            </li>
                        @endauth
                        <div class="position-relative">
                            <button id="notification-btn" class="notification-btn">
                                <img src="logo/bell.png" alt="Notifications">
                                <span id="notification-count" class="notification-count">15</span>
                            </button>
                            <div id="notification-dropdown" class="notification-dropdown">
                                <div class="notification-header">
                                    <span>Notifications</span>
                                    <small><a href="#" style="color: var(--primary-color);">Mark all as read</a></small>
                                </div>
                                <div class="notification-item unread">
                                    <div class="notification-item-title">New AQI Alert</div>
                                    <div class="notification-item-message">High pollution levels detected in Colombo</div>
                                    <div class="notification-item-time">2 minutes ago</div>
                                </div>
                                <div class="notification-item unread">
                                    <div class="notification-item-title">System Update</div>
                                    <div class="notification-item-message">New features available in dashboard</div>
                                    <div class="notification-item-time">1 hour ago</div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-item-title">Maintenance Scheduled</div>
                                    <div class="notification-item-message">System maintenance planned for tomorrow</div>
                                    <div class="notification-item-time">Yesterday</div>
                                </div>
                                <div class="notification-footer">
                                    <a href="#" style="color: var(--primary-color);">View All Notifications</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Your page content here -->
    <div class="container">
        <!-- Content goes here -->
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Notification dropdown toggle
        document.getElementById('notification-btn').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('notification-dropdown');
            dropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            const dropdown = document.getElementById('notification-dropdown');
            dropdown.classList.remove('show');
        });

        // Prevent dropdown from closing when clicking inside
        document.getElementById('notification-dropdown').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    </script>
</body>
</html>
