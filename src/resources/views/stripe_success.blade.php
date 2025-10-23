@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
<link rel="stylesheet" href="{{ asset('css/stripe_success.css')}}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection


@section('content')
<div class="stripe">
    <div class="stripe__body">
        <p class="stripe__text">
            決済が完了しました
        </p>

        <div class="stripe__actions">
            <a href="/mypage" class="stripe__link">マイページへ</a>
        </div>
    </div>
</div>
@endsection