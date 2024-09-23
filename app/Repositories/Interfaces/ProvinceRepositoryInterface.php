<?php

namespace App\Repositories\Interfaces;

/**
 * Interface ProvinceRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface ProvinceRepositoryInterface
{
    public function all();

    // Thêm phương thức findById vào interface
    public function findById(int $modelId, array $column = ['*'], array $relation = []);
}
