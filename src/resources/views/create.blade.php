@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/create.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="listing">

    {{-- enctype="multipart/form-data 画像を送る --}}
    <form action="{{ route('item.listing') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="listing__header">
            <h2 class="listing__title">商品の出品</h2>
        </div>

        <div class="listing__image">
            <h3 class="listing__image-title">商品画像</h3>

            <div class="listing__uploader">
                <input id="item-img" class="listing__uploader-input" type="file" name="item_img_url" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                <label for="item-img" class="listing__uploader-button">画像を選択する</label>
            </div>
            @error('item_img_url')
                <p class="listing__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="listing__detail">
            <div class="listing__section-head">
                <h2 class="listing__subtitle">商品の詳細</h2>
            </div>

            <div class="listing__category">
                <div class="listing__category-title">
                    <h3>カテゴリー</h3>
                </div>

                @foreach ($categories as $cat)
                <input
                    type="checkbox"
                    id="cat{{ $cat->id }}"
                    name="categories[]"
                    value="{{ $cat->id }}"
                    {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}>
                <label for="cat{{ $cat->id }}" class="listing__chip">{{ $cat->name }}</label>
                @endforeach

                @error('categories')
                <p class="listing__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="listing__condition">
                <div class="listing__condition-title">
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
                    <p class="listing__error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="listing__description">
            <div class="listing__section-head">
                <h2 class="listing__subtitle">商品名と説明</h2>
            </div>

            <div class="listing__name">
                <div class="listing__name-title">
                    <h3>商品名</h3>
                </div>
                <input type="text" name="name" class="listing__input" value="{{ old('name') }}">
                @error('name')
                    <p class="listing__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="listing__brand">
                <div class="listing__brand-title">
                    <h3>ブランド名</h3>
                </div>
                <input type="text" name="brand" class="listing__input" value="{{ old('brand') }}">
            </div>

            <div class="listing__text">
                <div class="listing__text-title">
                    <h3>商品の説明</h3>
                </div>
                <textarea name="description">{{ old('description') }}</textarea>
                @error('description')
                    <p class="listing__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="listing__price">
                <div class="listing__price-title">
                    <h3>販売価格</h3>
                </div>
                <input type="text" name="price" class="listing__price-input" value="{{ old('price') }}">
                @error('price')
                    <p class="listing__error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="listing__submit">出品する</button>
    </form>
</div>
@endsection
