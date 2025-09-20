@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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

    <div class="product__area">

        {{-- 商品名・ブランド・価格 --}}
        <h1 class="item__name">{{ $item->name }}</h1>
        <div class="item__brand">{{ $item->brand }}</div>
        <div class="item__price">¥{{ number_format($item->price) }} <span>（税込）</span>
        </div>

        <form action="{{ route('favorites.favorite') }}" method="post" class="favorite">
        @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <button class="favorite__button" type="submit">
                @if($item->is_favorited)
                <i class="fa-solid fa-star"></i>
                @else
                <i class="fa-regular fa-star"></i>
                @endif
            </button>
            <p>{{ $item->favorites_count }}</p>
        </form>

        <div class="comment__icon">
            <i class="fa-regular fa-comment"></i>
            <p>{{ $item->comments_count }}</p>
        </div>

    </div>

    <div class="product-description">

        {{-- 購入ボタン --}}
        <form method="post" action="{{ route('purchase.store') }}">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <button type="submit" class="btn-buy">購入手続きへ</button>
        </form>

        {{-- 商品説明 --}}
        <h2 class="section-title">商品説明</h2>
        <p class="item__description">{{ $item->description }}</p>

    </div>

    <div class="product__info">

        {{-- 商品の情報 --}}
        <h2 class="section-title">商品の情報</h2>
        <ul class="item__spec">
            <li class="row">
                <span class="label">カテゴリー</span>
                <span class="value">
                    @if ($item->categories->isNotEmpty())
                        @foreach ($item->categories as $cat)
                        <span class="chip">{{ $cat->name }}</span>
                        @endforeach
                    @else
                        <span class="muted">未分類</span>
                    @endif
                </span>
            </li>
            <li class="row">
                <span class="label">商品の状態</span>
                <span class="value">{{ $item->condition_label }}</span>
            </li>
        </ul>

    </div>

    <div class="product__comments">

        {{-- コメント --}}
        <h2 class="section-title">
            コメント ({{ $item->comments->count() }})
        </h2>

        <div class="comments">
            @forelse ($item->comments as $comment)
            <div class="comment">
                <div class="comment__head">
                    <span class="avatar">
                        <img src="{{ $comment->user->profile_image_url ?? asset('img/avatar-default.png') }}" alt="" />
                    </span>
                    <strong class="name">{{ $comment->user->name }}</strong>
                </div>
                <p class="comment__body">{{ $comment->comment }}</p>
            </div>
            @empty
            <p>コメントはまだありません。</p>
            @endforelse
        </div>

        {{-- コメント投稿フォーム --}}
        @auth
        <form action="{{ route('comment.store') }}" method="post" class="comment-form">
        @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">

            <h3 class="form-title">商品のコメント</h3>
            <textarea name="comment" rows="5"></textarea>
            <button type="submit" class="btn-comment">コメントを送信する</button>
        </form>

        <div class="comment__error">
            @error('comment')
                {{ $message }}
            @enderror
            @error('item_id')
                {{ $message }}
            @enderror
        </div>
        @endauth

  </div>
</div>
@endsection
