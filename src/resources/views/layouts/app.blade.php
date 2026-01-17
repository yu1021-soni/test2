<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coachtech</title>
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    @yield('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="app">
        <header class="header">
            <div class="header__inner">
                @if (Route::is('transaction.*'))
                    {{-- 共通フォーム（表示しない） --}}
                    <form id="global-chat-form" method="POST" enctype="multipart/form-data" style="display:none;">
                        @csrf
                    </form>

                    {{-- ロゴ：共通フォームを送信して下書き保存→トップへ --}}
                    <button type="submit" class="header__logo-link" form="global-chat-form" formaction="{{ route('transaction.draft.redirect', request()->route('transaction')) }}" formmethod="POST" name="redirect_to" value="/">
                        <img src="{{ asset('img/logo.svg') }}" alt="COACHTECH" class="header__logo">
                    </button>
                @else
                    <a href="/" class="header__logo-link">
                        <img src="{{ asset('img/logo.svg') }}" alt="COACHTECH" class="header__logo">
                    </a>
                @endif

                {{-- login,register, mailhog,transaction ページではロゴだけ --}}
                @if (!Route::is('register') && !Route::is('transaction.*') && !Route::is('mailhog') && !Route::is('login') && !Route::is('verification.*'))

                <form class="header__search" action="/search" method="get">
                    <input class="header__search-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}" >
                </form>

                <nav class="header__nav">
                    <ul class="header__menu">
                        <li>
                            @if(Auth::check())
                            <form method="post" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="header__logout">ログアウト</button>
                            </form>
                            @else
                            <form action="{{ route('login') }}" method="get">
                                <button type="submit" class="header__login">ログイン</button>
                            </form>
                            @endif
                        </li>
                        <li>
                            <form method="post" action="{{ route('mypage') }}">
                                @csrf
                                <button type="submit" class="header__mypage">マイページ</button>
                            </form>
                        </li>
                        <li>
                            <a href="{{ route('item.sell') }}" class="header__sell">出品</a>
                        </li>
                    </ul>
                </nav>

                @endif
            </div>
        </header>
        
        <div class="content">
            @yield('content')
        </div>

    </div>
</body>
</html>
