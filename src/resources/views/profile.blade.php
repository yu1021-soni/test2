@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
@endsection

@section('content')
<div class="content">

    {{-- 上：ユーザー情報 --}}
    <div class="user">
        <div class="user__img">
            <img src="{{ $user->user_img_url ? Storage::url($user->user_img_url) : asset('img/avatar-default.png') }}" alt="{{ $user->name }}"/>
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
        {{-- タブリンク --}}
        <div class="select__page">
            <a href="?page=sell" class="{{ request('page','sell')==='sell' ? 'is-active' : '' }}">
                出品した商品
            </a>
            <a href="?page=buy"  class="{{ request('page','sell')==='buy'  ? 'is-active' : '' }}">
                購入した商品
            </a>
        </div>

        {{-- コンテンツ --}}
        @if ($tab === 'sell')
        <div class="cards-grid">
            @foreach ($items as $item)
            <div class="card">
                <div class="thumb">
                    <img src="{{ $item->item_img_url ? Storage::url($item->item_img_url) : asset('img/placeholder.png') }}" alt="{{ $item->name }}">

                    @if (($item->order_count ?? 0) > 0)
                        <p class="badge-sold">sold</p>
                    @endif
                </div>
                <div class="name">
                    {{ $item->name }}
                </div>
            </div>
            @endforeach
            </div>
            {{ $items->links() }}
        @else
            <div class="orders-grid">
            @foreach ($orders as $order)
                <div class="order">
                    <div class="thumb">
                        <img src="{{ optional($order->item)->item_img_url ? Storage::url($order->item->item_img_url) : asset('img/placeholder.png') }}" alt="{{ $order->item->name ?? '購入商品' }}">
                    </div>
                    <div class="name">
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