<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coachtech</title>
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    @yield('css')
</head>
<body>
    <div class="app">
        <header class="header">
            <div class="header__heading">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" class="header__logo">
                @yield('link')
            </div>
        </header>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>
</html>