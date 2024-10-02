<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface {Module}ServiceInterface extends BaseServiceInterface
{
    public function paginate($request, $languageId);
    public function create(Request $request, $languageId);
    public function update($id, Request $request, $languageId);
    public function destroy($id, $languageId);
}
