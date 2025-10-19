@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/create.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="content__listing">

    {{-- enctype="multipart/form-data 画像を送る --}}
    <form action="{{ route('item.sell') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="title">
            <h2 class ="h2__title">商品の出品</h2>
        </div>

        <div class="item__img">
            <h3 class="img__title">商品画像</h3>

            <div class="uploader">
                <input id="item-img" class="uploader__input" type="file" name="item_img_url" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                <label for="item-img" class="uploader__button">画像を選択する</label>
            </div>
            @error('item_img_url')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="item__detail">
            <div class="detail__title">
                <h2 class="sub__title">商品の詳細</h2>
            </div>
            <div class="category">
                <div class="category__title">
                    <h3>カテゴリー</h3>
                </div>
                @foreach ($categories as $cat)
                <input type="checkbox"
                    id="cat{{ $cat->id }}"
                    name="categories[]"
                    value="{{ $cat->id }}"
                    {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}>
                <label for="cat{{ $cat->id }}" class="chip">{{ $cat->name }}</label>
                @endforeach

                @error('categories')
                    <p class="error">{{ $message }}</p>
                @enderror

            </div>
            <div class="condition">
                <div class="condition__title">
                    <h3>商品の状態</h3>
                </div>
                <select name="condition">
                    <option value="" disabled {{ old('condition') ? '' : 'selected' }}>選択してください</option>
                    <option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>良好</option>
                    <option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                    <option value="3" {{ old('condition') == 3 ? 'selected' : '' }}>やや傷や汚れあり</option>
                    <option value="4" {{ old('condition') == 4 ? 'selected' : '' }}>状態が悪い</option>
                </select>
                @error('condition')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="item__description">
            <div class="description__title">
                <h2 class="sub__title">商品名と説明</h2>
            </div>
            <div class="name">
                <div class="name__title">
                    <h3>商品名</h3>
                </div>
                <input type="text" name="name" class="box" value="{{ old('name') }}">
                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="brand">
                <div class="brand__title">
                    <h3>ブランド名</h3>
                </div>
                <input type="text" name="brand" class="box" value="{{ old('brand') }}">
            </div>
            <div class="item__text">
                <div class="text__title">
                    <h3>商品の説明</h3>
                </div>
                <textarea name="description">{{ old('description') }}</textarea>
                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="item__price">
                <div class="price__title">
                    <h3>販売価格</h3>
                </div>
                <input type="text" name="price" class="price__box" value="{{ old('price') }}">
                @error('price')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <button type="submit" class="sell__button">出品する</button>

    </form>
</div>
@endsection