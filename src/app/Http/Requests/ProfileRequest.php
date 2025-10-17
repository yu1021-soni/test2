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
            'name.required'     => 'ユーザ名を入力してください',
            'postcode.required' => '郵便番号を入力してください',
            'postcode.regex'    => '郵便番号は 123-4567 の形式で入力してください',
            'address.required'  => '住所を入力してください',
            'address.max'  => '住所は255文字以下で入力して下さい',
            'building.max'  => '建物名は255文字以下で入力して下さい',
        ];
    }
}
