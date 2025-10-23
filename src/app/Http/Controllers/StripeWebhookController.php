<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 受信したリクエストの内容と署名を取得
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            // Stripe公式ライブラリで署名を検証（不正アクセスを防止）
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (\Throwable) {
            // 署名検証に失敗したら「OK」を返して終了（無限リトライ防止）
            return response('OK', 200);
        }

        // -----------------------------
        // 1) Checkout 完了イベント
        // -----------------------------
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;  // CheckoutSession オブジェクト
            $metadata = $session->metadata ? $session->metadata->toArray() : [];

            // Stripe側の支払いステータスをチェック
            $status = ($session->payment_status === 'paid') ? 'paid' : 'pending';

            // 注文をDBに作成
            $this->createOrder(
                $metadata,
                $status,
                $session->id,
                $session->payment_intent
            );
        }

        // -----------------------------
        // 2) 支払い成功イベント（特にカード）
        // -----------------------------
        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;  // PaymentIntent オブジェクト
            $metadata = $paymentIntent->metadata ? $paymentIntent->metadata->toArray() : [];

            // 注文をDBに作成（確定で paid）
            $this->createOrder(
                $metadata,
                'paid',
                null,
                $paymentIntent->id
            );
        }

        return response('OK', 200);
    }

    /**
     * 注文レコードを作成する
     * （同じ PaymentIntent / Session が存在する場合はスキップして二重作成防止）
     */
    private function createOrder(array $metadata, string $status, ?string $sessionId, ?string $paymentIntentId)
    {
        // 重複チェック
        if ($paymentIntentId && Order::where('stripe_payment_intent', $paymentIntentId)->exists()) return;
        if ($sessionId && Order::where('stripe_session_id', $sessionId)->exists()) return;

        // 注文レコードを作成
        Order::create([
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
