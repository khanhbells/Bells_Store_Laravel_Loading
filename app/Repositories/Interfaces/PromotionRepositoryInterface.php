<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface PromotionRepositoryInterface extends BaseRepositoryInterface
{
    public function findByProduct(array $productId = []);
    public function findPromotionByVariantUuid($uuid);
}
