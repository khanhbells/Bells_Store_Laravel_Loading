<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Models\Language;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Models\System;

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class ProductController extends FrontendController
{
    protected $system;
    protected $productCatalogueRepository;
    protected $productService;
    protected $productRepository;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductService $productService,
        ProductRepository $productRepository,
    ) {
        parent::__construct();
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productService = $productService;
        $this->productRepository = $productRepository;
    }
    public function index($id, $request)
    {
        $config = $this->config();
        $system = $this->system;
        $product = $this->productRepository->getProductById($id, $this->language);
        $product = $this->productService->combineProductAndPromotion([$id], $product, $flag = true);
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($product->product_catalogue_id, $this->language);
        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $this->language);
        $seo = seo($product);
        // -----------------------------------------------------------
        $product = $this->productService->getAttribute($product, $this->language);
        return view('frontend.product.product.index', compact(
            'config',
            'seo',
            'productCatalogue',
            'system',
            'breadcrumb',
            'product',
        ));
    }

    private function config()
    {
        return [
            'language' => $this->language
        ];
    }
}
