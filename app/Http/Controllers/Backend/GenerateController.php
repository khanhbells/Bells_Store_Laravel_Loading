<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenerateRequest;
use App\Http\Requests\TranslateRequest;
use App\Http\Requests\UpdateGenerateRequest;
use Illuminate\Http\Request;
// use Generate;
use App\Services\Interfaces\GenerateServiceInterface as GenerateService;
use App\Repositories\Interfaces\GenerateRepositoryInterface as GenerateRepository;


use Illuminate\Support\Facades\App;
// use App\Repositories\Interfaces\GenerateRepositoryInterface as GenerateRepository;
//Neu muon view hieu duoc controller thi phai compact
class GenerateController extends Controller
{
    protected $generateService;
    protected $generateRepository;
    public function __construct(GenerateService $generateService, GenerateRepository $generateRepository)
    {
        $this->generateService = $generateService;
        $this->generateRepository = $generateRepository;
    }
    public function index(Request $request)
    {
        try {
            $this->authorize('modules', 'generate.index');
            $generates = $this->generateService->paginate($request);
            // dd($Generates); //hien thi thanh vienƯ
            $config = [
                'js' => [
                    'backend/js/plugins/switchery/switchery.js',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
                ],
                'css' => [
                    'backend/css/plugins/switchery/switchery.css',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
                ],
                'model' => 'Generate'
            ];
            $config['seo'] = __('message.generate');
            // dd($config['seo']);
            $template = 'backend.generate.index';
            return view('backend.dashboard.layout', compact('template', 'config', 'generates'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function create()
    {
        try {
            $this->authorize('modules', 'generate.create');
            // dd($provinces);
            // dd($province);
            $config = $this->configData();
            $config['seo'] = __('message.generate');
            $config['method'] = 'create';
            $config['model'] = 'Generate';
            $template = 'backend.generate.store';
            return view('backend.dashboard.layout', compact('template', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function store(StoreGenerateRequest $request)
    {
        if ($this->generateService->create($request)) {
            return redirect()->route('generate.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('generate.index')->with('error', 'Thêm mới bản ghi không thành công');
    }
    public function edit($id)
    {
        try {
            $this->authorize('modules', 'generate.update');
            $generate = $this->generateRepository->findById($id);
            $config = $this->configData();
            // dd($Generate);
            // dd($provinces);
            // dd($province);
            $template = 'backend.generate.store';

            $config['seo'] = __('message.generate');
            $config['method'] = 'edit';
            $config['model'] = 'Generate';
            return view('backend.dashboard.layout', compact('template', 'config', 'generate'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function update($id, UpdateGenerateRequest $request)
    {
        if ($this->generateService->update($id, $request)) {
            return redirect()->route('generate.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('generate.index')->with('error', 'Cập nhật bản ghi không thành công');
    }
    public function delete($id)
    {
        try {
            $this->authorize('modules', 'generate.destroy');
            $config['seo'] = __('message.generate');
            $generate = $this->generateRepository->findById($id);
            $template = 'backend.generate.delete';
            return view('backend.dashboard.layout', compact('template', 'generate', 'config'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào chức năng này.');
        }
    }
    public function destroy($id)
    {
        if ($this->generateService->destroy($id)) {
            return redirect()->route('generate.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('generate.index')->with('error', 'Xóa bản ghi không thành công');
    }
    private function configData()
    {
        return [
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
    }
}
