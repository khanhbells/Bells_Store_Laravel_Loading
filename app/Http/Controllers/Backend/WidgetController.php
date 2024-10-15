<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWidgetRequest;
use App\Http\Requests\UpdateWidgetRequest;
use Illuminate\Http\Request;
use App\Models\WidgetCatalogue;
// use App\Models\Widget;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
use App\Repositories\Interfaces\WidgetRepositoryInterface as WidgetRepository;

//Neu muon view hieu duoc controller thi phai compact
class WidgetController extends Controller
{
    protected $widgetService;
    protected $widgetRepository;
    public function __construct(
        WidgetService $widgetService,
        WidgetRepository $widgetRepository
    ) {
        $this->widgetService = $widgetService;
        $this->widgetRepository = $widgetRepository;
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'widget.index');
            // dd($request);
            $widgets = $this->widgetService->paginate($request);
            // dd($widgets); //hien thi thanh vien
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Widget'
            ];
            $config['seo'] = __('message.widget');
            // dd($config['seo']);
            $template = 'backend.widget.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'widgets'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'widget.create');
            $config = $this->configData();
            $config['seo'] = __('message.widget');
            $config['method'] = 'create';
            $template = 'backend.widget.store';

            // Truyền widgetCatalogues vào view
            return view('backend.dashboard.layout', compact('template', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreWidgetRequest $request)
    {
        if ($this->widgetService->create($request)) {
            return redirect()->route('widget.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'widget.update');
            $widget = $this->widgetRepository->findById($id);
            $config = $this->configData();
            $template = 'backend.widget.store';

            $config['seo'] = __('message.widget');
            $config['method'] = 'edit';
            return view('backend.dashboard.layout', compact('template', 'config', 'widget', 'widgetCatalogues'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function update($id, UpdateWidgetRequest $request)
    {
        if ($this->widgetService->update($id, $request)) {
            return redirect()->route('widget.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'widget.destroy');
            $config['seo'] = __('message.widget');
            $widget = $this->widgetRepository->findById($id);
            $template = 'backend.widget.delete';
            return view('backend.dashboard.layout', compact('template', 'widget', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->widgetService->destroy($id)) {
            return redirect()->route('widget.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'css' => ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/widget.js',
                'backend/plugin/ckeditor/ckeditor.js',
            ],

        ];
    }
}
