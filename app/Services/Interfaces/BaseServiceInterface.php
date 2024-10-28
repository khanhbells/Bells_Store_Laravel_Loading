<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface BaseServiceInterface
{
    public function currentLanguage();
    public function objectCount($objectCatalogues);
}
