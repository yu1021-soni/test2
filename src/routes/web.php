<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AccountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[ItemController::class,'index'])->name('item.index'); // 一覧 ok
Route::get('/search', [ItemController::class, 'search'])->name('items.search'); // 検索 ?? post ??

Route::get('/detail/{item_id}',[ItemController::class,'detail'])->name('items.detail'); // 商品詳細画面 ok

Route::middleware(['auth'])->group(function () {

    Route::post('/item/favorite', [ItemController::class, 'favorite'])->name('favorites.favorite'); //ok

    Route::post('/comment', [OrderController::class, 'comment'])->name('comment.store');


    Route::get('/mypage', [AccountController::class, 'mypage'])->name('mypage');

    Route::get('/purchase/{item}', [OrderController::class, 'purchase'])->name('purchase.create');

    Route::post('/purchase/{item}', [OrderController::class, 'store'])->name('purchase.store');
});

//Route::post('/item/favorite', [ItemController::class, 'favorite'])
    //->middleware('auth')
//->name('favorites.favorite'); // いいね機能 ok
//Route::delete('item/{item}/favorite',[ItemController::class,'destroy'])->name('favorites.destroy'); // いいねから削除

//Route::middleware('auth')->group(function () {
    //Route::post('/item/comment/{item}', [ItemController::class, 'comment'])->name('comment.store'); // コメント機能 確認中
//});

//Route::middleware('auth')->group(function () {
    //Route::get('/purchase/{item_id}', [OrderController::class, 'purchase'])
    //->middleware('auth')
    //->name('purchase.create'); // 購入画面表示
//});
