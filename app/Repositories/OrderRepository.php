<?php

namespace App\Repositories;


use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    protected $model;
    public function __construct(Order $model)
    {
        $this->model = $model;
    }
}
