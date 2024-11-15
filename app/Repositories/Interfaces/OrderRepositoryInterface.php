<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function getOrderById($id);
    public function getOrderByTime($month, $year);
}
