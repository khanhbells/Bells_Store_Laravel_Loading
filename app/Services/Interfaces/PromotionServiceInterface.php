<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface PromotionServiceInterface extends BaseServiceInterface
{
    public function paginate($request);
    public function create(Request $request, $languageId);
    public function update($id, Request $request, $languageId);
    public function destroy($id);
    public function saveTranslate($request);
}
