<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;

class MenuComposer
{
    protected $menuCatalogueRepository;
    protected $language;
    public function __construct(
        MenuCatalogueRepository $menuCatalogueRepository,
        $language
    ) {
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->language = $language;
    }

    public function compose(View $view)
    {
        $agrument = $this->agrument($this->language);
        $menuCatalogue = $this->menuCatalogueRepository->findByCondition(...$agrument);
        $menus = [];
        if (count($menuCatalogue)) {
            foreach ($menuCatalogue as $key => $val) {
                $menus[$val->keyword] = frontend_recursive_menu(recursive($val->menus));
            }
        }
        dd($menus);
        $view->with('menu', $menus);
    }
    private function agrument($language)
    {
        return [
            // 'condition' => [
            //     ['keyword', '=', 'menu-content']
            // ],
            'flag' => true,
            'relation' => [
                'menus' => function ($query) use ($language) {
                    $query->orderBy('order', 'desc');
                    $query->with([
                        'languages' => function ($query) use ($language) {
                            $query->where('language_id', $language);
                        }
                    ]);
                }
            ]
        ];
    }
}
