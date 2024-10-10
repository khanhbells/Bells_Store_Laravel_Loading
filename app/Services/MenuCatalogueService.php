<?php

namespace App\Services;

use App\Services\Interfaces\MenuCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

/**
 * Class MenuCatalogueService
 * @package App\Services
 */
class MenuCatalogueService extends BaseService implements MenuCatalogueServiceInterface
{
    protected $menuCatalogueRepository;
    public function __construct(MenuCatalogueRepository $menuCatalogueRepository)
    {
        $this->menuCatalogueRepository = $menuCatalogueRepository;
    }
    public function paginate($request)
    {
        return [];
    }
    public function create(Request $request)
    {
        DB::beginTransaction();

        try {
            $payload = $request->only('name', 'keyword');
            $payload['keyword'] = Str::slug($payload['keyword']);
            $menuCatalogue = $this->menuCatalogueRepository->create($payload);
            DB::commit();
            return [
                'name' => $menuCatalogue->name,
                'id' => $menuCatalogue->id
            ];
        } catch (Exception $e) {
            DB::rollBack();
            // In ra lỗi để debug
            dd($e);
            // Ghi lỗi vào log
            Log::error($e->getMessage());
            // Trả về mã lỗi 500
            abort(500, 'Đã xảy ra lỗi trong quá trình tạo bản ghi.');
        }
    }
    // --------------------------------------------------------------------------------
    public function update($id, Request $request)
    {
        DB::beginTransaction();
        try {

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }

    private function paginateselect()
    {
        return ['menus.id', 'menus.publish', 'menus.image', 'menus.order', 'tb2.name', 'tb2.canonical'];
    }
    private function payload()
    {
        return ['follow', 'publish', 'image', 'album', 'menu_catalogue_id'];
    }
    private function payloadLanguage()
    {
        return  ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
