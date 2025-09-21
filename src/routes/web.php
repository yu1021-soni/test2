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

    Route::post('/comment', [OrderController::class, 'comment'])->name('comment.store'); // ok

    Route::match(['get','post'], '/mypage', [AccountController::class, 'mypage'])->name('mypage');

    //Route::post('/mypage', [AccountController::class, 'mypage'])->name('mypage'); // ok

    Route::post('/purchase',[OrderController::class,'purchase'])->name('purchase.store'); // ok

    Route::post('/mypage/profile',[AccountController::class,'edit'])->name('profile.edit'); // ok

    Route::get('/mypage/profile', [AccountController::class, 'edit'])->name('profile.view');

    Route::post('/update',[AccountController::class,'update'])->name('profile.update');

    Route::post('/listing',[ItemController::class,'listing'])->name('item.listing');
});