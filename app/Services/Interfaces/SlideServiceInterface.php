<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface SlideServiceInterface
 * @package App\Services\Interfaces
 */
interface SlideServiceInterface
{
    public function paginate($request);
    public function create(Request $request, $languageId);
    public function update($id, Request $request, $languageId);
    public function destroy($id);
    public function convertSlideArray(array $slide = []): array;
    public function updateImage($id, $requestSlide, $languageId);
}
