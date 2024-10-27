<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;

class RouterController extends FrontendController
{
    protected $language;
    protected $routerRepository;
    protected $router;

    public function __construct(RouterRepository $routerRepository)
    {
        parent::__construct();
        $this->routerRepository = $routerRepository;
    }
    public function index(string $canonical = '', $page = 1, Request $request)
    {
        $this->getRouter($canonical);
        if (!is_null($this->router) && !empty($this->router)) {
            $method = 'index';
            $controllerClass = $this->router->controllers;
            if (strpos($controllerClass, 'App\Http\Controller') === 0) {
                $controllerClass = str_replace('App\Http\Controller', 'App\Http\Controllers', $controllerClass);
            }
            echo app($controllerClass)->{$method}($this->router->module_id, $request, $page);
        }
    }
    public function page(string $canonical = '', $page, Request $request)
    {
        $this->getRouter($canonical);
        $page = (!isset($page)) ? 1 : $page;
        if (!is_null($this->router) && !empty($this->router)) {
            $method = 'index';
            $controllerClass = $this->router->controllers;
            if (strpos($controllerClass, 'App\Http\Controller') === 0) {
                $controllerClass = str_replace('App\Http\Controller', 'App\Http\Controllers', $controllerClass);
            }
            echo app($controllerClass)->{$method}($this->router->module_id, $request, $page);
        }
    }

    public function getRouter($canonical)
    {
        $this->router = $this->routerRepository->findByCondition(
            [
                ['canonical', '=', $canonical],
                ['language_id', '=', $this->language]
            ]
        );
    }
}
