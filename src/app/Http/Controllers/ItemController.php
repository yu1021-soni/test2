<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Favorite;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request) {

    // ?tab=mylist を取得
    $tab = $request->query('tab');
    // ログインしてたらユーザーID、してなければ null
    $user   = $request->user();
    $userId = optional($user)->id;

    // ① ベースのクエリ
    $query = Item::query();

    // ② 自分の出品を除外（ログイン時のみ）
    if ($userId !== null) {
        $query->where('user_id', '!=', $userId);
    }

    // ③ マイリスト（自分がいいねした商品だけ）
    if ($tab === 'mylist') {
        if ($userId === null) {
            $items = collect(); // 空コレクション
            return view('index', compact('items', 'tab'));
        }
        $query->whereHas('favorites', fn($q) => $q->where('user_id', $userId));
    }

    // ④ 「自分が買った注文」だけ（user_id で絞る）
    if ($userId !== null) {
        $query->with([
            'order' => fn($q) => $q->where('user_id', $userId),
        ]);
    }

    // いいいね件数を一緒に取得（favorites_count）
    $query->withCount('favorites');

    // ⑤ カテゴリも取得して、並び順指定
    $items = $query->with('categories')
                    ->orderByDesc('id')
                    ->paginate(16);

    $favoritedIds = $userId
        ? \App\Models\Favorite::where('user_id', $userId)->pluck('item_id')->all()
        : [];

    // 各アイテムに is_favorited を true/false で
    foreach ($items as $item) {
        $item->is_favorited = in_array($item->id, $favoritedIds, true);
    }

    return view('index', compact('items', 'tab'));
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
                'comments.user', // コメント表示でユーザー名を出す
        ])
            ->withCount(['order', 'comments', 'favorites'])
            ->findOrFail($item_id);

        $userId = auth()->id(); // 未ログインなら null

        // いいね済みか判定
        $item->is_favorited = $userId
            ? Favorite::where('user_id', $userId)
                ->where('item_id', $item->id)
                ->exists()
            : false;

        return view('detail', compact('item'));
    }

    public function favorite(Request $request) {
        $request->validate([
        'item_id' => ['required', 'integer', 'exists:items,id'],
        ]);

        $request->user()->favorites()->toggle((int) $request->item_id);

        return back();
    }

    public function listing(Request $request) {
        $user = $request -> user();
        $categories = Category::all();

        return view('create',compact('user','categories'));
    }

    public function sell(ExhibitionRequest $request) {

         // バリデーション済みデータを取得
        $validated = $request->validated();

        $user = $request -> user();

        //Storage::disk('public') ファイル保存先指定
        //'items' 第一引数は保存先フォルダ
        //$validated['item_img_url'] 第二引数はアップロードされたファイル
        $img = Storage::disk('public')->putFile('items', $validated['item_img_url']);

        // 代表カテゴリ＝先頭
        $mainCategoryId = (int) $validated['categories'][0];


        // items へ保存（DBにあるカラムだけ入れる）
        $item = Item::create([
            'user_id'      => $user->id,
            'name'         => $validated['name'],
            'description'  => $validated['description'],
            'price'        => $validated['price'],
            'condition'    => $validated['condition'],
            'item_img_url' => $img,
        ]);

    // 多対多（category_item）へ全カテゴリを保存
    //sync ピボットテーブルを渡した配列と同じ状態にする
    $item->categories()->sync($validated['categories']);

    return redirect()->route('item.index');
    }
}