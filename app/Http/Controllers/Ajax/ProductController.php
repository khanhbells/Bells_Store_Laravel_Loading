<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Http\Requests\StoreMenuCatalogueRequest;
use App\Repositories\Interfaces\ProductRepositoryInterface  as ProductRepository;



class ProductController extends Controller
{
    protected $language;
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
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
        $objects = $this->productRepository->findProductForPromotion($condition);
        foreach ($objects as $object) {
            if (isset($object->image) && is_string($object->image)) {
                // Nếu image là chuỗi, tách thành mảng theo dấu phẩy
                $object->image = explode(',', $object->image);
            }
        }
        // dd($objects->toArray());
        return response()->json([
            'model' => ($get['model']) ?? 'Product',
            'objects' => $objects
        ]);
    }
}
