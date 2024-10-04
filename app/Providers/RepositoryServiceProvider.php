<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        //User
        'App\Repositories\Interfaces\UserRepositoryInterface' =>
        'App\Repositories\UserRepository',
        //UserCatalogue
        'App\Repositories\Interfaces\UserCatalogueRepositoryInterface' =>
        'App\Repositories\UserCatalogueRepository',
        //Permission
        'App\Repositories\Interfaces\PermissionRepositoryInterface' =>
        'App\Repositories\PermissionRepository',
        //Language
        'App\Repositories\Interfaces\LanguageRepositoryInterface' =>
        'App\Repositories\LanguageRepository',
        //PostCatalogue
        'App\Repositories\Interfaces\PostCatalogueRepositoryInterface' =>
        'App\Repositories\PostCatalogueRepository',
        //Post
        'App\Repositories\Interfaces\PostRepositoryInterface' =>
        'App\Repositories\PostRepository',
        //Provinces,Districts
        'App\Repositories\Interfaces\ProvinceRepositoryInterface' =>
        'App\Repositories\ProvinceRepository',
        'App\Repositories\Interfaces\DistrictRepositoryInterface' =>
        'App\Repositories\DistrictRepository',
        //Router
        'App\Repositories\Interfaces\RouterRepositoryInterface' =>
        'App\Repositories\RouterRepository',
        //Generate
        'App\Repositories\Interfaces\GenerateRepositoryInterface' =>
        'App\Repositories\GenerateRepository',
        //ProductCatalogue
        'App\Repositories\Interfaces\ProductCatalogueRepositoryInterface' =>
        'App\Repositories\ProductCatalogueRepository',
        //Product
        'App\Repositories\Interfaces\ProductRepositoryInterface' =>
        'App\Repositories\ProductRepository',
    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->bindings as $key => $val) {
            $this->app->bind($key, $val);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
