<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class FrontendController extends Controller
{
    protected $language;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }
}
