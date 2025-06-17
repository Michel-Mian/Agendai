<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;   
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

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
        $user->profile_photo_url = $request->profile_photo_url;
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
        return redirect('/');
    }

    private function getUser($id)
    {
        return User::findOrFail($id);
    }

    public function editProfile($id)
    {
        $user = $this->getUser($id);
        return view('myProfile', ['user' => $user, 'title' => 'Meu Perfil']);
    }

    public function editConfig($id)
    {
        $user = $this->getUser($id);
        return view('config', ['user' => $user, 'title' => 'Configurações']);
    }

    public function updateProfile(Request $request, $id)
    {
    $user = $this->getUser($id);

    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email,' . $user->id, // ignora o próprio usuário
    ],
    [
        'name.required' => 'Ops, o nome é obrigatório.',
        'email.required' => 'Ops, o e-mail é obrigatório.',
        'email.email' => 'Por favor, digite um e-mail válido.',
        'email.unique' => 'Hmmm, parece que esse e-mail já existe.',
    ]);

    $user->name = $request->name;
    $user->email = $request->email;

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('profile_photos', 'public');
            $user->profile_photo_url = 'storage/' . $path;
        }

    $user->save();
    return redirect()->back()->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updateConfig(Request $request, $id)
    {
        $user = $this->getUser($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id, // ignora o próprio usuário
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('profile_photos', 'public');
            $user->profile_photo_url = 'storage/' . $path;
        }

        $user->save();
        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso!');
    }

    public function getForgot()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    
        $status = Password::sendResetLink(
            $request->only('email')
        );
    
        return $status === Password::ResetLinkSent
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function getReset(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
    
                $user->save();
    
                event(new PasswordReset($user));
            }
        );
    
        return $status === Password::PasswordReset
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Senha alterada com sucesso!');
    }
}
