<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;
use App\Models\Profile;
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

    public function purchase ($item_id,Request $request) {

        $item = Item::findOrFail($item_id); // 商品をIDで取得

        $user = $request->user(); // ログイン中のユーザーを取得
        $profile = $user->profile; // ユーザーに紐づくプロフィール取得

        return view ('purchase',compact('item','profile'));
    }
}
