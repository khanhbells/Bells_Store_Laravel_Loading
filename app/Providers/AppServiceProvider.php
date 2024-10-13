<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        //User
        'App\Services\Interfaces\UserServiceInterface' =>
        'App\Services\UserService',
        //UserCatalogue
        'App\Services\Interfaces\UserCatalogueServiceInterface' =>
        'App\Services\UserCatalogueService',
        //Permission
        'App\Services\Interfaces\PermissionServiceInterface' =>
        'App\Services\PermissionService',
        //Language
        'App\Services\Interfaces\LanguageServiceInterface' =>
        'App\Services\LanguageService',
        //PostCatalogue
        'App\Services\Interfaces\PostCatalogueServiceInterface' =>
        'App\Services\PostCatalogueService',
        //Post
        'App\Services\Interfaces\PostServiceInterface' =>
        'App\Services\PostService',
        //Generate
        'App\Services\Interfaces\GenerateServiceInterface' =>
        'App\Services\GenerateService',
        //ProductCatalogue
        'App\Services\Interfaces\ProductCatalogueServiceInterface' =>
        'App\Services\ProductCatalogueService',
        //Product
        'App\Services\Interfaces\ProductServiceInterface' =>
        'App\Services\ProductService',
        //AttributeCatalogue
        'App\Services\Interfaces\AttributeCatalogueServiceInterface' =>
        'App\Services\AttributeCatalogueService',
        //Attribute
        'App\Services\Interfaces\AttributeServiceInterface' =>
        'App\Services\AttributeService',
        //System
        'App\Services\Interfaces\SystemServiceInterface' =>
        'App\Services\SystemService',
        //Menu
        'App\Services\Interfaces\MenuServiceInterface' =>
        'App\Services\MenuService',
        //MenuCatalogue
        'App\Services\Interfaces\MenuCatalogueServiceInterface' =>
        'App\Services\MenuCatalogueService',
        //Slide
        'App\Services\Interfaces\SlideServiceInterface' =>
        'App\Services\SlideService',
    ];
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->bindings as $key => $val) {
            $this->app->bind($key, $val);
        }
        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        //
    }
}
