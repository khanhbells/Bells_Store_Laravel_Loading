<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Services\Interfaces\ReviewServiceInterface as ReviewService;
use App\Repositories\Interfaces\ReviewRepositoryInterface as ReviewRepository;
//Neu muon view hieu duoc controller thi phai compact
class ReviewController extends Controller
{
    protected $reviewService;
    protected $reviewRepository;
    public function __construct(
        ReviewService $reviewService,
        ReviewRepository $reviewRepository,
    ) {
        $this->reviewService = $reviewService;
        $this->reviewRepository = $reviewRepository;
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'review.index');
            // dd($request);
            $reviews = $this->reviewService->paginate($request);
            // dd($reviews); //hien thi thanh vien
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Review'
            ];
            $config['seo'] = __('message.review');
            // dd($config['seo']);
            $template = 'backend.review.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'reviews'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'review.destroy');
            $config['seo'] = __('message.review');
            $review = $this->reviewRepository->findById($id);
            $template = 'backend.review.delete';
            return view('backend.dashboard.layout', compact('template', 'review', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->reviewService->destroy($id)) {
            return redirect()->route('review.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('review.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'css' => ['backend/plugin/select2-4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'backend/plugin/select2-4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/review.js',
                'backend/plugin/ckeditor/ckeditor.js',
            ],

        ];
    }
}
