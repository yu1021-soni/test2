<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;

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

Route::get('/',[ItemController::class,'index'])->name('item.index'); // 一覧
Route::get('/search', [ItemController::class, 'search'])->name('items.search'); // 検索

Route::get('/detail/{item_id}',[ItemController::class,'detail'])->name('items.detail'); // 商品詳細画面

Route::post('/item/{item}/favorite',[FavoriteController::class,'favorite'])->name('favorites.favorite'); // いいね
//Route::delete('item/{item}/favorite',[FavoriteController::class,'destroy'])->name('favorites.destroy'); // いいねから削除

Route::middleware('auth')->group(function () {
    // 購入画面表示
    Route::get('/purchase/{item_id}', [OrderController::class, 'purchase'])->name('purchase.create');
});