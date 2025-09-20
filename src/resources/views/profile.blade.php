@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
@endsection

@section('content')
<div class="content">

    {{-- 上：ユーザー情報 --}}
    <div class="user">
        <div class="user__img">
            <img src="{{ $comment->user->profile_image_url ?? asset('img/avatar-default.png') }}" alt="{{ $user->name }}" />
        </div>
        <form action="{{ route('profile.edit') }}" class="edit" method="post">
            @csrf
            <button type="submit" class="button__edit">
                プロフィールを編集
            </button>
        </form>
    </div>

    {{-- 下：アイテム一覧 --}}
    <div class="item__list">
        <div class="select__page">
            <a href="/mypage?page=sell" class="select__page-main">出品した商品</a>
            <a href="/mypage?page=buy" class="select__page-my">購入した商品</a>
        </div>
        <div class="item__img">

        </div>
    </div>

</div>
@endsection