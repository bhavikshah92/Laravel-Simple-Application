<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register()
    {

      return view('auth.register');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'usertype'=>'User',
        ]);

        return redirect('home');
    }

    public function login()
    {

      return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $isAuthroized = Auth::attempt([
            'email'    => $request->get('email'),
            'password'    => $request->get('password'),
        ]);

        if($isAuthroized === true){
            //set session
            Session::put('email',$request->get('email'));
            $users = DB::table('users')->where('email',$request->get('email'))->get();
            foreach ($users as $user) {
                Session::put('name',$user->name);
                Session::put('usertype',$user->usertype);
                //Session::put('password',$request->get('password'));
            }
            if (auth()->user()->usertype=="Admin") {
                return redirect()->intended('home');
            }
            else{
                return redirect()->intended('userhome');
            }
        }
        return redirect('login')->with('error', 'Oppes! You have entered invalid credentials');
    }

    public function logout() {
        Session::flush();
        Auth::logout();

        return redirect('login');
    }

    public function home()
    {
        if(Auth::check()){
            if (auth()->user()->usertype=="Admin") {
                return view('home');
            }
            else{
                return view('userhome');
            }
        }
        else{
            return redirect('login');
        }
    }

    public function edit($id){
        $employee = DB::table('employee')->find($id);
        return view('employee.update', compact('employee'));
    }

    public function loginAPI(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $isAuthroized = Auth::attempt([
            'email'    => $request->get('email'),
            'password'    => $request->get('password'),
        ]);

        if($isAuthroized === true){
            
            $affected = DB::table('users')
            ->where('email',$request->get('email'))
            ->update(['api_token' => Str::random(60)]);
            $users = DB::table('users')->select(array('id', 'name', 'email','usertype','api_token'))->where('email',$request->get('email'))->get();
            return response()->json(['data' => $users->toArray()], 200);
        }
        else{
            return response()->json(['data' => null], 401);
        }
    }
}