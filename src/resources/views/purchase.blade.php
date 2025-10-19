@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="purchase">

    {{-- 左：商品画像など --}}
    <div class="purchase__left">

        <div class="purchase__item">
            <div class="purchase__item-image">
                <img src="{{ $item->item_img_url ? Storage::url($item->item_img_url) : asset('img/placeholder.png') }}" alt="{{ $item->name }}">
            </div>
            <div class="purchase__details">
                <p class="purchase__item-name">{{ $item->name }}</p>
                <div class="purchase__item-price">
                    ¥{{ number_format($item->price) }}
                </div>
            </div>
        </div>

        <div class="purchase__payment">
            <h3>支払い方法</h3>
            <select name="payment" form="purchase-form" required>
                <option value="" selected disabled>選択してください</option>
                <option value="1" >コンビニ払い</option>
                <option value="2" >カード払い</option>
            </select>
        </div>

        <div class="purchase__shipping">
            <div class="purchase__shipping-title">
                <h3>配送先</h3>
                <a href="{{ route('address.edit', ['user_id' => auth()->id()]) }}">変更する</a>
            </div>

            @php
                // ★ドラフト優先 → なければ users のカラムを使う
                $postcode = data_get($draft, 'postcode') ?? $user->postcode;
                $address  = data_get($draft, 'address')  ?? $user->address;
                $building = data_get($draft, 'building') ?? $user->building;

                $showPostcode = $postcode ?? '未設定';
                $showAddress  = $address  ?? '未設定';
                $showBuilding = $building ?? '';
            @endphp


            <div class="purchase__address">
                {{ $showPostcode }}<br>
                {{ $showAddress }}<br>
                {{ $showBuilding }}
            </div>

        </div>

    </div>

    {{-- 右：決済方法など --}}
    <div class="purchase__right">
        <div class="purchase__confirm">
            <div class="purchase__confirm-price">
                <p class="purchase__confirm-title">商品代金</p>
                <p class="purchase__confirm-content">¥{{ number_format($item->price) }}</p>
            </div>
            <div class="purchase__confirm-pay">
                <p class="purchase__confirm-title">支払い方法</p>
                <p class="purchase__confirm-content"></p>
            </div>
        </div>
        <form action="{{ route('item.pay') }}" method="post" id="purchase-form">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <input type="hidden" name="postcode" value="{{ $postcode }}">
            <input type="hidden" name="address"  value="{{ $address }}">
            <input type="hidden" name="building" value="{{ $building }}">
            <button type="submit" class="purchase__button">購入する</button>
        </form>
    </div>

</div>
@endsection
