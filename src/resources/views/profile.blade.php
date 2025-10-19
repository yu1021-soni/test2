@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/profile.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="profile">

    {{-- 上：ユーザー情報 --}}
    <div class="profile__user">
        <div class="profile__avatar">
            <img src="{{ $user->user_img_url ? Storage::url($user->user_img_url) : asset('img/avatar-default.png') }}" >
        </div>
        <div class="profile__name">
            {{ $user->name }}
        </div>
        <form action="{{ route('profile.edit') }}" class="profile__edit" method="post">
            @csrf
            <button type="submit" class="profile__edit-button">
                プロフィールを編集
            </button>
        </form>
    </div>

    {{-- 下：アイテム一覧 --}}
    <div class="profile__table">
        {{-- タブリンク --}}
        <div class="profile__tabs">
            <a href="?page=sell" class="{{ request('page','sell')==='sell' ? 'is-active' : '' }}">
                出品した商品
            </a>
            <a href="?page=buy"  class="{{ request('page','sell')==='buy'  ? 'is-active' : '' }}">
                購入した商品
            </a>
        </div>

        {{-- コンテンツ --}}
        @if ($tab === 'sell')
        <div class="profile__list">
            @foreach ($items as $item)
            <div class="profile__card">
                <div class="profile__image">
                    <img src="{{ $item->item_img_url ? Storage::url($item->item_img_url) : asset('img/placeholder.png') }}" alt="{{ $item->name }}">
                    @if (($item->order_count ?? 0) > 0)
                    <div class="badge-sold">
                        sold
                    </div>
                    @endif
                </div>
                <div class="profile__item-name">
                    {{ $item->name }}
                </div>
            </div>
        @endforeach
        </div>
        {{ $items->links() }}
        @else
        <div class="profile__list">
        @foreach ($orders as $order)
            <div class="profile__card">
                <div class="profile__image">
                    <img src="{{ optional($order->item)->item_img_url ? Storage::url($order->item->item_img_url) : asset('img/placeholder.png') }}" alt="{{ $order->item->name ?? '購入商品' }}">
                </div>
                <div class="profile__item-name">
                    {{ $order->item->name ?? '' }}
                </div>
            </div>
            @endforeach
        </div>
        {{ $orders->links() }}
        @endif
    </div>

</div>
@endsection
