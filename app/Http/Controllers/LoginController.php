<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }
    public function authenticate(Request $request)
    {
        $validater=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required',
        ]);
        if($validater->passes())
        {
            if(Auth::attempt(['email'=>$request->email,'password'=>$request->password]))
            {
                
                if(Auth::user()->role !="user")
                {
                    Auth::logout();
                    return redirect()->route('account.login')->with('error','You are not a authorised person to access the page');
                }
                else{
                return redirect()->route('account.dashboard');
                }
            }
            else{
                return redirect()->route('account.login')->with('error','Either email or password is Incorrect');
            }
        }else{
            return redirect()->route('account.login')->withInput()->withErrors($validater);
        }
    }
    public function dashboard()
    {
        return view('dashboard');
    }
    public function register()
    {   
        return view('register');
    }

    public function processregister(Request $request)
    {
        $validater=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed',
        ]);
        if($validater->passes())
        {
            $user = new User();
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password=Hash::make($request->password);
            $user->role='user';
            $user->save();
            return redirect()->route('account.register')->with('success','You have registered successfully');
        }else{
            return redirect()->route('account.register')->withInput()->withErrors($validater);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }
}
