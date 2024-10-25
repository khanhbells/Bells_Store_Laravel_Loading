<?php

namespace App\Repositories\Interfaces;

/**
 * Interface WidgetServiceInterface
 * @package App\Services\Interfaces
 */
interface WidgetRepositoryInterface extends BaseRepositoryInterface
{
    public function getWidgetWhereIn(array $whereIn = [], $whereInField = 'keyword');
}
