<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $seller;
    public $item;

    public function __construct(User $seller, Item $item)
    {
        $this->seller = $seller;
        $this->item = $item;
    }

    public function build()
    {
        return $this->subject('【取引完了】購入者が取引を完了しました')
            ->view('transaction_completed')
            ->with([
                'seller' => $this->seller,
                'item'   => $this->item,
            ]);
    }
}
