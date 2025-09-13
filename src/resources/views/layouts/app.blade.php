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
            <div class="header__inner">
                <a href="" class="header__logo-link">
                    <img src="{{ asset('img/logo.svg') }}" alt="COACHTECH" class="header__logo">
                </a>

                <form class="search" action="/search" method="get">
                    <input class="search__input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}" >
                </form>

                <nav class="header__nav">
                    <ul class="header__menu">
                        <li>
                            <form method="post" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="logout">ログアウト</button>
                            </form>
                        </li>
                        <li><a href="/mypage" class="mypage">マイページ</a></li>
                        <li><a href="/sell" class="sell">出品</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        
        <div class="content">
            @yield('content')
        </div>

    </div>
</body>
</html>