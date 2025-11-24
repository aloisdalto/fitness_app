<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wellness Tracker</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <!-- Fuente Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Iconos (Bootstrap Icons CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bg-color: #f6f6f6;
            --card-bg: #ffffff;
            --accent-soft: #e0dbf3;
            --primary: #f3ba60;
            --text-head: #202022;
            --text-body: #736a6a;
            --success: #4ade80;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-body);
            margin: 0;
        }

        /* Navbar */
        .navbar {
            background-color: var(--card-bg);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-logo {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-head);
            text-decoration: none;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-btn {
            background: none;
            border: 1px solid var(--text-body);
            padding: 5px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: var(--text-head);
            color: white;
            border-color: var(--text-head);
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Cards Globales */
        .card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
        }

        h2 { color: var(--text-head); margin-bottom: 20px; }
        h4 { color: var(--text-head); margin: 0 0 10px 0; font-weight: 600; }
        
        /* Grid Systems */
        .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .grid-4 { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }

    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            Wellness<span style="color: var(--primary)">Tracker</span>
        </a>
        <div class="nav-user">
            <span>Hola, <strong>{{ Auth::user()->name }}</strong></span>
            
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Salir</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>
</body>
</html>