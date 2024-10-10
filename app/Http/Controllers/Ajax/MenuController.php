<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface  as MenuCatalogueRepository;
use App\Services\Interfaces\MenuCatalogueServiceInterface  as MenuCatalogueService;
use App\Models\Language;
use App\Http\Requests\StoreMenuCatalogueRequest;


class MenuController extends Controller
{
    protected $menuCatalogueRepository;
    protected $menuCatalogueService;
    protected $language;

    public function __construct(
        MenuCatalogueRepository $menuCatalogueRepository,
        MenuCatalogueService $menuCatalogueService
    ) {
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->menuCatalogueService = $menuCatalogueService;
    }
    public function createCatalogue(StoreMenuCatalogueRequest $request)
    {
        $menuCatalogue = $this->menuCatalogueService->create($request);
        if ($menuCatalogue !== FALSE) {
            return response()->json([
                'code' => 0,
                'messages' => 'Tạo nhóm menu thành công!',
                'data' => $menuCatalogue
            ]);
        }
        return response()->json(
            [
                'messages' =>  'Có vấn đề xảy ra, Hãy thử lại',
                'code' => 1
            ]
        );
    }
}
