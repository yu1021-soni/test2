<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Models\Order;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\Message;
use App\Models\Evaluation;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;


class AccountController extends Controller
{
    public function mypage(Request $request) {

        $user = $request->user();

        $tab = $request->query('page', 'sell');
        if (!in_array($tab, ['sell', 'buy', 'transaction'])) {
        $tab = 'sell';}

        $items = null;
        $orders = null;
        $transactions = null;

        if ($tab === 'sell') {
            // 自分が出品した商品。order の件数で SOLD 判定
            $items = Item::withCount('order')
                ->where('user_id', $user->id)->paginate(12);
        } elseif ($tab === 'buy') {
             // 自分が購入した商品（orders）＋商品データ
            $orders = Order::with('item')
                ->where('user_id', $user->id)
                ->paginate(12);
        } elseif ($tab === 'transaction') {
            // 取引中の商品
            $transactions = Transaction::with('item')
            ->withMax('messages', 'created_at') // 最新メッセージ日時を取得
            ->whereIn('status', [
                Transaction::STATUS_IN_PROGRESS,
                Transaction::STATUS_WAITING_RATINGS,])
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                ->orWhere('seller_id', $user->id);
            })
            ->orderByRaw('messages_max_created_at IS NULL ASC')
            ->orderByDesc('messages_max_created_at')
            ->paginate(12);
        }

        $unreadMessageCount = Message::where('receiver_id', $user->id)
            ->where('is_read', 0)
            ->count();

        // 評価の件数を数える
        $ratingCount = Evaluation::where('evaluatee_id', $user->id)
        //その条件に合う 行の数
        ->count();

        $ratingAvgRounded = null;
        // 評価が1件以上ある場合だけ処理
        if ($ratingCount > 0) {
            $query = Evaluation::where('evaluatee_id', $user->id);
            // 平均値を計算して返す
            $avg = $query->avg('rating');
            $ratingAvgRounded = ($avg);
        }

        return view('profile', compact(
            'user', 'tab', 'items', 'orders','transactions', 'unreadMessageCount','ratingCount','ratingAvgRounded'
            ));
    }

    public function edit(Request $request){

        $user = $request->user(); //ログイン中のユーザ取得

        return view('edit',compact('user'));
    }

    public function update(ProfileRequest $request) {

        $user = $request->user();

        // 旧画像パスを保持
        $oldPath = $user->user_img_url;

        // フォーム入力をusersテーブルのカラムへ直接代入
        $user->name     = $request->input('name');
        $user->postcode = $request->input('postcode');
        $user->address  = $request->input('address');
        $user->building = $request->input('building');

        // ファイルが送られているかチェック
        if ($request->hasFile('user_img_url')) {
            $path = $request->file('user_img_url')->store('avatars', 'public');
            $user->user_img_url = $path;
        }

        // 画像が変わったか保存前に判定
        $imgChanged = $user->isDirty('user_img_url');

        $saved = $user->save();

        // 保存成功かつ画像が変更されたときだけ旧ファイル削除
        if ($saved && $imgChanged && $oldPath &&    Storage::disk('public')->exists($oldPath)) {
        Storage::disk('public')->delete($oldPath);
        }

        // 初回導線かどうかでリダイレクト先を変更
        $isOnboarding = $request->session()->pull('onboarding', false);

        return $isOnboarding
            ? redirect()->route('item.index')
            : redirect('mypage');
    }

    public function address(Request $request) {

        $user = $request->user(); // ← ログイン中のユーザー取得

        $itemId = $request->session()->get('checkout.item_id');

        // 商品ごとの下書きに合わせる
        $draft = $itemId ? $request->session()->get("checkout.address.$itemId") : null;

        return view('address', [
            'user' => $user,
            'draft' => $draft,
        ]);
    }

    // 住所変更処理（セッションに保存、DBは更新しない）
    public function change(AddressRequest $request) {

        $validated = $request->validated();
        $itemId = (int)$validated['item_id'];

        // 商品ごとに下書きを保存 checkout.address.{itemId}に配列を保存
        $request->session()->put("checkout.address.$itemId", [
            'postcode' => $validated['postcode'],
            'address'  => $validated['address'],
            'building' => $validated['building'] ?? null,
        ]);

        // purchase へ戻る（item_id はセッションに入っている）
        return redirect()->route('purchase.show');
    }
}
