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
        // Subquery to calculate the max discount for each product
        $subquery = DB::table('promotions')
            ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
            ->join('products', 'products.id', '=', 'ppv.product_id')
            ->where('products.publish', 2)
            ->where('promotions.publish', 2)
            ->whereIn('products.id', $productId)
            ->whereDate('promotions.endDate', '>', now())
            ->select(
                'products.id as product_id',
                DB::raw(
                    "MAX(
                    IF(
                        promotions.maxDiscountValue != 0,
                        LEAST(
                            CASE
                                WHEN promotions.discountType = 'cash' THEN promotions.discountValue
                                WHEN promotions.discountType = 'percent' THEN products.price * promotions.discountValue / 100
                                ELSE 0
                            END,
                            promotions.maxDiscountValue
                        ),
                        CASE
                            WHEN promotions.discountType = 'cash' THEN promotions.discountValue
                            WHEN promotions.discountType = 'percent' THEN products.price * promotions.discountValue / 100
                            ELSE 0
                        END
                    )
                ) as max_discount"
                )
            )
            ->groupBy('products.id');

        // Main query to select promotion details based on max discount
        return $this->model->select(
            'promotions.id as promotion_id',
            'promotions.discountValue',
            'promotions.discountType',
            'promotions.maxDiscountValue',
            'products.id as product_id',
            'products.price as product_price',
            DB::raw(
                "IF(
                    promotions.maxDiscountValue != 0,
                    LEAST(
                        CASE
                            WHEN promotions.discountType = 'cash' THEN promotions.discountValue
                            WHEN promotions.discountType = 'percent' THEN products.price * promotions.discountValue / 100
                            ELSE 0
                        END,
                        promotions.maxDiscountValue
                    ),
                    CASE
                        WHEN promotions.discountType = 'cash' THEN promotions.discountValue
                        WHEN promotions.discountType = 'percent' THEN products.price * promotions.discountValue / 100
                        ELSE 0
                    END
                ) as discount"
            )
        )
            ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
            ->join('products', 'products.id', '=', 'ppv.product_id')
            ->joinSub($subquery, 'max_discount_sub', function ($join) {
                $join->on('products.id', '=', 'max_discount_sub.product_id')
                    ->on(DB::raw('IF(promotions.maxDiscountValue != 0, LEAST(
                    CASE
                        WHEN promotions.discountType = \'cash\' THEN promotions.discountValue
                        WHEN promotions.discountType = \'percent\' THEN products.price * promotions.discountValue / 100
                        ELSE 0
                    END, promotions.maxDiscountValue), CASE
                        WHEN promotions.discountType = \'cash\' THEN promotions.discountValue
                        WHEN promotions.discountType = \'percent\' THEN products.price * promotions.discountValue / 100
                        ELSE 0
                    END)'), '=', 'max_discount_sub.max_discount');
            })
            ->whereIn('products.id', $productId)
            ->orderBy('discount', 'DESC')
            ->get();
    }
}
