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
            'address' => ['required','string'],
            'building' => ['nullable','string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'ユーザ名は必須です。',
            'postcode.required' => '郵便番号は必須です。',
            'postcode.regex'    => '郵便番号は 123-4567 の形式で入力してください。',
            'address.required'  => '住所は必須です。',
        ];
    }
}
