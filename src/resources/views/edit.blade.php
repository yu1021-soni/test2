@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/edit.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@section('content')
    <div class="content">
        <h2 class="content__title">プロフィール設定</h2>
        <form action="{{ route('profile.update') }}" method="post" class="update" enctype="multipart/form-data">
            @csrf
        <div class="user__img">
            <img src="{{ data_get($user, 'profile.user_img_url')
            ? Storage::url(data_get($user, 'profile.user_img_url'))
            : asset('img/avatar-default.png') }}" class="user__item"/>
            <input id="user-img" class="uploader__input" type="file" name="user_img_url" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
            <label for="user-img" class="uploader__button">画像を選択する</label>
        </div>
    
            <div class="profile">
                <div class="profile__name">
                    <p class="title">ユーザ名</p>
                    <input type="text" name="name" value="{{ $user->name }}" class="text">
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="profile__postcode">
                    <p class="title">郵便番号</p>
                    <input type="text" name="postcode" value="{{ optional($profile)->postcode }}" class="text">
                    @error('postcode')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="profile__address">
                    <p class="title">住所</p>
                    <input type="text" name="address" value="{{ optional($profile)->address }}" class="text">
                    @error('address')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="profile__building">
                    <p class="title">建物名</p>
                    <input type="text" name="building" value="{{ optional($profile)->building }}" class="text">
                    @error('building')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="submit">
                <button type="submit" class="button__update">更新する</button>
            </div>
        </form>
    </div>
@endsection