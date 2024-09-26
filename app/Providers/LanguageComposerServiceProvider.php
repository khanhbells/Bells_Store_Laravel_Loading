<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;

class LanguageComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Repositories\Interfaces\LanguageRepositoryInterface',
            'App\Repositories\LanguageRepository'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('backend.dashboard.component.nav', function ($view) {
            $languageRepository = $this->app->make(LanguageRepository::class);
            $language = $languageRepository->all();
            $view->with('language', $language);
        });
    }
}
