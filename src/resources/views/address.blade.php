@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/address.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@section('content')
    <div class="address">
        <h2 class="address__title">住所の変更</h2>
        <form method="post" action="{{ route('address.change') }}" class="address__form">
        @csrf
            <input type="hidden" name="item_id" value="{{ session('checkout.item_id') }}">

            <label class="address__label">郵便番号</label>
            <input class="address__input" type="text" name="postcode" value="{{ old('postcode') }}">

            <label class="address__label">住所</label>
            <input class="address__input" type="text" name="address" value="{{ old('address') }}">

            <label class="address__label">建物名</label>
            <input class="address__input" type="text" name="building" value="{{ old('building') }}">

            <button class="address__submit" type="submit">更新する</button>
        </form>
    </div>
@endsection
