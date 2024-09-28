<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest; //Xu ly ngoai le
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Http\Requests\StoreUserRequest;
use App\Services\Interfaces\UserServiceInterface as UserService;

class AuthController extends Controller
{
    protected $provincerepository;
    protected $userService;
    public function __construct(ProvinceRepository $provincerepository, UserService $userService)
    {
        $this->provincerepository = $provincerepository;
        $this->userService = $userService;
    }
    public function index()
    {
        return view('backend.auth.login');
    }
    public function login(AuthRequest $request)
    {
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ];
        // dd($credentials); //debug
        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard.index')->with('success', 'Đăng nhập thành công');
        } else {
            return redirect()->route('auth.admin')->with('error', 'Email hoặc mật khẩu không chính xác');
        }
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth.admin');
    }
    public function register()
    {
        $provinces = $this->provincerepository->all();

        return view('backend.auth.register', compact('provinces'));
    }
    public function store(StoreUserRequest $request)
    {
        // dd($request);
        if ($this->userService->create($request)) {
            return redirect()->route('auth.admin')->with('success', 'Đăng ký tài khoản thành công, hãy đăng nhập để tiếp tục');
        }
        return redirect()->route('auth.register')->with('error', 'Thêm mới bản ghi không thành công');
    }
}
