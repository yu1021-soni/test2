@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css')}}">
@endsection

@section('content')
    <div class="content">
        <h2>住所の変更</h2>
        <form method="post" action="{{ route('address.change') }}">
        @csrf
            {{-- purchase で保存済みの item_id を hidden で渡す --}}
            <input type="hidden" name="item_id" value="{{ session('checkout.item_id') }}">

            <label>郵便番号</label>
            <input type="text" name="postcode" value="{{ old('postcode') }}">

            <label>住所</label>
            <input type="text" name="address" value="{{ old('address') }}">

            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building') }}">

            <button type="submit">更新する</button>
        </form>
    </div>
@endsection