<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\PurchaseRequest;

class OrderController extends Controller
{
    public function comment(CommentRequest $request) {

        // バリデーション済みデータを取得
        $validated = $request->validated();

        // ログインユーザーのコメントを作成
        $request->user()->comments()->create([
            'item_id' => $validated['item_id'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('message', 'コメントを投稿しました');
    }

    public function purchase(Request $request) {

        // ① $request->input('item_id') フォームから送られてきた item_id を探す
        $itemId = $request->input('item_id') ?? $request->session()->get('checkout.item_id');

        // ② セッションに保持
        $request->session()->put('checkout.item_id', $itemId);

        $item    = Item::findOrFail($itemId);
        $user    = $request->user();
        $profile = $user?->profile;

        // ③ 住所下書きは 商品ごと に保持
        $draft = $request->session()->get("checkout.address.$itemId");

        return view('purchase', compact('item', 'user', 'profile', 'draft'));
    }

    public function pay (PurchaseRequest $request) {

        $validated = $request->validated();

        // ① 購入する商品IDを受け取る
        $itemId = $request->input('item_id');
        $item   = Item::findOrFail($itemId);

        // ② ログインユーザーを取得
        $user = $request->user();

        // ③ 注文データをDBに登録
        $order = Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment' => $validated['payment'],
            'shipping' => $validated['shipping'],
        ]);

        // ④ 完了画面やマイページにリダイレクト
        return redirect()->route('mypage')
                        ->with('message', '購入が完了しました！');
    }
}
