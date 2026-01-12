<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function  show(Transaction $transaction) {

        $authUser = auth()->user();
        $transaction->load(['item', 'order']);
        $item = $transaction->item;

        // 相手ユーザー（自分がsellerならbuyer、buyerならseller）
        // ? : は三項演算子（もし〜ならA、ちがうならB）
        $partnerId = ($authUser->id === $transaction->seller_id)
            ? $transaction->buyer_id
            : $transaction->seller_id;

        $user = User::findOrFail($partnerId);

        $transactions = Transaction::with('item')
            // 今表示している取引は除外
            ->where('id', '!=', $transaction->id)
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
                })
            ->get();

        return view ('transaction', compact('item','user','authUser', 'transaction', 'transactions'));
    }

    public function messages(Request $request, Transaction $transaction) {

        // ① バリデーション
        $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
            'image'   => ['nullable', 'image', 'max:2048'], // 2MBまで
        ]);

        // ② メッセージも画像も無い送信は禁止
        if (!$request->filled('message') && !$request->hasFile('image')) {
            return back()
                ->withErrors(['message' => 'メッセージまたは画像を入力してください'])
                ->withInput();
        }

        // ③ 画像があれば保存
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        // ④ DBに保存
        Message::create([
            'transaction_id' => $transaction->id,
            'sender_id'      => auth()->id(),
            'message'        => $request->input('message', ''),
            'image_path'     => $imagePath,
        ]);

        // ⑤ 元の取引画面に戻る
        return redirect()->route('transaction.show', $transaction);
    }

}
