<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Breazy - Air Quality Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #038b40;
            --secondary-color: #2c3e50;
            --background-color: #f5f5f5;
            --text-color: #333;
            --white: #ffffff;
            --sensor-color: #4a6bff;
        }

        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
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

        /* AQI Alert Button Styles */
        #aqiAlertsButton {
            position: relative;
        }

        .aqi-alert-badge {
            font-size: 0.6rem;
            padding: 0.25rem 0.4rem;
            display: none;
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(25%, -25%);
        }

        /* AQI Alert Popup Styles */
        .aqi-alert-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            max-width: 90%;
            max-height: 80vh;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1080;
            display: flex;
            flex-direction: column;
        }

        .aqi-alert-popup-content {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .aqi-alert-popup-header {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .aqi-alert-popup-body {
            padding: 1rem;
            overflow-y: auto;
            flex-grow: 1;
        }

        .aqi-alert-popup-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fa;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        /* AQI Alert Item Styles */
        .aqi-alert-item {
            padding: 0.75rem 1rem;
            border-left: 4px solid;
            margin-bottom: 0.5rem;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .aqi-alert-item:hover {
            background-color: #f8f9fa;
        }

        .aqi-alert-location {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .aqi-alert-message {
            font-size: 0.85rem;
            color: #495057;
        }

        .aqi-alert-time {
            font-size: 0.75rem;
            color: #6c757d;
            text-align: right;
        }
        

        /* AQI Level Colors */
        .aqi-good { border-color: #00e400; }
        .aqi-moderate { border-color: #ffff00; }
        .aqi-unhealthy-sg { border-color: #ff7e00; }
        .aqi-unhealthy { border-color: #ff0000; }
        .aqi-very-unhealthy { border-color: #99004c; }
        .aqi-hazardous { border-color: #7e0023; }

        /* Overlay */
        .aqi-alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1070;
        }

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
                Breazy
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
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="aqiAlertsButton">
                                <i class="fas fa-bell"></i>
                                <span class="aqi-alert-badge" id="aqiAlertCount"></span>
                            </a>
                        </li>
                    @endauth
                    
                    <!-- AQI Alerts Button -->
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="aqiAlertsButton">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger aqi-alert-badge" id="aqiAlertCount">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Your page content here -->
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AQI Alerts Script -->
    <script src="/js/aqi-alerts.js"></script>
</body>
</html>