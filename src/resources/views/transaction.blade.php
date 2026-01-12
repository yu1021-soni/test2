@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/transaction.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@section('content')
    <div class="content">

        {{-- 左：取引中商品一覧 --}}
        <div class="select__box">
            <div class="select__box-title">
                <h3>その他の取引</h3>
            </div>
            <div class="select__box-name">
                @if($transaction->seller_id === auth()->id())
                    @foreach($transactions as $t)
                    <div class="catalog__name">
                        {{ $t->item->name }}
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- 右：取引中商品詳細 --}}
        <div class="tradition__detail">

            <div class="profile">
                <div class="profile__avatar">
                    <img src="{{ $user->user_img_url ? Storage::url($user->user_img_url) : asset('img/avatar-default.png') }}" >
                </div>
                <div class="profile__name">
                    <p>
                        「{{ $user->name }}」さんとの取引画面
                    </p>
                </div>
                <div class="complete">
                    取引を完了する
                </div>
            </div>

            <div class="transaction__item">
                <div class="item__image">
                    <img
                        src="{{ $item->item_img_url ? Storage::url($item->item_img_url) : asset('img/placeholder.png') }}" alt="{{ $item->name }}" class="detail__image-img">
                </div>
                <h1 class="detail__name">{{ $item->name }}</h1>
                <div class="detail__price">
                    ¥{{ number_format($item->price) }} <span>（税込）</span>
                </div>
            </div>

            <div class="chat">

            <div class="chat__list">

                {{-- エラーが出ないように空配列 --}}
                @foreach(($transaction->messages ?? []) as $messages)

                {{-- 自分のメッセージ --}}
                @if($messages->sender_id === auth()->id())
                <div class="chat__row chat__row--me">

                    <div class="chat__bubble">
                        <div class="chat__avatar">
                            <img src="{{ auth()->user()->user_img_url
                            ? Storage::url(auth()->user()->user_img_url)
                            : asset('img/avatar-default.png') }}">
                        </div>
                        <div class="chat__name">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="chat__message">

                        @if($editMessageId == $messages->id)
                            <form action="{{ route('message.edit', ['transaction' => $transaction->id, 'message_id' => $messages->id]) }}" method="POST">
                            @csrf
                                <input type="text" name="message" value="{{ old('message', $messages->message) }}" required>
                                <button type="submit">
                                    更新
                                </button>

                                {{-- キャンセル --}}
                                <a href="{{ route('transaction.show', $transaction->id) }}">キャンセル</a>
                            </form>

                            @error('message')
                            <p class="error">{{ $message }}</p>
                            @enderror

                            @else
                            {{-- 通常表示 --}}
                            {{ $messages->message }}

                            {{-- 編集ボタン --}}
                            <a href="{{ route('transaction.show', $transaction->id) }}?edit={{ $messages->id }}">編集</a>

                            {{-- 削除（POST） --}}
                            <form action="{{ route('message.delete', ['transaction' => $transaction->id, 'message_id' => $messages->id]) }}" method="POST" style="display:inline;">
                            @csrf
                                <button type="submit" onclick="return confirm('削除しますか？')">
                                    削除
                                </button>
                            </form>
                            @endif

                        </div>
                        @if($messages->image_path)
                        <div class="chat__photo">
                            <img src="{{ Storage::url($messages->image_path) }}" alt="chat image">
                        </div>
                        @endif
                    </div>

                </div>

                {{-- 相手のメッセージ --}}
                @else
                <div class="chat__row chat__row--other">

                    <div class="chat__avatar">
                        <img src="{{ $messages->sender->user_img_url
                        ? Storage::url($messages->sender->user_img_url)
                        : asset('img/avatar-default.png') }}">
                    </div>

                    <div class="chat__bubble">
                        <div class="chat__name">
                            {{ $messages->sender->name }}
                        </div>
                        <div class="chat__message">
                            {{ $messages->message }}
                        </div>
                        @if($messages->image_path)
                        <div class="chat__photo">
                            <img src="{{ Storage::url($messages->image_path) }}" alt="chat image">
                        </div>
                        @endif
                    </div>

                </div>
                @endif
                @endforeach

            </div>

            <form class="chat__form" action="{{ route('transaction.message', $transaction) }}" method="POST" enctype="multipart/form-data">
            @csrf
                <input class="chat__input" type="text" name="message" placeholder="取引メッセージを記入してください" value="{{ old('message') }}" required>
                <label class="chat__image">
                    画像を追加
                    <input type="file" name="image" hidden>
                </label>
                <button class="chat__send" type="submit" aria-label="送信">
                    ➤
                </button>
            </form>

        </div>

    </div>

</div>
@endsection
