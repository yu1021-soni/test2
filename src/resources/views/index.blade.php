@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('content')
<div class="content">
  <div class="select__page">
    <a
    href="{{ route('item.index') }}"
    class="select__page-link {{ request('tab') !== 'mylist' ? 'is-active' : '' }}"
  >おすすめ</a>

  {{-- マイリスト（?tab=mylist の時に active） --}}
  <a
    href="{{ route('item.index', ['tab' => 'mylist']) }}"
    class="select__page-link {{ request('tab') === 'mylist' ? 'is-active' : '' }}"
  >マイリスト</a>
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

        <div class="item__name">
            {{ $item->name }}
        </div>
        </a>

        {{-- 購入済みは Sold --}}
        @if ($item->order)
          <p class="badge-sold">Sold</p>
        @endif

      </div>
    @empty
      <p class="nothing">商品がありません</p>
    @endforelse
  </div>
</div>
@endsection
