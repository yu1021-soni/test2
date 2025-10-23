<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Stripe\StripeClient;
use App\Http\Requests\PurchaseRequest;

class CheckoutController extends Controller
{
    public function create(PurchaseRequest $request) {
        $data   = $request->validated();

        //商品ID検索
        $item   = Item::findOrFail($data['item_id']);

        //商品価格 整数化
        $amount = (int) $item->price;

        $method = ($data['payment'] == 1) ? 'konbini' : 'card';

        $stripe = new StripeClient(config('services.stripe.secret'));

        //注文に必要な情報
        $meta = [
            'user_id'  => $request->user()->id,
            'item_id'  => $item->id,
            'payment'  => $data['payment'],
            'postcode' => $data['postcode'],
            'address'  => $data['address'],
            'building' => $data['building'] ?? '',
            'amount'   => $amount,
        ];

        $params = [
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
            'cancel_url'  => url('/checkout/cancel'),
            'metadata' => $meta,
            'payment_intent_data' => ['metadata' => $meta],
        ];

        //コンビニ払いの場合 メール必須
        if ($method === 'konbini') {
            $params['customer_email'] = $request->user()->email;
        }

        //StripeにCheckoutセッションを作成依頼
        $session = $stripe->checkout->sessions->create($params);

        //専用決済ページへリダイレクト
        return redirect($session->url);
    }

    public function success() {
        return view('stripe_success');
    }

    public function cancel() {
        return view('stripe_cancel');
    }
}
