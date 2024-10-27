<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface ProductServiceInterface extends BaseServiceInterface
{
    public function paginate($request, $languageId, $productCatalogue = null, $page, $extend = []);
    public function create(Request $request, $languageId);
    public function update($id, Request $request, $languageId);
    public function destroy($id);
    public function combineProductAndPromotion($productId = [], $products);
    public function paginateIndex(mixed $productCatalogue = null);
}
