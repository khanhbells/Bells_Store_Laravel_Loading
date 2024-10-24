<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
// use App\Models\User;
use App\Models\Language;
use App\Models\Attribute;
use App\Repositories\SystemRepository;

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class HomeController extends FrontendController
{
    protected $language;
    protected $slideRepository;
    protected $widgetService;

    public function __construct(
        SlideRepository $slideRepository,
        WidgetService $widgetService
    ) {
        $this->slideRepository = $slideRepository;
        $this->widgetService = $widgetService;
        parent::__construct();
    }
    public function index()
    {
        $config = $this->config();
        $widget = [
            'category' => $this->widgetService->findWidgetByKeyword('category', $this->language, ['children' => true]),
            // 'bai-viet' => $this->widgetService->findWidgetByKeyword('bai-viet', $this->language),
        ];
        $slides = $this->slideRepository->findByCondition(...$this->slideAgrument());
        $slides->slideItems = $slides->item[$this->language];
        return view('frontend.homepage.home.index', compact(
            'config',
            'slides',
        ));
    }

    private function slideAgrument()
    {
        return [
            'condition' => [
                config('app.general.defaultPublish'),
                ['keyword', '=', 'banner']
            ]
        ];
    }
    private function config()
    {
        return [];
    }
}
