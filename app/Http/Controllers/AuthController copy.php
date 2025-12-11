<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Auth;
use App\Services\SupabaseService;
use App\Auth\SupabaseUser;

class AuthController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function loginView()
    {
        return view('login');
    }

    /*public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        if (\Auth::attempt(array('email' => $validated['email'], 'password' => $validated['password']))) {
            return redirect()->route('index');
        } else {
            $validator->errors()->add(
                'password', 'The password does not match with username'
            );
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }*/

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $supabase = $this->supabase->client();

        $response = $supabase->auth()->signInWithPassword([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if (isset($response['data']['user'])) {
            // Crée l’objet Authentifiable
            $user = new SupabaseUser($response['data']['user']);

            // Connecte l’utilisateur
            Auth::login($user);

            return redirect()->intended('/index');
        }

        return back()->withErrors([
            'email' => 'Les identifiants sont invalides.',
        ]);
    }

    

    public function registerView(){
        return view('register');
    }

    public function register(Request $request){
        
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'email' => ['required', 'email','unique:users'],
            'password' => ['required',"confirmed", Password::min(7)],
        ]);

        $validated = $validator->validated();

        $user = User::create([
            'name' => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"])
        ]);

        auth()->login($user);

        return redirect()->route('index');
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }
}
