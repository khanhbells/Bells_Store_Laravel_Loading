<?php

namespace App\Services;

use App\Services\Interfaces\BaseServiceInterface as BaseServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * Class LanguageService
 * @package App\Services
 */
class BaseService implements BaseServiceInterface
{
    public function __construct(BaseServiceInterface $baseServiceInterface) {}
    public function currentLanguage()
    {
        return 1;
    }
}
