<?php

namespace App\Services;

use App\Services\Interfaces\MenuServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;


/**
 * Class MenuService
 * @package App\Services
 */
class MenuService extends BaseService implements MenuServiceInterface
{
    protected $menuRepository;
    protected $nestedset;
    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }
    public function paginate($request, $languageId)
    {
        return [];
    }
    public function create(Request $request, $languageId)
    {
        DB::beginTransaction();

        try {
            $payload = $request->only('menu', 'menu_catalogue_id', 'type');
            if (count($payload['menu']['name'])) {
                foreach ($payload['menu']['name'] as $key => $val) {
                    $menuArray = [
                        'menu_catalogue_id' => $payload['menu_catalogue_id'],
                        'type' => $payload['type'],
                        'order' => $payload['menu']['order'][$key],
                        'user_id' => Auth::id()
                    ];
                    $menu = $this->menuRepository->create($menuArray);


                    if ($menu->id > 0) {
                        $menu->languages()->detach([$languageId, $menu->id]);
                        $payloadLanguage = [
                            'language_id' => $languageId,
                            'name' => $val,
                            'canonical' => $payload['menu']['canonical'][$key],
                        ];
                        $this->menuRepository->createPivot($menu, $payloadLanguage, 'languages');
                    }
                }
                $this->nestedset = new Nestedsetbie([
                    'table' => 'menus',
                    'foreignkey' => 'menu_id',
                    'isMenu' => true,
                    'language_id' => $languageId,
                ]);
                $this->nestedset();
                // die();
            }
            // die();


            DB::commit();
            return true;
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
    public function update($id, Request $request, $languageId)
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

    public function destroy($id, $languageId)
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
