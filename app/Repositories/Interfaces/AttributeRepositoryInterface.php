<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface AttributeRepositoryInterface extends BaseRepositoryInterface
{
    public function getAttributeById(int $id = 0, $language_id = 0);
    public function searchAttributes(string $keyword = '', array $option = [], int $languageId);
    public function findAttributeByIdArray(array $attributeArray = [], int $languageId = 0);
    // public function getAllPaginate();
    // public function create(array $payload = []);
    // public function findById(int $modelId, array $column = ['*'], array $relation = []);
    // public function update(int $id = 0, array $payload = []);
}
