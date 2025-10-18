<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => ['required','string','max:20'],
            'user_img_url' => ['nullable', 'file', 'mimes:jpeg,png', 'mimetypes:image/jpeg,image/png','max:5120'],
            'postcode' => ['required', 'string', 'size:8', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required','string','max:255'],
            'building' => ['nullable','string','max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザ名を入力してください',
            'name.string' => 'ユーザ名は文字列で入力してください',
            'name.max' => 'ユーザ名は20文字以下で入力してください',

            'user_img_url.file' => 'プロフィール画像はファイル形式で指定してください',
            'user_img_url.mimes' => 'プロフィール画像は jpeg または png 形式で指定してください',
            'user_img_url.mimetypes'=> 'プロフィール画像は jpeg または png 形式で指定してください',
            'user_img_url.max' => 'プロフィール画像は5MB以下でアップロードしてください',

            'postcode.required' => '郵便番号を入力してください',
            'postcode.string' => '郵便番号は文字列で入力してください',
            'postcode.size' => '郵便番号は8文字で入力してください（例：123-4567）',
            'postcode.regex' => '郵便番号は 123-4567 の形式で入力してください',

            'address.required' => '住所を入力してください',
            'address.string' => '住所は文字列で入力してください',
            'address.max'  => '住所は255文字以下で入力して下さい',

            'building.string' => '建物名は文字列で入力してください',
            'building.max'  => '建物名は255文字以下で入力して下さい',
        ];
    }
}
