@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('link')
    <form method="post" action="{{ route('logout') }}">
    @csrf
        <button type="submit">ログアウト</button>
    </form>
@endsection

@section('content')

    <div class="content">
        <div class="select__page">
            <div class="select__page-main">
                おすすめ
            </div>
            <a href="/?tab=mylist" class="select__page-my">
                マイリスト
            </a>
        </div>
        <div class="item__list">
            <div class="item__img">
                <img src="" alt="">
            </div>
            <div class="item__name">
                
            </div>
        </div>

    </div>

@endsection