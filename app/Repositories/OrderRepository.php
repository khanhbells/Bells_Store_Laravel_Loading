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
        $query = $this->model->select($column)->distinct(); // Bắt đầu truy vấn, chọn các cột và loại bỏ các dòng trùng lặp
        return $query->keyword($condition['keyword'] ?? null, ['fullname', 'phone', 'email', 'address', 'code'], ['field' => 'name', 'relation' => 'products']) // Áp dụng tìm kiếm theo từ khóa
            ->publish($condition['publish'] ?? null) // Lọc theo publish
            ->customDropdownFilter($condition['dropdown'] ?? null)
            ->customWhere($condition['where'] ?? null) // Áp dụng các điều kiện where tùy chỉnh
            ->customWhereRaw($rawQuery['whereRaw'] ?? null) // Áp dụng truy vấn thô (raw query)
            ->relationCount($relations ?? null) // Lấy kèm số lượng của các quan hệ (relations)
            ->customJoin($join ?? null) // Join các bảng khác
            ->customOrderBy($orderBy ?? null) // Sắp xếp kết quả theo điều kiện orderBy
            ->customerCreatedAt($condition['created_at'] ?? null)
            ->paginate($perPage) // Thực hiện phân trang
            ->withQueryString() // Giữ lại các query string của URL
            ->withPath(env('APP_URL') . $extend['path']); // Thêm đường dẫn cho phân trang
    }
    public function getOrderById($id)
    {
        return $this->model->select([
            'orders.*',
            'provinces.name as province_name',
            'districts.name as district_name',
            'wards.name as ward_name',
        ])->distinct()
            ->join('provinces', 'orders.province_id', '=', 'provinces.code')
            ->join('districts', 'orders.district_id', '=', 'districts.code')
            ->join('wards', 'orders.ward_id', '=', 'wards.code')
            ->with('products')
            ->find($id);
    }
}
