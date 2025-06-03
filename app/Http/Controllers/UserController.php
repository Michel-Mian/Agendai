<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;   

class UserController extends Controller
{
    public function create(){
        return view('auth/register');
    }

    public function index(){
        return view('auth/login');
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect('dashboard');

    }

    public function login(Request $request){
        $user = $request->only('email', 'password');

        if(Auth::attempt($user)){
            return redirect()->intended('dashboard');
        }
        return back()->withErrors([
            'email' => 'Email ou senha incorretos.',
        ]);
        
        // $user = DB::table('users')->where('email', $request->email)->first();
        // if ($user && Hash::check($request->password, $user->password)) {
        //     // Aqui você poderia usar Auth::login() se estivesse usando Eloquent
        //     return redirect('dashboard');
        // }

        // return back()->withErrors([
        //     'email' => 'Email ou senha incorretos.',
        // ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
