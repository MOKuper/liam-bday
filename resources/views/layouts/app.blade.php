<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', "Liam's 5th Birthday Party!")</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fredoka-one:400|nunito:400,500,600,700,800" rel="stylesheet" />
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Additional Styles -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
        .fun-font {
            font-family: 'Fredoka One', cursive;
        }
        .bounce {
            animation: bounce 1s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .confetti {
            background-image: 
                url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cg%3E%3Crect fill='%23ff6b6b' x='10' y='10' width='5' height='10' transform='rotate(45 12.5 15)'/%3E%3Crect fill='%234ecdc4' x='30' y='20' width='5' height='10' transform='rotate(-45 32.5 25)'/%3E%3Crect fill='%23ffe66d' x='50' y='15' width='5' height='10' transform='rotate(30 52.5 20)'/%3E%3Crect fill='%23a8e6cf' x='70' y='25' width='5' height='10' transform='rotate(-30 72.5 30)'/%3E%3Crect fill='%23ff8b94' x='20' y='40' width='5' height='10' transform='rotate(60 22.5 45)'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 100px 100px;
            background-position: 0 0, 50px 50px;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-yellow-100 via-pink-100 to-blue-100">
    <div class="confetti fixed inset-0 opacity-10 pointer-events-none"></div>
    
    @yield('content')
    
    @stack('scripts')
</body>
</html>