<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'payment' => ['required'],
            'postcode' => ['required','string','size:8','regex:/^\d{3}-\d{4}$/'],
            'address'  => ['required','string','max:255'],
            'building' => ['nullable','string','max:255'],
        ];
    }

    public function messages() {
        return [
            'payment.required' => '支払い方法をお選びください',

            'postcode.required' => '郵便番号を入力してください',
            'postcode.string' => '郵便番号は文字列で入力してください',
            'postcode.size' => '郵便番号は8文字で入力してください（例：123-4567）',
            'postcode.regex' => '郵便番号は 123-4567 の形式で入力してください',

            'address.required' => '住所を入力してください',
            'address.string' => '住所は文字列で入力してください',
            'address.max' => '住所は255文字以下で入力してください',

            'building.string' => '建物名は文字列で入力してください',
            'building.max' => '建物名は255文字以下で入力してください',
        ];
    }
}
