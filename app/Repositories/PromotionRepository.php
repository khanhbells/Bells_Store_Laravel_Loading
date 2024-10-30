<?php

namespace App\Repositories;


use App\Models\Promotion;
use App\Repositories\Interfaces\PromotionRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class UserService
 * @package App\Services
 */

class PromotionRepository extends BaseRepository implements PromotionRepositoryInterface
{
    protected $model;
    public function __construct(Promotion $model)
    {
        $this->model = $model;
    }

    public function findByProduct(array $productId = [])
    {
        $discountSubquery = $this->model
            ->select('products.id as product_id')
            ->selectRaw("
            MAX(
                IF(promotions.maxDiscountValue != 0,
                    LEAST(
                        CASE 
                            WHEN discountType = 'cash' THEN discountValue 
                            WHEN discountType = 'percent' THEN products.price * discountValue / 100 
                            ELSE 0 
                        END, 
                        promotions.maxDiscountValue
                    ),
                    CASE 
                        WHEN discountType = 'cash' THEN discountValue 
                        WHEN discountType = 'percent' THEN products.price * discountValue / 100 
                        ELSE 0 
                    END
                )
            ) as max_discount
        ")
            ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
            ->join('products', 'products.id', '=', 'ppv.product_id')
            ->where('promotions.publish', 2)
            ->where('products.publish', 2)
            ->whereIn('products.id', $productId)
            ->where(function ($query) {
                $query->whereDate('promotions.endDate', '>=', now())
                    ->orWhere('promotions.neverEndDate', 'accept');
            })
            ->groupBy('products.id');

        return $this->model->select(
            'promotions.id as promotion_id',
            'promotions.discountValue',
            'promotions.discountType',
            'promotions.maxDiscountValue',
            'products.id as product_id',
            'products.price as product_price',
            'discounts.max_discount as discount'
        )
            ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
            ->join('products', 'products.id', '=', 'ppv.product_id')
            ->where('promotions.publish', 2)
            ->where('products.publish', 2)
            ->whereIn('products.id', $productId)
            ->where(function ($query) {
                $query->whereDate('promotions.endDate', '>=', now())
                    ->orWhere('promotions.neverEndDate', 'accept');
            })
            ->joinSub($discountSubquery, 'discounts', function ($join) {
                $join->on('products.id', '=', 'discounts.product_id');
            })
            ->whereRaw("
            IF(promotions.maxDiscountValue != 0,
                LEAST(
                    CASE 
                        WHEN discountType = 'cash' THEN discountValue 
                        WHEN discountType = 'percent' THEN products.price * discountValue / 100 
                        ELSE 0 
                    END, 
                    promotions.maxDiscountValue
                ),
                CASE 
                    WHEN discountType = 'cash' THEN discountValue 
                    WHEN discountType = 'percent' THEN products.price * discountValue / 100 
                    ELSE 0 
                END
            ) = discounts.max_discount
        ")
            ->get();
    }

    public function findPromotionByVariantUuid($uuid)
    {
        return Promotion::select(
            'promotions.id as promotion_id',
            'promotions.discountValue',
            'promotions.discountType',
            'promotions.maxDiscountValue',
        )
            ->selectRaw("
        IF(promotions.maxDiscountValue != 0,
            LEAST(
                CASE 
                    WHEN discountType = 'cash' THEN discountValue 
                    WHEN discountType = 'percent' THEN pv.price * discountValue / 100 
                    ELSE 0 
                END, 
                promotions.maxDiscountValue
            ),
            CASE 
                WHEN discountType = 'cash' THEN discountValue 
                WHEN discountType = 'percent' THEN pv.price * discountValue / 100 
                ELSE 0 
            END
        ) as discount
    ")
            ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
            ->join('product_variants as pv', 'pv.uuid', '=', 'ppv.variant_uuid')
            ->where('promotions.publish', 2)
            ->where('ppv.variant_uuid', $uuid)
            ->whereDate('promotions.startDate', '<=', now())
            ->where(function ($query) {
                $query->whereDate('promotions.endDate', '>=', now())
                    ->orWhere('promotions.neverEndDate', 'accept');
            })
            ->orderBy('discount', 'desc') // Sắp xếp theo discount giảm dần
            ->first(); // Lấy bản ghi có discount cao nhất
    }
    public function getPromotionByCartTotal()
    {
        return $this->model
            ->where('promotions.publish', 2)
            ->where('promotions.method', 'order_amount_range')
            ->whereDate('promotions.startDate', '<=', now())
            ->where(function ($query) {
                $query->whereDate('promotions.endDate', '>=', now())
                    ->orWhere('promotions.neverEndDate', 'accept');
            })
            ->get();
    }
}
