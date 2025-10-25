<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CheckoutController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\StripeWebhookController;

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

Route::get('/',[ItemController::class,'index'])
 ->middleware('force.verified')->name('item.index'); // 一覧 ok
Route::get('/search', [ItemController::class, 'search'])->name('items.search'); //検索

Route::get('/detail/{item_id}',[ItemController::class,'detail'])->name('items.detail'); // 商品詳細画面

Route::middleware(['auth','verified'])->group(function () {

    Route::post('/item/favorite', [ItemController::class, 'favorite'])->name('favorites.favorite'); // いいね

    Route::post('/comment', [OrderController::class, 'comment'])->name('comment.store'); // コメント

    Route::match(['get','post'], '/mypage', [AccountController::class, 'mypage'])->name('mypage');
    //　マイページ

    Route::post('/purchase', [OrderController::class, 'purchase'])->name('purchase.store');

    // 会員登録処理が正常終了したとき
    Route::get('/mypage/profile', [AccountController::class, 'edit'])->name('profile.view');

    Route::post('/mypage/profile',[AccountController::class,'edit'])->name('profile.edit');

    Route::post('/update',[AccountController::class,'update'])->name('profile.update'); // プロフィール更新

    Route::get('/listing',[ItemController::class,'listing'])->name('item.listing'); // 出品

    Route::post('/sell',[ItemController::class,'sell'])->name('item.sell'); // マイページ出品するボタン

     // 住所変更ページ (GET)
    Route::get('/address/{user_id}', [AccountController::class, 'address'])->name('address.edit');

    // 住所変更処理 (POST)
    Route::post('/change', [AccountController::class, 'change'])->name('address.change');


    Route::post('/pay', [CheckoutController::class, 'create'])->name('item.pay');

    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/checkout/cancel',  [CheckoutController::class, 'cancel'])->name('checkout.cancel');

});

Route::withoutMiddleware([VerifyCsrfToken::class])
    ->post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');
