<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // 登録直後にプロフィール編集画面へリダイレクト
        return redirect()->intended(route('profile.view'));
    }
}