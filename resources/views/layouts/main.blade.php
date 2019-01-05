<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
@include('includes.header')
</head>
<body>
@include('includes.navbar')
    <div id="main-wrapper" class="container">
        <div id="main-container">
            <main class="py-4">
                @yield('content')
            </main>
        </div>
    </div>
@include('includes.footer')
</body>
</html>
