<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // どの取引のチャットか
            $table->foreignId('transaction_id')
                    ->constrained()
                    ->cascadeOnDelete();

            $table->foreignId('receiver_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

            // 誰が送ったか
            $table->foreignId('sender_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

            // メッセージ本文
            $table->text('message');
            $table->string('image_path')->nullable();

            $table->tinyInteger('is_read')
                ->default(0)
                ->comment('0:未読 1:既読');

            // created_at / updated_at
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
        Schema::dropIfExists('messages');
    }
}
