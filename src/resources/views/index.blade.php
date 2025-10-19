@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/index.css')}}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@php
  // 検索条件を保持
  $searchParams = request()->except('tab', 'page');
@endphp

@section('content')
<div class="catalog">
  <div class="catalog__tabs">
    <a
      href="{{ route('item.index', $searchParams) }}"
      class="catalog__tab-link {{ request('tab') !== 'mylist' ? 'is-active' : '' }}"
    >おすすめ</a>

  {{-- マイリスト（?tab=mylist の時に active） --}}
  {{-- array_merge は 配列どうしを合体する関数 --}}
  <a
    href="{{ route('item.index', array_merge($searchParams, ['tab' => 'mylist'])) }}"
    class="catalog__tab-link {{ request('tab') === 'mylist' ? 'is-active' : '' }}"
  >マイリスト</a>
  </div>

  <div class="catalog__list">
    @foreach ($items as $item)
      <div class="catalog__card">

        <a href="{{ route('items.detail', $item ) }}">
        {{-- リンク先URL = route('ルート名', ['プレースホルダ名' => 値]) --}}
        <div class="catalog__image">
          <img
            src="{{ $item->item_img_url ? Storage::url($item->item_img_url) : asset('img/placeholder.png') }}"
            alt="{{ $item->name }}">
        </div>

        <div class="catalog__name">
            {{ $item->name }}
        </div>
        </a>

        {{-- 購入済みは Sold --}}
        @if ($item->order)
          <p class="catalog__badge-sold">Sold</p>
        @endif

      </div>
    @endforeach
  </div>
</div>
@endsection
