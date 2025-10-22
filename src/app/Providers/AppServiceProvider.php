<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

//ログアウト
use Laravel\Fortify\Contracts\LogoutResponse;
use App\Http\Responses\LogoutResponse as CustomLogoutResponse;

//ログインバリデーション
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest as MyLoginRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(LogoutResponse::class, CustomLogoutResponse::class);

        $this->app->bind(FortifyLoginRequest::class, MyLoginRequest::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
