@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/edit.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@section('content')
    <div class="profile">
        <h2 class="profile__title">プロフィール設定</h2>
        <form action="{{ route('profile.update') }}" method="post" class="profile__form" enctype="multipart/form-data">
            @csrf
        <div class="profile__image">
            <img src="{{ $user->user_img_url
            ? Storage::url($user->user_img_url)
            : asset('img/avatar-default.png') }}" class="profile__avatar"/>
            <input id="user-img" class="profile__uploader-input" type="file" name="user_img_url" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
            <label for="user-img" class="profile__uploader-button">画像を選択する</label>
        </div>
    
            <div class="profile__fields">
                <div class="profile__name">
                    <p class="profile__label">ユーザ名</p>
                    <input type="text" name="name" value="{{ $user->name }}" class="profile__input">
                    @error('name')
                        <div class="profile__error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="profile__postcode">
                    <p class="profile__label">郵便番号</p>
                    <input type="text" name="postcode" value="{{ $user->postcode }}" class="profile__input">
                    @error('postcode')
                        <div class="profile__error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="profile__address">
                    <p class="profile__label">住所</p>
                    <input type="text" name="address" value="{{ $user->address }}" class="profile__input">
                    @error('address')
                        <div class="profile__error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="profile__building">
                    <p class="profile__label">建物名</p>
                    <input type="text" name="building" value="{{ $user->building }}" class="profile__input">
                    @error('building')
                        <div class="profile__error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="profile__actions">
                <button type="submit" class="profile__submit">更新する</button>
            </div>
        </form>
    </div>
@endsection
