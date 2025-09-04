<?php

namespace App\Http\Requests;

use Laravel\Fortify\Http\Requests\LoginRequest as FortifyBase;

class LoginRequest extends FortifyBase
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email'    => ['required','email'],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'メールアドレスを入力してください',
            'email.email'       => 'メールアドレスはメール形式で入力してください',
            'password.required' => 'パスワードを入力してください',
        ];
    }

    public function attributes(): array
    {
        return [
            'email'    => 'メールアドレス',
            'password' => 'パスワード',
        ];
    }
}
