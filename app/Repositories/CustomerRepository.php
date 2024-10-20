<?php

namespace App\Repositories;


use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class CustomerService
 * @package App\Services
 */

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    protected $model;
    public function __construct(Customer $model)
    {
        $this->model = $model;
    }
    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perPage = 1,
        array $extend = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = []
    ) {
        $query = $this->model->select($column)->where(function ($query) use ($condition) {
            if (isset($condition['keyword']) && !empty($condition['keyword'])) {
                $query->where('name', 'LIKE', '%' . $condition['keyword'] . '%')
                    ->orWhere('email', 'LIKE', '%' . $condition['keyword'] . '%')
                    ->orWhere('address', 'LIKE', '%' . $condition['keyword'] . '%')
                    ->orWhere('phone', 'LIKE', '%' . $condition['keyword'] . '%');
            }
            // Chỉ lọc nếu publish khác -1
            if (isset($condition['publish']) && $condition['publish'] != -1) {
                $query->where('publish', '=', $condition['publish']);
            }
            if (isset($condition['where']) && !empty($condition['where'])) {
                foreach ($condition['where'] as $key => $val) {
                    $query->where($val[0], $val[1], $val[2]);
                }
            }
            // return $query;
        })->with(['customer_catalogues', 'sources']);
        if (!empty($join)) {
            $query->join(...$join);
        }
        return $query->paginate($perPage)->withQueryString()->withPath(env('APP_URL') . $extend['path']);
    }
}
