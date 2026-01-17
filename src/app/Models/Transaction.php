<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_WAITING_RATINGS = 'waiting_ratings';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'order_id',
        'item_id',
        'buyer_id',
        'seller_id',
        'status',
        'completed_at',
        'last_message_at',
        'buyer_rated_at',
        'seller_rated_at',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function evaluations() {
        return $this->hasMany(Evaluation::class);
    }

}
