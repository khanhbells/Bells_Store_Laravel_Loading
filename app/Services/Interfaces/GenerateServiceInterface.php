<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface GenerateServiceInterface extends BaseServiceInterface
{
    public function paginate($request);
    public function create(Request $request);
    public function update($id, Request $request);
    public function destroy($id);
}
