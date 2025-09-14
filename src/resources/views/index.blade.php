@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('content')
<div class="content">
  <div class="select__page">
    <a href="/" class="select__page-main">おすすめ</a>
    <a href="/?tab=mylist" class="select__page-my">マイリスト</a>
  </div>

  <div class="item__list">
    @forelse ($items as $item)
      <div class="item__card">

        <a href="{{ route('items.detail', $item ) }}">
        {{-- リンク先URL = route('ルート名', ['プレースホルダ名' => 値]) --}}
        <div class="item__img">
          <img
            src="{{ $item->item_img_url ? Storage::url($item->item_img_url) : asset('img/placeholder.png') }}"
            alt="{{ $item->name }}">
        </div>

        {{-- 購入済みは Sold --}}
        @if ($item->order)
          <span class="badge-sold">Sold</span>
        @endif

        <div class="item__name">
            {{ $item->name }}
        </div>
        </a>

      </div>
    @empty
      <p>商品がありません。</p>
    @endforelse
  </div>
</div>
@endsection
