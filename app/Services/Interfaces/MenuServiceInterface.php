<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface MenuServiceInterface extends BaseServiceInterface
{
    public function paginate($request, $languageId);
    public function save(Request $request, $languageId);
    public function destroy($id);
    public function saveChildren($request, $languageId, $menu);
    public function getAndconvertMenu($menu = null, $language = 1): array;
    public function dragUpdate(array $json = [], int $menuCatalogueId = 0, int $languageId = 1, $parentId = 0);
    public function convertMenu($menuList = null);
    public function findMenuItemTranslate($menus, int $currentLanguage = 1, int $languageId = 1);
    public function saveTranslateMenu($request, int $languageId = 1);
}
