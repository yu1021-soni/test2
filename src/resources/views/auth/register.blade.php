@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
@endsection

@section('content')
<div class="register-form__content">

  <form action="/register" class="form" method="post">
  @csrf
    <div class="register-form__heading">
      <h2>会員登録</h2>
    </div>

    <div class="register__group">
      <div class="register__group-title">
        <span class="register__label--item">ユーザ名</span>
      </div>
      <div class="register__group-content">
      <div class="register__input--text">
        <input type="text" name="name">
      </div>
      <div class="register__error">
        @error('name')
          {{ $message }}
        @enderror
      </div>
    </div>
    </div>
      <div class="register__group">
        <div class="register__group-title">
          <span class="register__label--item">メールアドレス</span>
        </div>
        <div class="register__group-content">
          <div class="register__input--text">
            <input type="text" name="email">
          </div>
          <div class="register__error">
            @error('email')
              {{ $message }}
            @enderror
          </div>
        </div>
      </div>
      <div class="register__group">
        <div class="register__group-title">
          <span class="register__label--item">パスワード</span>
        </div>
        <div class="register__group-content">
          <div class="register__input--text">
            <input type="password" name="password">
          </div>
          <div class="register__error">
            @error('password')
              {{ $message }}
            @enderror
          </div>
        </div>
      </div>
      <div class="register__group">
        <div class="register__group-title">
          <span class="register__label--item">もう一度入力</span>
        </div>
        <div class="register__group-content">
          <div class="register__input--text">
            <input type="password" name="password_confirmation">
          </div>
        </div>
      </div>
      <div class="register__button">
        <button class="register__button-submit" type="submit">登録する</button>
      </div>

      <a class="bottom__link" href="/login">ログインはこちら</a>
    </form>


</div>
@endsection