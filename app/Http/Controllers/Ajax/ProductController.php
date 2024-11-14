<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Http\Requests\StoreMenuCatalogueRequest;
use App\Repositories\Interfaces\ProductRepositoryInterface  as ProductRepository;
use App\Services\Interfaces\ProductServiceInterface  as ProductService;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface  as ProductVariantRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface  as PromotionRepository;
use App\Repositories\Interfaces\AttributeRepositoryInterface  as AttributeRepository;



class ProductController extends Controller
{
    protected $language;
    protected $productRepository;
    protected $productService;
    protected $productVariantRepository;
    protected $promotionRepository;
    protected $attributeRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductVariantRepository $productVariantRepository,
        PromotionRepository $promotionRepository,
        AttributeRepository $attributeRepository,
        ProductService $productService,
    ) {
        $this->productRepository = $productRepository;
        $this->productService = $productService;
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
        $attributeId = sortAttributeId($attributeId);
        $variant = $this->productVariantRepository->findVariant($attributeId, $get['product_id'], $get['language_id']);
        $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
        $variantPrice = getVariantPrice($variant, $variantPromotion);
        return response()->json([
            'variant' => $variant,
            'variantPrice' => $variantPrice
        ]);
    }

    public function filter(Request $request)
    {
        $products = $this->productService->filter($request);
        $html = $this->renderFilterProduct($products);
        return response()->json([
            'data' => $html,
        ]);
    }

    public function renderFilterProduct($products)
    {

        $html = '';
        if (!is_null($products) && count($products)) {
            $html .= '<div class="uk-grid uk-grid-medium">';
            foreach ($products as $product) {
                $name = $product->languages->first()->pivot->name;
                $canonical = write_url($product->languages->first()->pivot->canonical, true, true);
                $image = asset($product->image);
                $price = getPrice($product);
                $catNames = $product->product_catalogues->first()->languages->first()->pivot->name;
                $review = getReview($product);
                $html .= '<div class="uk-width-large-1-5 mb20">';
                $html .= '<div class="product-item product">';
                if ($price['percent'] > 0) {
                    $html .= "<div class='badge badge-bg1'>-{$price['percent']}%</div>";
                }
                $html .= "<a href='$canonical' class='image img-cover'><img src='$image' alt='$name'></a>";
                $html .= '<div class="info">';
                $html .= "<div class='category-title'><a href='$canonical' title='$name'>$catNames</a></div>";
                $html .= "<h3 class='title'><a href='$canonical' title='$name'>$name</a></h3>";
                $html .= '<div class="rating">';
                $html .= '<div class="uk-flex uk-flex-middle">';
                $html .= '<div class="star">';
                for ($i = 0; $i <= $review['star']; $i++) {
                    $html .= '<i class="fa fa-star"></i>';
                }
                $html .= '</div>';
                $html .= "<span class='rate-number'>({$review['count']})</span>";
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="product-group">';
                $html .= '<div class="uk-flex uk-flex-middle uk-flex-space-between">';
                $html .= $price['html'];
                $html .= '<div class="addcart">';
                $html .= renderQuickBuy($product, $name, $canonical);
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
            $html .= $products->links('pagination::bootstrap-4');
        } else {
            $html = '<div class"no-result">Không có sản phẩm nào phù hợp </div>';
        }

        return $html;
    }
}
