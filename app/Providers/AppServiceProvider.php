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
        'App\Repositories\Interfaces\UserRepositoryInterface' =>
        'App\Repositories\UserRepository',
        //UserCatalogue
        'App\Services\Interfaces\UserCatalogueServiceInterface' =>
        'App\Services\UserCatalogueService',
        'App\Repositories\Interfaces\UserCatalogueRepositoryInterface' =>
        'App\Repositories\UserCatalogueRepository',
        //Permission
        'App\Services\Interfaces\PermissionServiceInterface' =>
        'App\Services\PermissionService',
        'App\Repositories\Interfaces\PermissionRepositoryInterface' =>
        'App\Repositories\PermissionRepository',
        //Language
        'App\Services\Interfaces\LanguageServiceInterface' =>
        'App\Services\LanguageService',
        'App\Repositories\Interfaces\LanguageRepositoryInterface' =>
        'App\Repositories\LanguageRepository',
        //PostCatalogue
        'App\Services\Interfaces\PostCatalogueServiceInterface' =>
        'App\Services\PostCatalogueService',
        'App\Repositories\Interfaces\PostCatalogueRepositoryInterface' =>
        'App\Repositories\PostCatalogueRepository',
        //Post
        'App\Services\Interfaces\PostServiceInterface' =>
        'App\Services\PostService',
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
        'App\Services\Interfaces\GenerateServiceInterface' =>
        'App\Services\GenerateService',
        'App\Repositories\Interfaces\GenerateRepositoryInterface' =>
        'App\Repositories\GenerateRepository',
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
