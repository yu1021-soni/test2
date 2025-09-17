@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css')}}">
@endsection

@section('content')
<div class="content">

  {{-- 左：商品画像 --}}
  <div class="item__img">
    <img
      src="{{ $item->item_img_url ? Storage::url($item->item_img_url) : asset('img/placeholder.png') }}"
      alt="{{ $item->name }}">
  </div>

  {{-- 右：商品情報 --}}
  <div class="detail__info">

    {{-- 商品名・ブランド・価格 --}}
    <h1 class="item__name">{{ $item->name }}</h1>
    <div class="item__brand">{{ $item->brand }}</div>
    <div class="item__price">¥{{ number_format($item->price) }} <span>（税込）</span></div>

    <form action="{{ route('favorites.favorite',$item) }}" class="favorite" method="post">
        @csrf
        <button class="favorite__button" type="submit" >
            @if(auth()->check() && auth()->user()->favorites->contains($item->id))
                ⭐️
            @else
                ☆
            @endif
        </button>
        <p>{{ $item->favorites_count }}</p>
    </form>
    <div class="comment">
        <p>💬</p>
        <p>{{ $item->comments_count }}</p>
    </div>


    {{-- 購入ボタン --}}
    <form action="{{ route('purchase.create',$item) }}" class="btn-purchase" method="get" >
        <button type="submit">購入手続きへ</button>
    </form>

    {{-- 商品説明 --}}
    <h2 class="section-title">商品説明</h2>
    <p class="item__description">{{ $item->description }}</p>

    {{-- 商品の情報 --}}
    <h2 class="section-title">商品の情報</h2>
    <ul class="item__info">
      <li>カテゴリ：{{ $item->categories->pluck('name')->join(' / ') ?: '未分類' }}</li>
      <li>商品の状態：{{ $item->condition_label }}</li>
    </ul>

    {{-- コメント --}}
    <h2 class="section-title">コメント ({{ $item->comments->count() }})</h2>
    <div class="comments">
      @forelse($item->comments as $comment)
        <div class="comment">
          <strong>{{ $comment->user->name }}</strong>
          <p>{{ $comment->comment }}</p>
        </div>
      @empty
        <p>コメントはまだありません。</p>
      @endforelse
    </div>


    {{--  コメント投稿フォーム --}}
    @auth
      <form action="{{ route('comment.store', $item) }}" method="post">
      @csrf
        <textarea name="comment" rows="3" placeholder="商品へのコメントを入力"></textarea>
        <button type="submit" class="btn-comment">コメントを送信する</button>
      </form>
      <div class="comment__error">
        @error('comment')
          {{ $message }}
        @enderror
      </div>
    @else
    <p>コメントするには <a href="{{ route('login') }}">ログイン</a> してください。</p>
    @endauth

  </div>
</div>
@endsection
