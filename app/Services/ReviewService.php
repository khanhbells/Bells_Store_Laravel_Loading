<?php

namespace App\Services;

use App\Services\Interfaces\ReviewServiceInterface;
use App\Repositories\Interfaces\ReviewRepositoryInterface as ReviewRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Classes\ReviewNested;

use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * Class ReviewService
 * @package App\Services
 */
class ReviewService extends BaseService implements ReviewServiceInterface
{
    protected $reviewRepository;
    protected $reviewNestedset;

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $perPage = $request->integer('perpage');
        // dd($condition);
        $reviews = $this->reviewRepository->pagination(
            $this->paginateselect(),
            $condition,
            $perPage,
            ['path' => 'review/index'],
        );
        // dd($reviews);
        return $reviews;
    }

    public function create(Request $request)
    {
        DB::beginTransaction();

        try {
            $payload = $request->except('_token');
            $review = $this->reviewRepository->create($payload);
            $this->reviewNestedset = new ReviewNested([
                'table' => 'reviews',
                'reviewable_type' => $payload['reviewable_type']
            ]);
            $this->reviewNestedset->Get('level ASC, order ASC');
            $this->reviewNestedset->Recursive(0, $this->reviewNestedset->Set());
            $this->reviewNestedset->Action();
            DB::commit();
            return [
                'code' => 10,
                'message' => 'Đánh giá sản phẩm thành công'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return [
                'code' => 11,
                'message' => 'Có vấn đề xảy ra hãy thử lại'
            ];
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $widget = $this->reviewRepository->forceDelete($id);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    private function paginateselect()
    {
        return ['*'];
    }
}
