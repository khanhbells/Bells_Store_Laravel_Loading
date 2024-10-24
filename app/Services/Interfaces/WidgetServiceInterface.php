<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface WidgetServiceInterface
 * @package App\Services\Interfaces
 */
interface WidgetServiceInterface
{
    public function paginate($request);
    public function create(Request $request, $languageId);
    public function update($id, Request $request, $languageId);
    public function destroy($id);
    public function saveTranslate($request);
    public function findWidgetByKeyword(string $keyword = '', int $language);
}
