<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
use App\Services\Interfaces\SlideServiceInterface as SlideService;
use App\Enums\SlideEnum;
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
    protected $slideService;

    public function __construct(
        SlideRepository $slideRepository,
        WidgetService $widgetService,
        SlideService $slideService,
    ) {
        $this->slideRepository = $slideRepository;
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
        parent::__construct();
    }
    public function index()
    {
        $config = $this->config();

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'category'],
            ['keyword' => 'bai-viet', 'children' => true],
            ['keyword' => 'category-highlight'],
            ['keyword' => 'category-home', 'children' => true, 'promotion' => true, 'object' => true],
        ], $this->language);

        $slides = $this->slideService->getSlide([SlideEnum::BANNER, SlideEnum::BANNER_BODY], $this->language);
        return view('frontend.homepage.home.index', compact(
            'config',
            'slides',
            'widgets',
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
