<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Encryption\Encrypter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Forcer la clé d'application correcte (décodée depuis base64)
        $encodedKey = 'base64:YJgmtLJEpaemj5Z8QuK0OxXQ4bwzakaq5dqganLW8BM=';
        $correctKey = base64_decode(str_replace('base64:', '', $encodedKey));
        $this->app->singleton('encrypter', function () use ($correctKey) {
            return new Encrypter($correctKey, config('app.cipher', 'AES-256-CBC'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}