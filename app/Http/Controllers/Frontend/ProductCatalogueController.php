<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Models\Language;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use App\Models\System;

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class ProductCatalogueController extends FrontendController
{
    protected $system;
    protected $productCatalogueRepository;
    protected $productService;
    protected $productCatalogueService;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductService $productService,
        ProductCatalogueService $productCatalogueService,
    ) {
        parent::__construct();
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productService = $productService;
        $this->productCatalogueService = $productCatalogueService;
    }
    public function index($id, $request, $page)
    {
        $config = $this->config();
        $system = $this->system;
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
        $filters = $this->filter($productCatalogue);
        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $this->language);
        $products = $this->productService->paginate(
            $request,
            $this->language,
            $productCatalogue,
            $page,
            ['path' => $productCatalogue->canonical],
        );
        $productId = $products->pluck('id')->toArray();
        if (count($productId) && !is_null($productId)) {
            $products = $this->productService->combineProductAndPromotion($productId, $products);
        }
        $seo = seo($productCatalogue, $page);
        return view('frontend.product.catalogue.index', compact(
            'config',
            'seo',
            'productCatalogue',
            'system',
            'breadcrumb',
            'products',
            'filters'
        ));
    }

    private function filter($productCatalogue)
    {
        $filters = null;
        if (isset($productCatalogue->attribute) && !is_null($productCatalogue->attribute) && count($productCatalogue->attribute)) {
            $filters = $this->productCatalogueService->getFilterList($productCatalogue->attribute, $this->language);
        }
        return $filters;
    }

    private function config()
    {
        return [
            'language' => $this->language,
            'js' => [
                'frontend/core/library/filter.js',
            ]
        ];
    }
}
