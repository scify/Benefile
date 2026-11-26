<?php

namespace App\Providers;

use App\Support\Form;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // The views call Form::text() and others, as with laravelcollective/html.
        AliasLoader::getInstance()->alias('Form', Form::class);
    }

    public function boot(): void
    {
        //
    }
}
