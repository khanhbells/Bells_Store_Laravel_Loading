<?php

namespace App\Repositories;


use App\Models\Base;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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

    public function all(array $relation = [], string $selectRaw = '')
    {
        $query = $this->model->newQuery();
        $query->select('*');
        if (!empty($selectRaw)) {
            $query->selectRaw($selectRaw);
        }
        $query->with($relation);
        return $query->get();
    }

    public function create(array $payload = [])
    {
        $model = $this->model->create($payload);
        return $model->fresh();
    }

    public function update($id = 0, array $payload = [])
    {
        $model = $this->findById($id);
        $model->fill($payload);
        $model->save();
        return $model;
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

    public function findByCondition(
        $condition = [],
        $flag = false,
        $relation = [],
        array $orderBy = ['id', 'desc'],
        array $param = [],
        array $withCount = []
    ) {
        $query = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        if (isset($param['whereIn'])) {
            $query->whereIn($param['whereInField'], $param['whereIn']);
        }
        $query->with($relation);
        $query->withCount($withCount);
        $query->orderBy($orderBy[0], $orderBy[1]);
        return ($flag == false) ? $query->first() : $query->get();
    }

    public function createPivot($model, array $payload = [], string $relation = '')
    {
        return $model->{$relation}()->attach($model->id, $payload);
    }
    public function createRelationPivot($model, array $payload = []) {}

    public function createBatch(array $payload = [])
    {
        return $this->model->insert($payload);
    }
    public function updateOrInsert(array $payload = [], array $condition = [])
    {
        return $this->model->updateOrInsert($condition, $payload);
    }
    public function findByWhereHas(array $condition = [], string $relation = '', string $alias = '')
    {
        return $this->model->with('languages')->whereHas($relation, function ($query) use ($condition, $alias, $relation) {
            foreach ($condition as $key => $val) {
                $query->where($alias . '.' . $key, $val);
            }
        })->first();
    }

    public function findWidgetItem(array $condition = [], int $language_id = 1, string $alias = '')
    {
        return $this->model->with([
            'languages' => function ($query) use ($language_id) {
                $query->where('language_id', $language_id);
            }
        ])->whereHas('languages', function ($query) use ($condition, $alias) {
            foreach ($condition as $key => $val) {
                $query->where($alias . '.' . $val[0], $val[1], $val[2]);
            }
        })->get();
    }

    public function recursiveCategory(string $parameter = '', $table = '')
    {
        $table = $table . '_catalogues';
        $query = "
            WITH RECURSIVE category_tree AS (
                SELECT id, parent_id, deleted_at
                FROM $table
                WHERE id IN (?)
                UNION ALL
                SELECT c.id, c.parent_id, c.deleted_at
                FROM $table as c
                JOIN category_tree as ct ON ct.id = c.parent_id
            )
            SELECT id FROM category_tree WHERE deleted_at IS NULL
        ";
        // Use parameter binding to prevent SQL injection
        $results = DB::select($query, [$parameter]);
        return $results;
    }


    public function findObjectByCategoryIds($catIds = [], $model, $language)
    {
        $query = $this->model->newQuery();
        $query->select(
            $model . 's.*'
        )
            ->where(
                [config('app.general.defaultPublish')],
            )
            ->with('languages', function ($query) use ($language) {
                $query->where('language_id', $language);
            })
            ->with($model . '_catalogues', function ($query) use ($language) {
                $query->with('languages', function ($query) use ($language) {
                    $query->where('language_id', $language);
                });
            });

        if ($model == 'product') {
            $query->with('product_variants');
        }

        $query->join($model . '_catalogue_' . $model . ' as tb2', 'tb2.' . $model . '_id', '=', $model . 's.id')
            ->whereIn('tb2.' . $model . '_catalogue_id', $catIds)
            ->orderBy('order', 'desc')
            ->limit(8)
            ->get();
        return $query->get();
    }

    public function breadcrumb($model, $language)
    {
        return $this->findByCondition([
            ['lft', '<=', $model->lft],
            ['rgt', '>=', $model->rgt],
            config('app.general.defaultPublish')
        ], true, [
            'languages' => function ($query) use ($language) {
                $query->where('language_id', $language);
            }
        ], ['lft', 'asc']);
    }
}
