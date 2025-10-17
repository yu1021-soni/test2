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
    return $this->belongsToMany(Category::class,'category_items')->withTimestamps();
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

    // 数値コード → 日本語ラベルの対応
    public const CONDITION_LABELS = [
        1 => '良好',
        2 => '目立った傷や汚れなし',
        3 => 'やや傷や汚れあり',
        4 => '状態が悪い',
    ];

    // Bladeで $item->condition_label で読める
    public function getConditionLabelAttribute(): string {
        return self::CONDITION_LABELS[$this->condition] ?? '未設定';
    }
}
