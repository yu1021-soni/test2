<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function favorite(Item $item) {
        $user = auth()->user(); //$userの中にユーザー情報が入る

        if($user->favorites()->where('item_id',$item->id)->exists()) {
            $user->favorites()->detach($item->id); // すでにいいねしてたら削除
        } else {
            $user->favorites()->attach($item->id); // いいねしてなければ追加
        }

        return back(); 
    }
}
