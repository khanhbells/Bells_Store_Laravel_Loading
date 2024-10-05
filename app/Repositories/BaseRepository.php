<?php

namespace App\Repositories;


use App\Models\Base;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\In;

/**
 * Class BaseService
 * @package App\Services
 */

// Xu ly chung cho viec lay toan bo du lieu tu csdl
class BaseRepository implements BaseRepositoryInterface
{
    protected $model;
    public function __construct(Model $model)
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
        return $query->keyword($condition['keyword'] ?? null) // Áp dụng tìm kiếm theo từ khóa
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

    public function all(array $relation = [])
    {
        return $this->model->with($relation)->get();
    }

    public function create(array $payload = [])
    {
        $model = $this->model->create($payload);
        return $model->fresh();
    }

    public function update($id = 0, array $payload = [])
    {
        $model = $this->findById($id);

        $model->update($payload);
    }

    public function updateByWhereIn($whereInField = '', array $whereIn = [], array $payload = [])
    {
        return $this->model->whereIn($whereInField, $whereIn)->update($payload);
    }

    public function updateByWhere($condition = [], array $payload = [])
    {
        $query = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        $query->update($payload);
    }

    public function delete(int $id = 0)
    {
        return $this->findById($id)->delete();
    }

    public function forceDeleteByCondition(array $condition = [])
    {
        $query = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        return $query->forceDelete();
    }

    public function forceDelete(int $id = 0)
    {
        return $this->findById($id)->forceDelete();
    }

    public function findById(int $modelId, array $column = ['*'], array $relation = [])
    {
        return $this->model->select($column)->with($relation)->findOrFail($modelId);
    }

    public function findByCondition($condition = [])
    {
        $query = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        return $query->first();
    }

    public function createPivot($model, array $payload = [], string $relation = '')
    {
        return $model->{$relation}()->attach($model->id, $payload);
    }
    public function createRelationPivot($model, array $payload = []) {}
}
