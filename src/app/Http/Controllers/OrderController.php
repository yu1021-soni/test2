<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Requests\CommentRequest;

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
        $user = $request->user();

        // ③ 住所下書きは 商品ごと に保持
        $draft = $request->session()->get("checkout.address.$itemId");

        return view('purchase', compact('item', 'user', 'draft'));
    }
}
