<?php

namespace App\Repositories;


use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class UserService
 * @package App\Services
 */

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected $model;
    public function __construct(Product $model)
    {
        $this->model = $model;
    }
    public function getProductById(int $id = 0, $language_id = 0)
    {
        return $this->model->select([
            'products.id',
            'products.product_catalogue_id',
            'products.image',
            'products.icon',
            'products.album',
            'products.publish',
            'products.follow',
            'products.price',
            'products.code',
            'products.made_in',
            'products.attributeCatalogue',
            'products.attribute',
            'products.variant',
            'tb2.name',
            'tb2.description',
            'tb2.content',
            'tb2.meta_title',
            'tb2.meta_keyword',
            'tb2.meta_description',
            'tb2.canonical',
        ])->distinct()->join('product_language as tb2', 'tb2.product_id', '=', 'products.id')
            ->with([
                'product_catalogues',
                'product_variants' => function ($query) use ($language_id) {
                    $query->with(['attributes' => function ($query) use ($language_id) {
                        $query->with(['attribute_language' => function ($query) use ($language_id) {
                            $query->where('language_id', '=', $language_id);
                        }]);
                    }]);
                },
                'reviews'
            ])
            ->where('tb2.language_id', '=', $language_id)
            ->find($id);
    }
    public function findProductForPromotion($condition = [], $relation = [])
    {
        $query = $this->model->newQuery();
        $query->select([
            'products.id',
            'tb2.name',
            'tb2.canonical',
            'tb3.uuid',
            'tb3.code',
            'tb3.id as product_variant_id',
            DB::raw('CONCAT(tb2.name,"-",COALESCE(tb4.name,"default")) as variant_name'),
            DB::raw('COALESCE(tb3.sku,products.code) as sku'),
            DB::raw('COALESCE(tb3.price,products.price) as price'),
            DB::raw('COALESCE(tb3.album,products.image) as image'),
        ])->distinct();
        $query->join('product_language as tb2', 'products.id', '=', 'tb2.product_id');
        $query->leftJoin('product_variants as tb3', 'products.id', '=', 'tb3.product_id');
        $query->leftJoin('product_variant_language as tb4', 'tb3.id', '=', 'tb4.product_variant_id');
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        if (count($relation)) {
            $query->with($relation);
        }
        $query->orderBy('id', 'desc');
        return $query->paginate(10);
    }

    public function filter($param, $perPage)
    {
        $query = $this->model->newQuery();
        // Chọn các trường cần thiết và sử dụng hàm tổng hợp MAX cho các cột cần thiết
        $query->select(
            'products.id',
            DB::raw('MAX(products.price) as price'),
            DB::raw('MAX(products.image) as image'),
        );


        // Thêm các trường khác từ $param['select']
        if (isset($param['select']) && count($param['select'])) {
            foreach ($param['select'] as $key => $val) {
                if (!is_null($val)) {
                    $query->selectRaw($val);
                }
            }
        }

        // Thực hiện join
        if (isset($param['join']) && count($param['join'])) {
            foreach ($param['join'] as $key => $val) {
                if (!is_null($val)) {
                    $query->leftJoin($val[0], $val[1], $val[2], $val[3]);
                }
            }
        }

        $query->where('products.publish', '=', 2);

        // Điều kiện where
        if (isset($param['where']) && count($param['where'])) {
            foreach ($param['where'] as $key => $val) {
                if (!is_null($val)) {
                    $query->where($val);
                }
            }
        }

        // Điều kiện whereRaw
        if (isset($param['whereRaw']) && count($param['whereRaw'])) {
            $query->whereRaw($param['whereRaw'][0][0], $param['whereRaw'][0][1]);
        }


        // Điều kiện having
        if (isset($param['having']) && count($param['having'])) {
            foreach ($param['having'] as $key => $val) {
                if (!is_null($val)) {
                    $query->having($val);
                }
            }
        }
        $query->groupBy('products.id'); // Nhóm theo products.id
        $query->with(['reviews', 'languages', 'product_catalogues']);

        // Kết quả cuối cùng
        return $query->paginate($perPage);
        // return $query->toSql(); // Debug SQL
    }
}
