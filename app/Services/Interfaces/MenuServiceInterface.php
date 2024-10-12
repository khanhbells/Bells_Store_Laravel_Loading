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
    public function create(Request $request, $languageId);
    public function update($id, Request $request, $languageId);
    public function destroy($id, $languageId);
    public function saveChildren($request, $languageId, $menu);
    public function getAndconvertMenu($menu = null, $language = 1): array;
    public function dragUpdate(array $json = [], int $menuCatalogueId = 0, int $languageId = 1, $parentId = 0);
}
