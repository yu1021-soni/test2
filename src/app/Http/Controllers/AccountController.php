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

        $oldPath = $user->user_img_url;

        // ファイルが送られているかチェック
        if ($request->hasFile('user_img_url')) {
            $path = $request->file('user_img_url')->store('avatars', 'public');
            $user->user_img_url = $path;
        }

        // 画像が変わったか保存前に判定
        $imgChanged = $user->isDirty('user_img_url');

        $saved = $user->save();

        $user->save(); // 保存

        // 保存成功かつ画像が変更されたときだけ旧ファイル削除
        if ($saved && $imgChanged && $oldPath && Storage::disk('public')->exists($oldPath)) {
        Storage::disk('public')->delete($oldPath);
        }

        return redirect('mypage');
    }

    public function address($user_id,Request $request) {

        $user = $request->user(); // ← ログイン中のユーザー取得

        $draft = $request->session()->get('checkout.address');

        return view('address', [
            'user' => $user,
            'draft' => $draft,
        ]);
    }

    // 住所変更処理（セッションに保存、DBは更新しない）
    public function change(Request $request) {
        $validated = $request->validate([
            'item_id'  => ['required','integer','exists:items,id'],
            'postcode' => ['required','regex:/^\d{3}-\d{4}$/'],
            'address'  => ['required','string'],
            'building' => ['nullable','string'],
        ]);

        $itemId = $validated['item_id'];

        // 商品ごとに下書きを保存 checkout.address.{itemId}に配列を保存
        $request->session()->put("checkout.address.$itemId", [
            'postcode' => $validated['postcode'],
            'address'  => $validated['address'],
            'building' => $validated['building'] ?? null,
        ]);

        // purchase へ戻る（item_id はセッションに入っている）
        return redirect()->route('purchase.store');
    }
}
