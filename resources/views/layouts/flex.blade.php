<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Head content here -->
</head>
<body>
    <!-- Navbar, header, etc. here -->

    <div class="container">
        @yield('content')
    </div>

    <!-- Footer, scripts, etc. here -->
</body>
</html>
