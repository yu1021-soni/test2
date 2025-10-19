@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/detail.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="detail">

    {{-- 左：商品画像 --}}
    <div class="detail__image">
        <img
        src="{{ $item->item_img_url ? Storage::url($item->item_img_url) : asset('img/placeholder.png') }}" alt="{{ $item->name }}" class="detail__image-img">
    </div>

    {{-- 右：商品情報 --}}
  <div class="detail__info">

    <div class="detail__area">

        {{-- 商品名・ブランド・価格 --}}
        <h1 class="detail__name">{{ $item->name }}</h1>
        <div class="detail__brand">{{ $item->brand }}</div>
        <div class="detail__price">¥{{ number_format($item->price) }} <span>（税込）</span>
        </div>

        <form action="{{ route('favorites.favorite') }}" method="post" class="detail__favorite">
        @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <button class="detail__favorite-button" type="submit">
                @if($item->is_favorited)
                <i class="fa-solid fa-star"></i>
                @else
                <i class="fa-regular fa-star"></i>
                @endif
            </button>
            <p>{{ $item->favorites_count }}</p>
        </form>

        <div class="detail__comment-icon">
            <i class="fa-regular fa-comment"></i>
            <p>{{ $item->comments_count }}</p>
        </div>

    </div>

    <div class="detail__description">

        {{-- 購入ボタン --}}
        {{-- 購入済みは Sold --}}
        @if ($item->order)
            <p class="detail__badge-sold">Sold</p>
        @else
        <form method="post" action="{{ route('purchase.store') }}">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <button type="submit" class="detail__btn-buy">購入手続きへ</button>
        </form>
        @endif

        {{-- 商品説明 --}}
        <h2 class="detail__section-title">商品説明</h2>
        <p class="detail__desc-text">{{ $item->description }}</p>

    </div>

    <div class="detail__specs">

        {{-- 商品の情報 --}}
        <h2 class="detail__section-title">商品の情報</h2>
        <ul class="detail__spec">
            <li class="detail__spec-row">
                <span class="detail__spec-label">カテゴリー</span>
                <span class="detail__spec-value">
                    @if ($item->categories->isNotEmpty())
                        @foreach ($item->categories as $cat)
                        <span class="detail__chip">{{ $cat->name }}</span>
                        @endforeach
                    @else
                        <span class="detail__muted">未分類</span>
                    @endif
                </span>
            </li>
            <li class="detail__spec-row">
                <span class="detail__spec-label">商品の状態</span>
                <span class="detail__spec-value">{{ $item->condition_label }}</span>
            </li>
        </ul>

    </div>

    <div class="detail__comments">

        {{-- コメント --}}
        <h2 class="detail__section-title">
            コメント ({{ $item->comments->count() }})
        </h2>

        <div class="detail__comments-list">
            @forelse ($item->comments as $comment)
            <div class="detail__comment">
                <div class="detail__comment-head">
                    <span class="detail__comment-avatar">
                        <img src="{{ $comment->user?->user_img_url ? Storage::url($comment->user->user_img_url) : asset('img/avatar-default.png') }}" alt="{{ $comment->user?->name ?? 'user' }}"/>
                    </span>
                    <strong class="detail__comment-name">{{ $comment->user->name }}</strong>
                </div>
                <p class="detail__comment-body">{{ $comment->comment }}</p>
            </div>
            @empty
            <p class="detail__no-comment">こちらにコメントが入ります。</p>
            @endforelse
        </div>

        {{-- コメント投稿フォーム --}}
        @auth
        <form action="{{ route('comment.store') }}" method="post" class="detail__comment-form">
        @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">

            <h3 class="detail__form-title">商品へのコメント</h3>
            <textarea name="comment" rows="5"></textarea>
            <button type="submit" class="detail__btn-comment">コメントを送信する</button>
        </form>

        <div class="detail__comment-error">
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
