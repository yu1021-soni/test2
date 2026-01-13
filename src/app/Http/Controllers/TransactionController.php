<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function  show(Transaction $transaction, Request $request) {

        $authUser = auth()->user();
        $transaction->load(['item', 'order','messages.sender']);
        $item = $transaction->item;

        // 相手ユーザー（自分がsellerならbuyer、buyerならseller）
        // ? : は三項演算子（もし〜ならA、ちがうならB）
        $partnerId = ($authUser->id === $transaction->seller_id)
            ? $transaction->buyer_id
            : $transaction->seller_id;

        $user = User::findOrFail($partnerId);

        $transactions = Transaction::with('item')
            ->where('seller_id', $authUser->id)   // 自分が seller
            ->where('id', '!=', $transaction->id) // 今表示中の取引は除外
            ->get();

        $editMessageId = $request->query('edit');

        Message::where('transaction_id', $transaction->id)
        ->where('receiver_id', $authUser->id)
        ->where('is_read', 0)
        ->update(['is_read' => 1]);

        return view ('transaction', compact('item','user','authUser', 'transaction', 'transactions','editMessageId'));
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

        // ✅ 追加：送信者ID
        $senderId = auth()->id();

        // ✅ 相手（受信者）IDを決める
        $receiverId = ($senderId === $transaction->seller_id)
        ? $transaction->buyer_id
        : $transaction->seller_id;

        // ④ DBに保存
        Message::create([
            'transaction_id' => $transaction->id,
            'receiver_id'    => $receiverId,
            'sender_id'      => $senderId,
            'message'        => $request->input('message', ''),
            'image_path'     => $imagePath,
            'is_read'        => 0,
        ]);

        // ⑤ 元の取引画面に戻る
        return redirect()->route('transaction.show', $transaction);
    }

    public function edit(Request $request, Transaction      $transaction, $message_id) {

        // ① メッセージが入力されているかチェック
        $request->validate(
            ['message' => 'required|string|max:1000'],
            ['message.required' => 'メッセージを入力してください']
        );

        // ② 編集したいメッセージを探す
        $message = Message::find($message_id);

        // ③ メッセージが存在しない、取引のものじゃない場合
        if (!$message) {
            abort(404);
        }

        if ($message->transaction_id !== $transaction->id) {
            abort(404);
        }

        // ④ 自分のメッセージかどうかチェック
        if ($message->sender_id !== auth()->id()) {
            abort(403);
        }

        // ⑤ メッセージ内容を更新
        $message->message = $request->message;
        $message->save();

        // ⑥ 取引画面に戻る
        return redirect()
            ->route('transaction.show', $transaction->id)
            ->with('success', 'メッセージを編集しました');
    }

    public function delete(Transaction $transaction, $message_id) {
        
        // ① 削除したいメッセージを探す
        $message = Message::find($message_id);

        // ②メッセージが存在しない、取引のものじゃない場合
        if (!$message) {
            abort(404);
        }

        if ($message->transaction_id !== $transaction->id) {
            abort(404);
        }

        // ③ 自分のメッセージかどうかチェック
        if ($message->sender_id !== auth()->id()) {
            abort(403);
        }

        // ④ メッセージを削除
        $message->delete();

        // ⑤ 取引画面に戻る
        return redirect()
            ->route('transaction.show', $transaction->id)
            ->with('success', 'メッセージを削除しました');
    }
}
