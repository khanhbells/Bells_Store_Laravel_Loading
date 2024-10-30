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
        //Customer
        'App\Repositories\Interfaces\CustomerRepositoryInterface' =>
        'App\Repositories\CustomerRepository',
        //CustomerCatalogue
        'App\Repositories\Interfaces\CustomerCatalogueRepositoryInterface' =>
        'App\Repositories\CustomerCatalogueRepository',
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
        //AttributeCatalogue
        'App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface' =>
        'App\Repositories\AttributeCatalogueRepository',
        //Attribute
        'App\Repositories\Interfaces\AttributeRepositoryInterface' =>
        'App\Repositories\AttributeRepository',
        //ProductVariantLanguage
        'App\Repositories\Interfaces\ProductVariantLanguageRepositoryInterface' =>
        'App\Repositories\ProductVariantLanguageRepository',
        //ProductVariantAttribute
        'App\Repositories\Interfaces\ProductVariantAttributeRepositoryInterface' =>
        'App\Repositories\ProductVariantAttributeRepository',
        //ProductVariant
        'App\Repositories\Interfaces\ProductVariantRepositoryInterface' =>
        'App\Repositories\ProductVariantRepository',
        //System
        'App\Repositories\Interfaces\SystemRepositoryInterface' =>
        'App\Repositories\SystemRepository',
        //Menu
        'App\Repositories\Interfaces\MenuRepositoryInterface' =>
        'App\Repositories\MenuRepository',
        //MenuCatalogue
        'App\Repositories\Interfaces\MenuCatalogueRepositoryInterface' =>
        'App\Repositories\MenuCatalogueRepository',
        //Slide
        'App\Repositories\Interfaces\SlideRepositoryInterface' =>
        'App\Repositories\SlideRepository',
        //Widget
        'App\Repositories\Interfaces\WidgetRepositoryInterface' =>
        'App\Repositories\WidgetRepository',
        //Promotion
        'App\Repositories\Interfaces\PromotionRepositoryInterface' =>
        'App\Repositories\PromotionRepository',
        //Source
        'App\Repositories\Interfaces\SourceRepositoryInterface' =>
        'App\Repositories\SourceRepository',
        //Order
        'App\Repositories\Interfaces\OrderRepositoryInterface' =>
        'App\Repositories\OrderRepository',
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
