@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css')}}">
@endsection

@section('content')
<div class="content">
    <form action="">
        @csrf

        <div class="title">
            <h2>商品の出品</h2>
        </div>

        <div class="item__img">
            <p>商品画像</p>
            <p>画像を選択する</p>
        </div>

        <div class="item__detail">
            <div class="detail___title">
                <h2>商品の詳細</h2>
            </div>
            <div class="category">
                <div class="category__title">
                    <h3>カテゴリー</h3>
                </div>
                @foreach ($categories as $cat)
                <input type="checkbox" id="cat{{ $cat->id }}" name="categories[]" value="{{ $cat->id }}" >
                <label for="cat{{ $cat->id }}" class="chip">{{ $cat->name }}</label>
                @endforeach

            </div>
            <div class="condition">
                <div class="condition__title">
                    <h3>商品の状態</h3>
                </div>
                <select name="condition" form="" required>
                    <option value="" selected disabled>選択してください</option>
                    <option value="1">良好</option>
                    <option value="2">目立った傷や汚れなし</option>
                    <option value="3">やや傷や汚れあり</option>
                    <option value="4">状態が悪い</option>
                </select>
            </div>
        </div>

        <div class="item__description">
            <div class="description__title">
                <h2>商品名と説明</h2>
            </div>
            <div class="name">
                <div class="name__title">
                    <h3>商品名</h3>
                </div>
                <input type="text" name="name">
            </div>
            <div class="brand">
                <div class="brand__title">
                    <h3>ブランド名</h3>
                </div>
                <input type="text" name="brand">
            </div>
            <div class="item__text">
                <div class="text__title">
                    <h3>商品の説明</h3>
                </div>
                <textarea name="description"></textarea>
            </div>
            <div class="item__price">
                <div class="price__title">
                    <h3>販売価格</h3>
                </div>
                <input type="text" name="price">
            </div>
        </div>
        <button type="submit">出品する</button>

    </form>
</div>
@endsection