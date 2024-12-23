<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface LanguageServiceInterface
{
    public function paginate($request);
    public function create(Request $request);
    public function update($id, Request $request);
    public function destroy($id);
    public function switch($id);
    public function saveTranslate($option, $request);
}
