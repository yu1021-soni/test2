<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;


class AccountController extends Controller
{
    public function mypage(Request $request) {

        $user = $request->user();

        // ?page=sell | buy（それ以外は sell）
        $tab = $request->query('page');
        $tab = $tab === 'buy' ? 'buy' : 'sell';

        if ($tab === 'sell') {
            // 自分が出品した商品。order の件数で SOLD 判定
            $items = Item::withCount('order')
                ->where('user_id', $user->id)->paginate(12);
            $orders = null;
        } else {
             // 自分が購入した商品（orders）＋商品データ
            $orders = Order::with('item')
                ->where('user_id', $user->id)
                ->paginate(12);
            $items = null;
        }

    return view('profile', compact('user', 'tab', 'items', 'orders'));
    }

    public function edit(Request $request){

        $user = $request->user(); //ログイン中のユーザ取得

        return view('edit',compact('user'));
    }

    public function update(ProfileRequest $request) {

        $user = $request->user(); // ← ログイン中のユーザー取得

        // フォームからきた値を代入
        $user->name = $request->input('name');
        $user->postcode = $request->input('postcode');
        $user->address = $request->input('address');
        $user->building = $request->input('building');

        // 任意（ファイルがあるときだけ処理）
        if ($request->hasFile('user_img_url')) {
            $path = $request->file('user_img_url')->store('avatars', 'public');
            $user->user_img_url = $path;
        }

        $user->save(); // 保存

        return redirect('mypage');
    }
}
