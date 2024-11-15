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
    public function getTotalOrders();
    public function getCancleOrders();
    public function revenueOrders();
    public function revenueByYear($year);
    public function revenue7Day();
    public function revenueCurrentMonth($currentMonth, $currentYear);
}
