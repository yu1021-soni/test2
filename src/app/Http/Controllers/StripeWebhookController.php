<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            // Stripe からの通知を確認（署名チェック）
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Throwable $e) {
            return response('Webhook Error', 400);
        }

        // イベントごとに分岐
        if (
            $event->type === 'checkout.session.completed'
            || $event->type === 'checkout.session.async_payment_succeeded'
            || $event->type === 'payment_intent.succeeded'
        ) {

            // 成功したら注文を確定
            $session = $event->data->object;
            $orderId = $session->metadata->order_id ?? null;

            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->update(['payment_status' => 'paid']);
                }
            }
        }

        if (
            $event->type === 'payment_intent.payment_failed'
            || $event->type === 'checkout.session.async_payment_failed'
        ) {

            // 失敗したら注文を失敗にする
            $session = $event->data->object;
            $orderId = $session->metadata->order_id ?? null;

            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->update(['payment_status' => 'failed']);
                }
            }
        }

        return response('OK', 200);
    }
}
