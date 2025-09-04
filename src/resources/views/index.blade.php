@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('link')
    <form method="post" action="{{ route('logout') }}">
    @csrf
        <button type="submit">ログアウト</button>
    </form>
@endsection