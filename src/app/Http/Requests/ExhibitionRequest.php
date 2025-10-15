<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'description' => ['required','max:255'],
            'item_img_url' => ['required','file','mimes:jpeg,png','mimetypes:image/jpeg,image/png','max:5120'],
            'categories'   => ['required','array','min:1'],
            'categories.*' => ['integer','exists:categories,id'],
            // *各要素のに対してのルール決め
            'condition' => ['required'],
            'price' => ['required','numeric','min:0'],
        ];
    }

    public function messages() {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は255文字以内で入力してください',
            'item_img_url.required' => '画像を選択してください',
            'categories.required' => 'カテゴリーを１つ以上選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '販売価格を入力してください',
            'price.numeric' => '販売価格は半角数値で入力してください',
        ];
    }
}
