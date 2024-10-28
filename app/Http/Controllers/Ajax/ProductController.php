<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Http\Requests\StoreMenuCatalogueRequest;
use App\Repositories\Interfaces\ProductRepositoryInterface  as ProductRepository;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface  as ProductVariantRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface  as PromotionRepository;
use App\Repositories\Interfaces\AttributeRepositoryInterface  as AttributeRepository;



class ProductController extends Controller
{
    protected $language;
    protected $productRepository;
    protected $productVariantRepository;
    protected $promotionRepository;
    protected $attributeRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductVariantRepository $productVariantRepository,
        PromotionRepository $promotionRepository,
        AttributeRepository $attributeRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->promotionRepository = $promotionRepository;
        $this->attributeRepository = $attributeRepository;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }
    public function loadProductPromotion(Request $request)
    {
        $get = $request->input();
        $loadClass = loadClass($get['model']);
        if ($get['model'] == 'Product') {
            $condition = [
                [
                    'tb2.language_id',
                    '=',
                    $this->language
                ]
            ];
            if (isset($get['keyword']) && $get['keyword'] != '') {
                $keywordCondition = [
                    'tb2.name',
                    'LIKE',
                    '%' . $get['keyword'] . '%'
                ];
                array_push($condition, $keywordCondition);
            }
            $objects = $loadClass->findProductForPromotion($condition);
            foreach ($objects as $object) {
                if (isset($object->image) && is_string($object->image)) {
                    // Nếu image là chuỗi, tách thành mảng theo dấu phẩy
                    $object->image = explode(',', $object->image);
                }
                if (isset($object->canonical)) {
                    // Nếu image là chuỗi, tách thành mảng theo dấu phẩy
                    $object->canonical = write_url($object->canonical, true, true);
                }
                if (isset($object->code)) {
                    $object->canonical = $object->canonical . '?attribute_id=' . $object->code;
                }
            }
        } else if ($get['model']  == 'ProductCatalogue') {
            $conditionArray['keyword'] = ($get['keyword']) ?? null;
            $conditionArray['where'] = [
                ['tb2.language_id', '=', $this->language]
            ];
            $objects = $loadClass->pagination(
                [
                    'product_catalogues.id',
                    'tb2.name',
                ],
                $conditionArray,
                20,
                ['path' => 'product/catalogue/index'],
                [
                    'product_catalogues.lft',
                    'DESC'
                ],
                [
                    [
                        'product_catalogue_language as tb2',
                        'tb2.product_catalogue_id',
                        '=',
                        'product_catalogues.id'
                    ]
                ],
            );
        }
        return response()->json([
            'model' => ($get['model']) ?? 'Product',
            'objects' => $objects
        ]);
    }

    public function loadVariant(Request $request)
    {
        $get = $request->input();
        $attributeId = $get['attribute_id'];
        sort($attributeId, SORT_NUMERIC);
        $attributeId = implode(',', $attributeId);
        $variant = $this->productVariantRepository->findVariant($attributeId, $get['product_id'], $get['language_id']);
        $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
        $variantPrice = getVariantPrice($variant, $variantPromotion);
        return response()->json([
            'variant' => $variant,
            'variantPrice' => $variantPrice
        ]);
    }
}
