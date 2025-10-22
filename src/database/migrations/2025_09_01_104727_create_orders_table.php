<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('payment')->comment('1:コンビニ払い,2:カード払い');
            $table->string('postcode',8);
            $table->string('address');
            $table->string('building')->nullable();
            $table->unique('item_id');

            // Stripe
            //支払いの進行状況をアプリ側で確認
            $table->string('payment_status')->default('pending');
            //どのセッションでこの注文を処理したか
            $table->string('stripe_session_id')->nullable()->index();
            $table->string('stripe_payment_intent')->nullable()->index();
            $table->integer('amount')->default(0); // 円（金額はCheckout作成時に設定）

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
