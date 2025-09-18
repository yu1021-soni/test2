<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;


class AccountController extends Controller
{
    public function mypage(Request $request) {

        $user = $request->user();  //ログイン中のユーザ取得

        // 出品した商品（user_idが自分のものを取得）
        $items = Item::where('user_id', $user->id)->get();

        // 購入した商品（user_idが自分のものをordersテーブルから取得)
        $orders = Order::with('item')
                ->where('user_id',$user->id)->get();

        // これは購入履歴しか取れない 商品情報にアクセスできない
        // $orders = Order::where('user_id', $user->id)->get();

        return view ('profile',compact('user','items','orders'));
    }
}
