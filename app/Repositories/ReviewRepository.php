<?php

namespace App\Repositories;


use App\Models\Review;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\ReviewRepositoryInterface;

/**
 * Class ReviewService
 * @package App\Services
 */

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    protected $model;
    public function __construct(Review $model)
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
        return $query->keyword($condition['keyword'] ?? null, ['fullname', 'email', 'phone', 'description']) // Áp dụng tìm kiếm theo từ khóa
            ->publish($condition['publish'] ?? null) // Lọc theo publish
            ->customWhere($condition['where'] ?? null) // Áp dụng các điều kiện where tùy chỉnh
            ->customWhereRaw($rawQuery['whereRaw'] ?? null) // Áp dụng truy vấn thô (raw query)
            ->relationCount($relations ?? null) // Lấy kèm số lượng của các quan hệ (relations)
            ->customJoin($join ?? null) // Join các bảng khác
            ->customOrderBy($orderBy ?? null) // Sắp xếp kết quả theo điều kiện orderBy
            ->paginate($perPage) // Thực hiện phân trang
            ->withQueryString() // Giữ lại các query string của URL
            ->withPath(env('APP_URL') . $extend['path']); // Thêm đường dẫn cho phân trang
    }
}
