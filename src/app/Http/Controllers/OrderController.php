<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;
use App\Models\Profile;

class OrderController extends Controller
{

    public function purchase ($item_id,Request $request) {

        $item = Item::findOrFail($item_id); // 商品をIDで取得

        $user = $request->user(); // ログイン中のユーザーを取得
        $profile = $user->profile; // ユーザーに紐づくプロフィール取得

        return view ('purchase',compact('item','profile'));
    }
}
