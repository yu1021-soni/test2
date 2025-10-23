<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig     = $request->header('Stripe-Signature');
        $secret  = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig, $secret);
        } catch (\Throwable $e) {
            return response('OK', 200);
        }

        // Checkout 完了
        if ($event->type === 'checkout.session.completed') {
            $s = $event->data->object;
            $m = $s->metadata ? $s->metadata->toArray() : [];
            $this->createOrder($m, ($s->payment_status === 'paid') ? 'paid' : 'pending', $s->id, $s->payment_intent);
        }

        // 支払い成功
        if ($event->type === 'payment_intent.succeeded') {
            $pi = $event->data->object;
            $m  = $pi->metadata ? $pi->metadata->toArray() : [];
            $this->createOrder($m, 'paid', null, $pi->id);
        }

        return response('OK', 200);
    }

    private function createOrder(array $m, string $status, ?string $sessionId, ?string $piId)
    {
        if ($piId && Order::where('stripe_payment_intent', $piId)->exists()) return;
        if ($sessionId && Order::where('stripe_session_id', $sessionId)->exists()) return;

        Order::create([
            'user_id'   => $m['user_id'] ?? null,
            'item_id'   => $m['item_id'] ?? null,
            'payment'   => $m['payment'] ?? null,
            'postcode'  => $m['postcode'] ?? '',
            'address'   => $m['address']  ?? '',
            'building'  => $m['building'] ?? '',
            'amount'    => $m['amount'] ?? 0,
            'payment_status'        => $status,
            'stripe_session_id'     => $sessionId,
            'stripe_payment_intent' => $piId,
        ]);
    }
}
