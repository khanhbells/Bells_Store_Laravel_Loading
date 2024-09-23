<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class DashboardController extends Controller
{

    public function __construct() {}
    public function changeStatus(Request $request)
    {
        $post = $request->input();
        $serviceInterfaceNamespage = '\App\Services\\' . ucfirst($post['model']) . 'Service';
        if (class_exists($serviceInterfaceNamespage)) {
            $serviceInstance = app($serviceInterfaceNamespage);
        }
        $flag = $serviceInstance->updateStatus($post);
        return response()->json(['flag' => $flag]);
    }
    public function changeStatusAll(Request $request)
    {
        $post = $request->input();
        $serviceInterfaceNamespage = '\App\Services\\' . ucfirst($post['model']) . 'Service';
        if (class_exists($serviceInterfaceNamespage)) {
            $serviceInstance = app($serviceInterfaceNamespage);
        }
        $flag = $serviceInstance->updateStatusAll($post);
        return response()->json(['flag' => $flag]);
    }
}
// array:2 [ // app\Http\Controllers\Ajax\LocationController.php:22
//     "data" => array:1 [
//       "province_id" => "01"
//     ]
//     "target" => "districts"
//   ]