<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface ProductCatalogueServiceInterface extends BaseServiceInterface
{
    public function paginate($request, $languageId);
    public function create(Request $request, $languageId);
    public function update($id, Request $request, $languageId);
    public function destroy($id, $languageId);
    public function setAttribute($product);
    public function getFilterList(array $attribute = [], $languageId);
}
