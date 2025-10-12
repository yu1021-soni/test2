@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mailhog.css') }}">
@endsection

@section('content')
<div class="mailhog__content">
    <div class="mailhog__body">
        <p class="mailhog__text">
            登録していただいたメールアドレスに認証メールを送信しました。<br>
        メール認証を完了してください。
        </p>

        <div class="mailhog__button">
            {{-- Figmaの「認証はこちらから」＝ 開発中はMailhogへ --}}
            <a href="http://localhost:8025" target="_blank" rel="noopener" class="mailhog__button__form">
                認証はこちらから
            </a>

            {{-- 再送リンク（Fortify標準ルート） --}}
            <form method="POST" action="{{ route('verification.send') }}" class="resend-button">
            @csrf
                <button type="submit" class="resend__link">認証メールを再送する</button>
            </form>
        </div>
    </div>
</div>
@endsection
