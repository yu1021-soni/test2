@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css')}}">
@endsection

@section('content')
    <div class="content">
        <h2 class="content__title">プロフィール設定</h2>
        <div class="user__img">
            <img src="{{ $user->user_img_url ? Storage::url($user->user_image_url) : asset('img/avatar-default.png') }}" class="user__item"/>
            <button  class="uploader__button">画像を選択する</button>
        </div>
        <form action="{{ route('profile.update') }}" method="post" class="update">
            @csrf
            <div class="profile">
                <div class="profile__name">
                    <p class="title">ユーザ名</p>
                    <input type="text" name="name" value="{{ $user->name }}" class="text">
                </div>
                <div class="profile__postcode">
                    <p class="title">郵便番号</p>
                    <input type="text" name="postcode" value="{{ $user->postcode }}" class="text">
                </div>
                <div class="profile__address">
                    <p class="title">住所</p>
                    <input type="text" name="address" value="{{ $user->address }}" class="text">
                </div>
                <div class="profile__building">
                    <p class="title">建物名</p>
                    <input type="text" name="building" value="{{ $user->building }}" class="text">
                </div>
            </div>
            <div class="submit">
                <button type="submit" class="button__update">更新する</button>
            </div>
        </form>
    </div>
@endsection