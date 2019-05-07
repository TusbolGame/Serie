<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
@include('includes.header')
</head>
<body>
@include('includes.navbar')
    <div id="main-wrapper" class="container mt-4">
        <div id="main-container" class="row">
            <main class="col-12 px-1">
                @yield('content')
            </main>
        </div>
    </div>
@include('includes.footer')
</body>
</html>
