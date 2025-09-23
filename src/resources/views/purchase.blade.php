@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
@endsection

@section('content')
<div class="content">

    {{-- 左：商品画像など --}}
    <div class="left__content">

        <div class="item__info">
            <div class="item__img">
                <img src="{{ $item->item_img_url ? Storage::url($item->item_img_url) : asset('img/placeholder.png') }}" alt="{{ $item->name }}">
            </div>
            <div class="detail__info">
                <h1 class="item__name">{{ $item->name }}</h1>
                <div class="item__price">
                    ¥{{ number_format($item->price) }}
                </div>
            </div>
        </div>

        <div class="pay__method">
            <h3>支払い方法</h3>
            <form action="" method="get">
                <select name="select" id="">
                    <option value="" selected disabled>選択してください</option>
                    <option value="" ></option>
                    <option value="" ></option>
                </select>
            </form>
        </div>

        <div class="shipping_address">
            <h3>配送先</h3>
            <a href="{{ route('address.edit', ['user_id' => auth()->id()]) }}">変更する</a>
            <div class="address">
                {{ $draft['postcode'] ?? ($profile?->postcode ?? $user->postcode ?? '未設定') }}<br>
                {{ $draft['address']  ?? ($profile?->address  ?? $user->address  ?? '未設定') }}
            </div>
        </div>

    </div>

    {{-- 右：決済方法など --}}
    <div class="right__content">
        <div class="confirm">
            <div class="confirm__price">
                <p class="title">商品代金</p>
                <p></p>
            </div>
            <div class="confirm__pay">
                <p class="title">支払い方法</p>
                <p></p>
            </div>
        </div>
        <form action="{{ route('item.pay') }}" method="post">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <button type="submit" class="button_buy">購入する</button>
        </form>
    </div>

</div>
@endsection