@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mailhog.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="mailhog__content">
    <div class="mailhog__body">
        <p class="mailhog__text">
            登録していただいたメールアドレスに認証メールを送信しました。<br>
        メール認証を完了してください。
        </p>

        <div class="mailhog__button">
            <a href="http://localhost:8025" target="_blank" rel="noopener" class="mailhog__button__form">
                認証はこちらから
            </a>

            <form method="POST" action="{{ route('verification.send') }}" class="resend-button">
            @csrf
                <button type="submit" class="resend__link">認証メールを再送する</button>
            </form>
        </div>
    </div>
</div>
@endsection
