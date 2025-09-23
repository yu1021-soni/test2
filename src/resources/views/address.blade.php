@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
@endsection

@section('content')
    <div class="content">
        <h2>住所の変更</h2>
        <form action="{{ route('address.change') }}" method="post" class="address">
            @csrf
            <div class="profile">
                <div class="profile__postcode">
                    <p>郵便番号</p>
                    <input type="text" name="postcode">
                </div>
                <div class="profile__address">
                    <p>住所</p>
                    <input type="text" name="address">
                </div>
                <div class="profile__building">
                    <p>建物名</p>
                    <input type="text" name="building">
                </div>
            </div>
            <div class="submit">
                <button type="submit" class="button__update">更新する</button>
            </div>
        </form>
    </div>
@endsection