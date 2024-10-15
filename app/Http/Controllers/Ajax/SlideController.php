<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\SlideServiceInterface as SlideService;
use App\Models\Language;


class SlideController extends Controller
{
    protected $slideService;
    protected $language;

    public function __construct(
        SlideService $slideService
    ) {
        $this->slideService = $slideService;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }
    public function updateImage(Request $request)
    {
        $id = $request->input('id');
        $this->slideService->updateImage($id, $request, $this->language);
    }
}
