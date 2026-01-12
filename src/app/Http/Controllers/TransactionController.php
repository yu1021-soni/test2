<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function  show(Transaction $transaction) {

        $authUser = auth()->user();
        $transaction->load(['item', 'order']);
        $item = $transaction->item;

        // 相手ユーザー（自分がsellerならbuyer、buyerならseller）
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
}
