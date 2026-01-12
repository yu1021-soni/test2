<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Item;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (\Throwable) {
            return response('OK', 200);
        }

        // checkout.session.completed だけで処理する
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $metadata = $session->metadata ? $session->metadata->toArray() : [];

            // paid のときだけ
            $status = ($session->payment_status === 'paid') ? 'paid' : 'pending';
            if ($status !== 'paid') {
                return response('OK', 200);
            }

            // 注文作成（Orderを返す）
            $order = $this->createOrder(
                $metadata,
                $status,
                $session->id,
                $session->payment_intent
            );

            // 重複などで作れなかったら終わり
            if (!$order) {
                return response('OK', 200);
            }

            // 出品者を取得するために item を取る
            $item = Item::find($order->item_id);
            if (!$item) {
                return response('OK', 200);
            }

            // transactions 作成
            Transaction::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'item_id'   => $item->id,
                    'buyer_id'  => $order->user_id,
                    'seller_id' => $item->user_id,
                    'status'    => Transaction::STATUS_IN_PROGRESS,
                ]
            );
        }

        return response('OK', 200);
    }

    // Orderを返すようにする
    private function createOrder(array $metadata, string $status, ?string $sessionId, ?string $paymentIntentId): ?Order
    {
        if ($paymentIntentId && Order::where('stripe_payment_intent', $paymentIntentId)->exists()) return null;
        if ($sessionId && Order::where('stripe_session_id', $sessionId)->exists()) return null;

        return Order::create([
            'user_id'   => $metadata['user_id'] ?? null,
            'item_id'   => $metadata['item_id'] ?? null,
            'payment'   => $metadata['payment'] ?? null,
            'postcode'  => $metadata['postcode'] ?? '',
            'address'   => $metadata['address']  ?? '',
            'building'  => $metadata['building'] ?? '',
            'amount'    => $metadata['amount']   ?? 0,
            'payment_status'        => $status,
            'stripe_session_id'     => $sessionId,
            'stripe_payment_intent' => $paymentIntentId,
        ]);
    }
}
