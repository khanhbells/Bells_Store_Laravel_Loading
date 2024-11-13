<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface ReviewServiceInterface
 * @package App\Services\Interfaces
 */
interface ReviewServiceInterface
{
    public function create(Request $request);
    public function paginate($request);
    public function destroy($id);
}
