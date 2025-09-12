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
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id')->withTimestamps();
    }

    public function order() {
        return $this->hasOne(Order::class);
    }

    public function categories() {
    return $this->belongsToMany(Category::class);
    }

     // 検索用のスコープ
    public function scopeSearch($query, $keyword) {
        // キーワードが空の場合は全件取得
        if (empty($keyword)) {
            return $query;
        }
        // 名前で部分一致検索
        return $query->where('name', 'like', "%{$keyword}%");
    }
}
