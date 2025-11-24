<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wellness Tracker</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <!-- Fuente: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Paleta de Colores */
            --bg-color: #f6f6f6;       /* Cascading White */
            --card-bg: #ffffff;
            --accent-soft: #e0dbf3;    /* Chinese Silver */
            --primary: #f3ba60;        /* Crunch (Acción) */
            --border: #b6b1c0;         /* Dreamland */
            --text-body: #736a6a;      /* Warm Haze */
            --text-head: #202022;      /* Lead */
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-body);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Utilidades */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        h1, h2, h3 {
            color: var(--text-head);
            font-weight: 700;
        }

        a {
            text-decoration: none;
            transition: 0.3s;
        }

        /* Botones */
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--text-head);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(243, 186, 96, 0.4);
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--text-head);
            color: var(--text-head);
        }

        .btn-outline:hover {
            background-color: var(--text-head);
            color: white;
        }

        .btn-link {
            color: var(--text-body);
            font-size: 0.9rem;
        }
        
        .btn-link:hover {
            color: var(--primary);
        }

        /* Cards (Login/Registro) */
        .auth-card {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 450px;
            border: 1px solid var(--accent-soft);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        /* Formularios */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-head);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--accent-soft);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            color: var(--text-head);
            transition: 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background-color: #fff;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-head);
            margin-bottom: 5px;
            display: block;
        }
        
        .logo-span {
            color: var(--primary);
        }

        /* Footer simple */
        footer {
            text-align: center;
            padding: 20px;
            font-size: 0.8rem;
            color: var(--border);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
    
    <footer>
        &copy; {{ date('Y') }} Wellness Tracker. Hecho por Alois D'Alto y Yohan Franco.
    </footer>
</body>
</html>