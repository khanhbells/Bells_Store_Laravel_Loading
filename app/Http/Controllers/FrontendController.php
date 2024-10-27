<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\System;


// use App\Repositories\Interfaces\languageRepositoryInterface as LanguageRepository;
//Neu muon view hieu duoc controller thi phai compact
class FrontendController extends Controller
{
    protected $language;
    protected $systemRepository;
    protected $system;
    public function __construct()
    {
        $this->setLanguage();
        $this->setSystem();
    }

    public function setLanguage()
    {
        $locale = app()->getLocale();
        $language = Language::where('canonical', $locale)->first();
        $this->language = $language->id;
    }

    public function setSystem()
    {
        $this->system = convert_array(System::where('language_id', $this->language)->get(), 'keyword', 'content');
    }
}
