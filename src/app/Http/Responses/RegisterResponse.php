<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // onboarding は「初回導入」「最初の案内」という意味の英単語
        // true が入っている → 初回フラグがON
        $request->session()->put('onboarding', true);

        return redirect()->route('profile.edit');
    }
}