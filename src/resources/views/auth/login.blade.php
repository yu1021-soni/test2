@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css')}}">
@endsection

@section('content')
<div class="login-form__content">

  <form action="/login" class="form" method="post">
  @csrf
    <div class="login-form__heading">
      <h2>ログイン</h2>
    </div>

      <div class="login__group">
        <div class="login__group-title">
          <span class="login__label--item">メールアドレス</span>
        </div>
        <div class="login__group-content">
          <div class="login__input--text">
            <input type="text" name="email">
          </div>
          <div class="login__error">
            @error('email')
              {{ $message }}
            @enderror
          </div>
        </div>
      </div>
      <div class="login__group">
        <div class="login__group-title">
          <span class="login__label--item">パスワード</span>
        </div>
        <div class="login__group-content">
          <div class="login__input--text">
            <input type="password" name="password">
          </div>
          <div class="login__error">
            @error('password')
              {{ $message }}
            @enderror
          </div>
        </div>
      </div>
      <div class="login__button">
        <button class="login__button-submit" type="submit">ログインする</button>
      </div>

      <a class="bottom__link" href="/register">会員登録はこちら</a>
    </form>


</div>
@endsection