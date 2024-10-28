<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface ProductVariantRepositoryInterface extends BaseRepositoryInterface
{
    public function findVariant($code, $productId, $languageId);
}
