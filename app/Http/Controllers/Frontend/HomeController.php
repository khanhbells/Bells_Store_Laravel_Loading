<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Models\Language;
use App\Models\Attribute;

// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class HomeController extends FrontendController
{
    protected $language;

    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $config = $this->config();
        return view('frontend.homepage.home.index', compact(
            'config'
        ));
    }
    private function config()
    {
        return [];
    }
}
