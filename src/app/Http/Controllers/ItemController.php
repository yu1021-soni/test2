<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Requests\CommentRequest;

class ItemController extends Controller
{
    public function index(Request $request) {

        //query('tab') URL のクエリパラメータの ?tab=mylist fを探す
        $tab = $request->query('tab');
        //  ログインしてたらユーザーID、してなければ null
        $userId = optional($request->user())->id;

        // ① ベースのクエリを作る
        $query = Item::query();

        // ② 自分の出品を除外
        if ($userId !== null) {
            // whereは条件を絞り込むメソッド
            $query->where('user_id', '!=', $userId);
        }

        // ③ マイリスト
        if ($tab === 'mylist') {  //?tab=mylistの時だけ実行
            // ユーザIDがnullじゃなかったら
            if ($userId == null) {
                $items = collect(); //collect() 空の配列
                return view('index', compact('items', 'tab'));
            }
            // いいねした商品だけ
            // whereHas 関連テーブルに条件をつけるメソッド
            $query->whereHas('favorites', fn($q) => $q->where('user_id', $userId));
        }

        // ④ 「自分が買った注文」だけ
        if ($userId !== null) {
            $query->with([
                'order' => fn($q) => $q->where('user_id', $userId)
            ]);
        }

        //  orderBy('id', 'desc') 並び順を指定
        $items = $query->with('categories') ->orderBy('id', 'desc')->get();

        return view('index', compact('items','tab'));
    }

    public function search(Request $request) {
        $keyword = $request->query('keyword');

        $items = Item::query()
            ->with('order')
            ->search($keyword)
            ->orderBy('id')
            ->paginate(16);

        return view('index',compact('items'));
    }

    public function detail($item_id) {

        $item = Item::with([
            'categories',
            'comments.user', // コメント表示でユーザー名を出すので同時読込
            ])
        ->withCount(['order','comments','favorites'])
        ->findOrFail($item_id);

        return view ('detail',compact('item'));
    }

    public function favorite(Item $item) {
        $user = auth()->user(); //$userの中にユーザー情報が入る

        if($user->favorites()->where('item_id',$item->id)->exists()) {
            $user->favorites()->detach($item->id); // すでにいいねしてたら削除
        } else {
            $user->favorites()->attach($item->id); // いいねしてなければ追加
        }

        return back(); 
    }

    public function comment(CommentRequest $request,Item $item) {
        
        $validated = $request->validated(); // バリデーション済みデータを取得

        $item->comments()  // 商品のコメント一覧を開く
             ->create([ // コメント一覧に新しいコメントを追加
                'user_id' => $request->user()->id, // コメントを書いた人
                'comment' => $validated['comment'], // コメント本文
            ]);
        
            return back()->with('message', 'コメントを投稿しました。');
    }
}
