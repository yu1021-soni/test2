@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="purchase__content">

    {{-- 左：商品画像など --}}
    <div class="left__content">

        <div class="item__info">
            <div class="item__img">
                <img src="{{ $item->item_img_url ? Storage::url($item->item_img_url) : asset('img/placeholder.png') }}" alt="{{ $item->name }}">
            </div>
            <div class="detail__info">
                <p class="item__name">{{ $item->name }}</p>
                <div class="item__price">
                    ¥{{ number_format($item->price) }}
                </div>
            </div>
        </div>

        <div class="pay__method">
            <h3>支払い方法</h3>
            <select name="payment" form="purchase-form" required>
                <option value="" selected disabled>選択してください</option>
                <option value="1" >コンビニ払い</option>
                <option value="2" >カード払い</option>
            </select>
        </div>

        <div class="shipping__address">
            <div class="shipping__title">
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


            <div class="address">
                {{ $showPostcode }}<br>
                {{ $showAddress }}<br>
                {{ $showBuilding }}
            </div>

        </div>

    </div>

    {{-- 右：決済方法など --}}
    <div class="right__content">
        <div class="confirm">
            <div class="confirm__price">
                <p class="confirm__title">商品代金</p>
                <p class="confirm__content">¥{{ number_format($item->price) }}</p>
            </div>
            <div class="confirm__pay">
                <p class="confirm__title">支払い方法</p>
                <p class="confirm__content"></p>
            </div>
        </div>
        <form action="{{ route('item.pay') }}" method="post" id="purchase-form">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <input type="hidden" name="postcode" value="{{ $postcode }}">
            <input type="hidden" name="address"  value="{{ $address }}">
            <input type="hidden" name="building" value="{{ $building }}">
            <button type="submit" class="button_buy">購入する</button>
        </form>
    </div>

</div>
@endsection