<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface SystemServiceInterface
{
    public function save($request, $languageId);
}
