<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request) {

        //  ログインしてたらユーザーID、してなければ null
        $userId = optional($request->user())->id;

        // ① ベースのクエリを作る
        $query = Item::query();

        // ② 自分の出品を除外（ログイン時だけ）
        if (!is_null($userId)) {
            $query->where('user_id', '!=', $userId);
        }

        // ③ 「自分が買った注文」だけ
        if (!is_null($userId)) {
            $query->with([
                'order' => fn($q) => $q->where('user_id', $userId)
            ]);
        }

        //  orderBy('id', 'desc') 並び順を指定
        $items = $query->orderBy('id', 'desc')->get();

        return view('index', compact('items'));
    }
}
