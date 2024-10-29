<?php

namespace App\Services;

use App\Services\Interfaces\CartServiceInterface;
use App\Services\Interfaces\ProductServiceInterface;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface  as ProductVariantRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Gloudemans\Shoppingcart\Facades\Cart;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class CartService extends BaseService implements CartServiceInterface
{
    protected $productReopsitory;
    protected $productService;
    protected $productVariantRepository;
    protected $promotionRepository;
    public function __construct(
        ProductRepository $productReopsitory,
        ProductVariantRepository $productVariantRepository,
        PromotionRepository $promotionRepository,
        ProductServiceInterface $productService,
    ) {
        $this->productReopsitory = $productReopsitory;
        $this->productVariantRepository = $productVariantRepository;
        $this->promotionRepository = $promotionRepository;
        $this->productService = $productService;
    }
    public function create(Request $request, $language = 1)
    {
        try {
            $payload = $request->input();
            $product = $this->productReopsitory->findById(
                $payload['id'],
                ['*'],
                ['languages' => function ($query) use ($language) {
                    $query->where('language_id', $language);
                }]
            );

            $data = [
                'id' => $product->id,
                'name' => $product->languages->first()->pivot->name,
                'qty' => $payload['quantity'],
                // 'price' =
            ];
            if (!empty($payload['attribute_id']) && count($payload['attribute_id'])) {
                $attributeId = sortAttributeId($payload['attribute_id']);
                $variant = $this->productVariantRepository->findVariant($attributeId, $product->id, $language);
                $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
                $variantPrice = getVariantPrice($variant, $variantPromotion);
                $data['id'] = $product->id . '_' . $variant->uuid;
                $data['name'] = $product->languages->first()->pivot->name . ' ' . $variant->languages->first()->pivot->name;
                $data['price'] = ($variantPrice['priceSale'] > 0) ? $variantPrice['priceSale'] : $variantPrice['price'];
                $data['options'] = [
                    'attribute' => $payload['attribute_id'],
                ];
            } else {
                $product = $this->productService->combineProductAndPromotion([$product->id], $product, $flag = true);
                $price = getPrice($product);
                $data['price'] = ($price['priceSale'] > 0) ? $price['priceSale'] : $price['price'];
            }
            Cart::instance('shopping')->add($data);
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function remakeCart($carts)
    {
        $cartId = $carts->pluck('id')->all();
        $temp = [];
        $objects = [];
        if (count($cartId)) {
            foreach ($cartId as $key => $val) {
                $extract = explode('_', $val);
                if (count($extract) > 1) {
                    $temp['variant'][] = $extract[1];
                } else {
                    $temp['product'][] = $extract[0];
                }
            }
            $objects['variants'] = $this->productVariantRepository->findByCondition(
                [],
                true,
                [],
                ['id', 'desc'],
                [
                    'whereIn' => $temp['variant'],
                    'whereInField' => 'uuid'
                ]
            )->keyBy('uuid');
            $objects['products'] = $this->productReopsitory->findByCondition(
                [
                    config('app.general.defaultPublish')
                ],
                true,
                [],
                ['id', 'desc'],
                [
                    'whereIn' => $temp['product'],
                    'whereInField' => 'id'
                ]
            )->keyBy('id');

            foreach ($carts as $keyCart => $cart) {
                $explode = explode('_', $cart->id);
                $objectId = $explode[1] ?? $explode[0];
                if (isset($objects['variants'][$objectId])) {
                    $variantItem = $objects['variants'][$objectId];
                    $variantImage = explode(',', $variantItem->album)[0] ?? null;
                    $cart->image = $variantImage;
                    $cart->priceOriginal = $variantItem->price;
                } elseif (isset($objects['products'][$objectId])) {
                    $productItem = $objects['products'][$objectId];
                    $cart->image = $productItem->image;
                    $cart->priceOriginal = $productItem->price;
                }
            }
        }

        return $carts;
    }
}
