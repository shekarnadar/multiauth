<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
Use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }
    public function authenticate(Request $request)
    {
        $validater=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required',
        ]);
        if($validater->passes())
        {
            if(Auth::guard('admin')->attempt(['email'=>$request->email,'password'=>$request->password]))
            {
                if(Auth::guard('admin')->user()->role !="admin")
                {
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error','You are not a authorised person to access the page');
                }
                else{
                return redirect()->route('admin.dashboard');
                }
            }

            else{
                return redirect()->route('admin.login')->with('error','Either email or password is Incorrect');
            }
        }else{
            return redirect()->route('admin.login')->withInput()->withErrors($validater);
        }
    }
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
