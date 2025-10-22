<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Stripe\StripeClient;
use App\Http\Requests\PurchaseRequest;

class CheckoutController extends Controller
{
    // 購入処理（Stripe の Checkout ページを作る）
    public function create(PurchaseRequest $request)
    {

        $validated = $request->validated();

        // フォーム入力の確認
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'payment' => 'required|in:1,2', // 1=コンビニ, 2=カード
        ]);

        // 商品を取得
        $item = Item::findOrFail($request->item_id);

        // 金額（円）
        $amount = (int) $item->price;

        // 注文を仮でDBに保存（まだ pending 状態）
        $order = Order::create([
            'user_id' => $request->user()->id,
            'item_id' => $item->id,
            'payment' => $validated['payment'],
            'postcode' => $validated['postcode'],
            'address'  => $validated['address'],
            'building' => $validated['building'] ?? null,
            'amount' => $amount,
            'payment_status' => 'pending',
        ]);

        // Stripe 初期化
        $stripe = new StripeClient(config('services.stripe.secret'));

        // 支払い方法を決定
        $method = $request->payment == 1 ? 'konbini' : 'card';

        // Stripe Checkout セッション作成
        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => [$method],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'success_url' => url('/checkout/success'),
            'cancel_url' => url('/checkout/cancel'),
            'metadata' => [
                'order_id' => $order->id, // Webhookで探すため
            ],
        ]);

        // 注文にセッションIDを保存
        $order->update(['stripe_session_id' => $session->id]);

        // Stripeの決済ページへリダイレクト
        return redirect($session->url);
    }

    public function success()
    {
        return "決済が完了しました（本当の確定はWebhookで行います）";
    }

    public function cancel()
    {
        return "決済をキャンセルしました";
    }
}
