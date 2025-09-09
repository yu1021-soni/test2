<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'item_img_url',
        'name',
        'price',
        'description',
        'condition',
        'brand',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function favorites() {
        return $this->hasMany(Favorite::class);
    }

    public function order() {
        return $this->hasOne(Order::class);
    }

    public function categories() {
    return $this->belongsToMany(Category::class);
    }
}
