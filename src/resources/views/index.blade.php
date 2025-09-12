@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('link')
<form class="search-form" action="/search" method="get">
  @csrf
  <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{request('keyword')}}">
</form>
<form method="post" action="{{ route('logout') }}">
  @csrf
  <button type="submit">ログアウト</button>
</form>
@endsection

@section('content')
<div class="content">
  <div class="select__page">
    <div class="select__page-main">おすすめ</div>
    <a href="/?tab=mylist" class="select__page-my">マイリスト</a>
  </div>

  <div class="item__list">
    @forelse ($items as $item)
      <div class="item__card">
        <div class="item__img">
          <img
            src="{{ $item->item_img_url ? Storage::url($item->item_img_url) : asset('img/placeholder.png') }}"
            alt="{{ $item->name }}">
        </div>

        {{-- 購入済みは Sold --}}
        @if ($item->order)
          <span class="badge-sold">Sold</span>
        @endif

        <div class="item__name">{{ $item->name }}</div>
      </div>
    @empty
      <p>商品がありません。</p>
    @endforelse
  </div>
</div>
@endsection
