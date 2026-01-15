<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Message;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Http\Requests\MessageRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

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

        // 既読
        Message::where('transaction_id', $transaction->id)
        ->where('receiver_id', $authUser->id)
        ->where('is_read', 0)
        ->update(['is_read' => 1]);

        // ✅ 自分がすでに評価したか？
        $alreadyRated = Evaluation::where('transaction_id', $transaction->id)
            ->where('evaluator_id', $authUser->id)
            ->exists();

        // ✅ モーダルを開く条件
        $openRatingModal =
            // 手動（?modal=rating）
            ($request->query('modal') === 'rating')
            // 自動（評価待ち＆自分が未評価）
            || (
                $transaction->status === Transaction::STATUS_WAITING_RATINGS
                && !$alreadyRated
        );

        return view ('transaction', compact('item','user','authUser', 'transaction', 'transactions','editMessageId', 'openRatingModal'));
    }

    public function messages(MessageRequest $request, Transaction $transaction) {

        // 画像があれば保存
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        // 送信者ID
        $senderId = auth()->id();

        // 受信者IDを決める
        $receiverId = ($senderId === $transaction->seller_id)
        ? $transaction->buyer_id
        : $transaction->seller_id;

        // DBに保存
        Message::create([
            'transaction_id' => $transaction->id,
            'receiver_id'    => $receiverId,
            'sender_id'      => $senderId,
            'message'        => $request->message,
            'image_path'     => $imagePath,
            'is_read'        => 0,
        ]);

        // ⑤ 元の取引画面に戻る
        return redirect()->route('transaction.show', $transaction);
    }

    public function edit(Request $request, Transaction $transaction, $message_id) {

        // ① メッセージが入力されているかチェック
        $request->validate(
            ['message' => 'required|string|max:400'],
            [
                'message.required' => '本文を入力してください',
                'message.max' => '本文は400文字以内で入力してください',
            ]
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

    public function complete(Transaction $transaction) {

        // 取引を「評価待ち」にする
        $transaction->status = Transaction::STATUS_WAITING_RATINGS;
        $transaction->save();

        // 出品者と商品を取得
        $seller = User::findOrFail($transaction->seller_id);
        $transaction->load('item');

        // Mail::to(宛先)->send(new Mailableクラス());
        // 出品者へメール送信
        Mail::to($seller->email)->send(
            new TransactionCompletedMail($seller, $transaction->item)
        );

        // 評価モーダルを開く
        return redirect()->route('transaction.show', [
            'transaction' => $transaction->id,
            'modal' => 'rating',
        ]);
    }

    public function evaluation(Transaction $transaction,Request $request) {

        $authId = auth()->id();

        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        if ($authId === $transaction->buyer_id) {
            $evaluateeId = $transaction->seller_id;
        } else {
            $evaluateeId = $transaction->buyer_id;
        }

        Evaluation::create([
        'transaction_id' => $transaction->id,
        'evaluator_id'   => $authId,
        'evaluatee_id'   => $evaluateeId,
        'rating'         => $request->rating,
        ]);

        // 両者が評価したかチェック
        $buyerRated = Evaluation::where('transaction_id', $transaction->id)
            ->where('evaluator_id', $transaction->buyer_id)
            ->exists();

        $sellerRated = Evaluation::where('transaction_id', $transaction->id)
            ->where('evaluator_id', $transaction->seller_id)
            ->exists();

        // status 更新
        if ($buyerRated && $sellerRated) {
            $transaction->status = Transaction::STATUS_COMPLETED;
        } else {
            $transaction->status = Transaction::STATUS_WAITING_RATINGS;
        }

        $transaction->save();

        return redirect()
            ->route('item.index');
    }
}
