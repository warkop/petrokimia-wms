<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Models\Role;
use App\Http\Models\Users;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    private $title = 'Login';
    protected $redirectTo = '/';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function index(Request $request)
    {
        if(Auth::check()){
            return redirect('home');
        }else{
            $data['title'] = $this->title;
            $data['source'] = ($request->get('source'))? $request->get('source') : '';
            return view('login', $data);
        }
    }

    public function authenticate(Request $request)
    {
        if(Auth::check()){
            return $request->expectsJson() ? response()->json(helpResponse(200, [], 'Anda sudah login')) : redirect()->intended('home');
        }else{
            $username       = $request->input('username');
            $password       = $request->input('password');

            $data = ['username' => $username, 'password' => $password];           
            if (Auth::attempt($data)) {
                $this->writeLog('Login', 3, 'User dengan username '.auth()->user()->username.' yang mempunyai role '.auth()->user()->role->nama.' berhasil login');
                return $request->expectsJson() ? response()->json(helpResponse(200, ['user' => ''], 'Selamat Anda berhasil login'), 200) : redirect()->intended('home');
            }else{
                $alerts[] = array('warning', 'Username atau Password Anda salah', 'Pemberitahuan');
                $request->session()->flash('alerts', $alerts);
                return $request->expectsJson() ? response()->json(helpResponse(401, [], 'Username atau Password Anda salah'), 401) : redirect()->intended('login');
            }
        }
    }

    public function logout(Request $request)
    {
        if(Auth::check()){
            $this->writeLog('Logout', 4, 'User dengan username ' . auth()->user()->username. ' berhasil logout');
            Auth::logout();
            $request->session()->invalidate();
        }

        return redirect('/');
    }
}
