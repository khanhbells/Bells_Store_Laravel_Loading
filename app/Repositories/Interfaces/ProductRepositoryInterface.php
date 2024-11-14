<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getProductById(int $id = 0, $language_id = 0);
    public function findProductForPromotion($condition = []);
    public function filter($param, $perPage);
    // public function getAllPaginate();
    // public function create(array $payload = []);
    // public function findById(int $modelId, array $column = ['*'], array $relation = []);
    // public function update(int $id = 0, array $payload = []);
}
