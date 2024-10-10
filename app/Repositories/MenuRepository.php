<?php

namespace App\Repositories;


use App\Models\Menu;
use App\Repositories\Interfaces\MenuRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */

class MenuRepository extends BaseRepository implements MenuRepositoryInterface
{
    protected $model;
    public function __construct(Menu $model)
    {
        $this->model = $model;
    }
}
