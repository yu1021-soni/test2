@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
@endsection

@section('content')
    <div class="content">
        <h2>プロフィール設定</h2>
        <div class="user__img">
            <img src="{{ $user->user_img_url ? Storage::url($user->user_image_url) : asset('img/avatar-default.png') }}" alt="{{ $user->name }}"/>
        </div>
        <form action="{{ route('profile.update') }}" method="post" class="update">
            @csrf
            <div class="profile">
                <div class="profile__name">
                    <p>ユーザ名</p>
                    <input type="text" name="name" value="{{ $user->name }}">
                </div>
                <div class="profile__postcode">
                    <p>郵便番号</p>
                    <input type="text" name="postcode" value="{{ $user->postcode }}">
                </div>
                <div class="profile__address">
                    <p>住所</p>
                    <input type="text" name="address" value="{{ $user->address }}">
                </div>
                <div class="profile__building">
                    <p>建物名</p>
                    <input type="text" name="building" value="{{ $user->building }}">
                </div>
            </div>
            <div class="submit">
                <button type="submit" class="button__update">更新する</button>
            </div>
        </form>
    </div>
@endsection