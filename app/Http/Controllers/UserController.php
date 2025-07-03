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
use Illuminate\Support\Facades\Http;

/**
 * Controller responsible for user registration, login, profile management,
 * password reset, and preference settings.
 */
class UserController extends Controller
{
    /**
     * Shows the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function create(){
        return view('auth/register');
    }

    /**
     * Shows the login form.
     *
     * @return \Illuminate\View\View
     */
    public function index(){
        return view('auth/login');
    }

    /**
     * Handles user registration.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter pelo menos :min caracteres.',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->profile_photo_url = $request->profile_photo_url;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect('dashboard');
    }

    /**
     * Attempts to log the user in.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request){
        $user = $request->only('email', 'password');

        if(Auth::attempt($user)){
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Incorrect email or password.',
        ]);
    }

    /**
     * Logs the user out.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    /**
     * Retrieves the user by ID or fails.
     *
     * @param int $id
     * @return \App\Models\User
     */
    private function getUser($id)
    {
        return User::findOrFail($id);
    }

    /**
     * Shows the profile editing page with currency info.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editProfile($id)
    {
        $user = $this->getUser($id);

        // Fetch available currencies from AwesomeAPI
        $response = Http::get('https://economia.awesomeapi.com.br/json/available/uniq');
        $currencies = [];
        if ($response->successful()) {
            $currencies = $response->json();
        }

        // Cotação
        $token = env('AWESOME_API_TOKEN');
        $currency = $user->currency;
        if ($currency === 'BRL') {
            $cotacao = 1;
        } else {
            $apiResponse = Http::get("https://economia.awesomeapi.com.br/json/last/{$currency}-BRL?token={$token}");
            $cotacao = null;
            if ($apiResponse->successful()) {
                $json = $apiResponse->json();
                if (isset($json[$currency.'BRL']['bid'])) {
                    $cotacao = $json[$currency.'BRL']['bid'];
                }
            }
        }

        return view('myProfile', [
            'user' => $user,
            'title' => 'Meu Perfil',
            'cotacao' => $cotacao,
            'currencies' => $currencies
        ]);
    }

    /**
     * Shows the configuration page.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editConfig($id)
    {
        $user = $this->getUser($id);
        return view('config', ['user' => $user, 'title' => 'Configurações']);
    }

    /**
     * Updates user profile (name, email, photo).
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request, $id)
    {
        $user = $this->getUser($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('profile_photos', 'public');
            $user->profile_photo_url = 'storage/' . $path;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Updates user configuration (name, email, password, photo).
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateConfig(Request $request, $id)
    {
        $user = $this->getUser($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
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

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Shows the forgot password form.
     *
     * @return \Illuminate\View\View
     */
    public function getForgot()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handles the forgot password request and sends reset link.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::ResetLinkSent
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Shows the password reset form.
     *
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function getReset(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handles the password reset logic.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Changes the user's current password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully!');
    }

    /**
     * Updates user currency preferences.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePreferences(Request $request, $id)
    {
        $user = $this->getUser($id);

        $request->validate([
            'currency' => 'required',
            // add more preference fields as needed
        ]);

        $user->currency = $request->currency;
        // $user->language = $request->language;
        // $user->theme = $request->theme;
        $user->save();

        return redirect()->back()->with('success', 'Preferences updated successfully!');
    }
}
