<?php

namespace App\Providers;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        //User
        'App\Services\Interfaces\UserServiceInterface' =>
        'App\Services\UserService',
        //UserCatalogue
        'App\Services\Interfaces\UserCatalogueServiceInterface' =>
        'App\Services\UserCatalogueService',
        //Customer
        'App\Services\Interfaces\CustomerServiceInterface' =>
        'App\Services\CustomerService',
        //CustomerCatalogue
        'App\Services\Interfaces\CustomerCatalogueServiceInterface' =>
        'App\Services\CustomerCatalogueService',
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
        //Widget
        'App\Services\Interfaces\WidgetServiceInterface' =>
        'App\Services\WidgetService',
        //Promotion
        'App\Services\Interfaces\PromotionServiceInterface' =>
        'App\Services\PromotionService',
        //Source
        'App\Services\Interfaces\SourceServiceInterface' =>
        'App\Services\SourceService',
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
        Validator::extend('custom_date_format', function ($attribute, $value, $parameters, $validator) {
            return DateTime::createFromFormat('d/m/Y H:i', $value) !== false;
        });
        Validator::extend('custom_after', function ($attribute, $value, $parameters, $validator) {
            if (isset($validator->getData()[$parameters[0]])) {
                $startDate = Carbon::createFromFormat('d/m/Y H:i', $validator->getData()[$parameters[0]]);
            } else {
                return false;
            }
            $endDate = Carbon::createFromFormat('d/m/Y H:i', $value);
            return $endDate->greaterThan($startDate) !== false;
        });
        Schema::defaultStringLength(191);
        //
    }
}
