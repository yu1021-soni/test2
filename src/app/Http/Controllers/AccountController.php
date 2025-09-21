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

        $user = $request->user();  //ログイン中のユーザ取得

        // GET/POST 両対応で受け取る
        $tab = $request->input('page', 'sell');
        if (!in_array($tab, ['sell','buy'], true)) $tab = 'sell';

        $items  = Item::where('user_id', $user->id)->latest('id')->get();
        $orders = Order::with('item')->where('user_id', $user->id)->latest('id')->get();

        // 出品した商品（user_idが自分のものを取得）
        //$items = Item::where('user_id', $user->id)->get();

        // 購入した商品（user_idが自分のものをordersテーブルから取得)
        //$orders = Order::with('item')
                //->where('user_id',$user->id)->get();

        // これは購入履歴しか取れない 商品情報にアクセスできない
        // $orders = Order::where('user_id', $user->id)->get();

        return view ('profile',compact('user','items','orders','tab'));
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
