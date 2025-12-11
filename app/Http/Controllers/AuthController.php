<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Services\SupabaseService;
use App\Models\User;
use App\Models\Membre;

use App\Services\RecompenseService;

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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $response = $this->supabase->login($request->email, $request->password);

        // ⚠️ Si erreur côté Supabase
        if (isset($response['error'])) {
            return back()->withErrors([
                'email' => $response['error_description'] ?? 'Identifiants invalides.',
            ]);
        }

        // ✅ Connexion OK → récupérer l'user
        if (isset($response['user'])) {
            $supabaseUser = $response['user'];

            $user = User::firstOrCreate(
                ['email' => $supabaseUser['email']],
                [
                    'name' => $supabaseUser['user_metadata']['full_name'] ?? $supabaseUser['email'],
                    'password' => Hash::make(uniqid()), // inutile car géré par Supabase
                    'supabase_user_id' => $supabaseUser['id'], // ici c'est OK
                ]
            );

            Auth::login($user);
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Identifiants invalides.',
        ]);
    }*/

    public function login(Request $request, RecompenseService $recompenseService)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $response = $this->supabase->login($request->email, $request->password);

    // ⚠️ Si erreur côté Supabase
    if (isset($response['error'])) {
        return back()->withErrors([
            'email' => $response['error_description'] ?? 'Identifiants invalides.',
        ]);
    }

    // ✅ Connexion OK → récupérer l'user
    if (isset($response['user'])) {
        $supabaseUser = $response['user'];

        $user = User::firstOrCreate(
            ['email' => $supabaseUser['email']],
            [
                'name' => $supabaseUser['user_metadata']['full_name'] ?? $supabaseUser['email'],
                'password' => Hash::make(uniqid()), // inutile car géré par Supabase
                'supabase_user_id' => $supabaseUser['id'],
            ]
        );

        // ✅ Ajout du remember me
        $remember = $request->boolean('remember', false);
        Auth::login($user, $remember);
        //Auth::login($user);

        // 🔗 Récupérer le membre lié
        $membre = Membre::where('user_id', $user->id)->first();
        if ($membre) {
            // 🎁 Attribuer récompense de connexion fréquente
            $recompenseService->attribuerRecompense('CONNEXION_FREQ', $membre, null, $membre->id);
        }

        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors([
        'email' => 'Identifiants invalides.',
    ]);
}



    public function registerView(){
        return view('register');
    }

    /*public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:7',
        ]);

        $response = $this->supabase->signUp($request->email, $request->password, [
            'full_name' => $request->name,
        ]);

        // Vérifier si l'ID existe
        if (isset($response['id'])) {
            // OK, l’utilisateur est bien créé
            $supabaseUser = $response;

            // Vérifier / créer un user local
            $user = User::firstOrCreate(
                ['email' => $supabaseUser['email']],
                [
                    'name' => $supabaseUser['user_metadata']['full_name'] ?? $supabaseUser['email'],
                    'password' => Hash::make(uniqid()), // mot de passe local inutile
                    'supabase_user_id' => $supabaseUser['id'],
                ],
            );

            Auth::login($user);
            return redirect()->intended(route('dashboard'));
        }

        // Sinon → erreur
        return back()->withErrors([
            'email' => 'Impossible de créer le compte sur Supabase.',
        ]);
    }*/


public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|confirmed|min:7',
    ]);

    // 🔗 Redirection après vérification email
    $redirectUrl = env('APP_URL') . '/emails/verify';
    
    // Appel Supabase signup
    $response = $this->supabase->signUp(
        $request->email,
        $request->password,
        ['full_name' => $request->name],
        $redirectUrl
    );

    // 🔍 Vérifie si Supabase a renvoyé un utilisateur
    $supabaseUser = null;

    if (isset($response['user']['id'])) {
        // Format Supabase Cloud
        $supabaseUser = $response['user'];
    } elseif (isset($response['id'])) {
        // Format Supabase Self-hosted
        $supabaseUser = $response;
    }

    if ($supabaseUser) {
        // ✅ Créer ou retrouver l’utilisateur local
        $user = User::firstOrCreate(
            ['email' => $supabaseUser['email']],
            [
                'name' => $supabaseUser['user_metadata']['full_name'] ?? $supabaseUser['email'],
                'password' => Hash::make(uniqid()), // mot de passe local inutile
                'supabase_user_id' => $supabaseUser['id'],
            ],
        );

        Auth::login($user);

            // ✅ Pas besoin d’envoyer de mail toi-même — Supabase s’en charge
            return redirect()->route('emails.verify')
                ->with('status', 'Un e-mail de confirmation vous a été envoyé. Veuillez vérifier votre boîte de réception.');

        //return redirect()->intended(route('dashboard'));
    }

    // ❌ En cas d’échec
    return back()->withErrors([
        'email' => 'Impossible de créer le compte sur Supabase.', //Détails: . json_encode($response)
    ]);
}


    /*public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:7',
        ]);

        // 🔗 Redirection après vérification email
        $redirectUrl = env('APP_URL') . '/emails/verify';

        // ✅ Appel Supabase pour inscription + envoi automatique du mail
        $response = $this->supabase->signUp(
            $request->email,
            $request->password,
            ['full_name' => $request->name],
            $redirectUrl
        );
        
//dd($response);

        // Vérifie si le compte a bien été créé côté Supabase
        if (isset($response['user']['id']) || isset($response['id'])) {
            $supabaseUser = $response['user'] ?? $response;

            // 🔐 Crée aussi le user local si nécessaire
            $user = User::firstOrCreate(
                ['email' => $supabaseUser['email']],
                [
                    'name' => $supabaseUser['user_metadata']['full_name'] ?? $supabaseUser['email'],
                    'password' => Hash::make(uniqid()),
                    'supabase_user_id' => $supabaseUser['id'],
                ]
            );

            // ✅ Pas besoin d’envoyer de mail toi-même — Supabase s’en charge
            return redirect()->route('emails.verify')
                ->with('status', 'Un e-mail de confirmation vous a été envoyé. Veuillez vérifier votre boîte de réception.');
        }

        // ❌ En cas d’erreur
        return back()->withErrors([
            'email' => 'Impossible de créer le compte sur Supabase. Vérifiez la configuration.',
        ]);
    }*/


    public function logout(Request $request)
    {
        auth()->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

        return redirect()->route('login');
    }


    // --- Étape 1 : Affichage du formulaire "Mot de passe oublié"
    public function forgotPasswordView()
    {
        return view('auth.forgot-password');
    }

    // --- Étape 2 : Traitement du formulaire
    public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    // Utiliser l'URL de redirection de Supabase ou fallback sur /reset-password
    $redirectUrl = env('SUPABASE_REDIRECT_URL', url('/reset-password'));

    // Appel Supabase pour envoyer le mail de récupération
    $response = $this->supabase->resetPasswordForEmail($request->email, [
        'redirect_to' => $redirectUrl,
    ]);
//dd($redirectUrl);
    if (isset($response['error'])) {
        return back()->withErrors([
            'email' => $response['error_description'] ?? 'Erreur lors de la demande de réinitialisation.'
        ]);
    }

    return back()->with('status', 'Un lien de réinitialisation a été envoyé à votre adresse e-mail.');
}


    // --- Étape 3 : Vue "Nouveau mot de passe"
    public function resetPasswordView(Request $request)
    {
        // ⚠️ Supabase renvoie un paramètre `token` (et non `access_token`)
        $accessToken = $request->query('token');

        if (!$accessToken) {
            return redirect()->route('loginView')->withErrors(['email' => 'Lien invalide ou expiré.']);
        }

        return view('auth.reset-password', ['accessToken' => $accessToken]);
    }

    // --- Étape 4 : Traitement du nouveau mot de passe
    public function resetPassword(Request $request)
    {
        $request->validate([
            'access_token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $response = $this->supabase->updateUser($request->access_token, [
            'password' => $request->password,
        ]);

        if (isset($response['error'])) {
            return back()->withErrors(['password' => $response['error_description'] ?? 'Erreur lors de la réinitialisation.']);
        }

        return redirect()->route('loginView')->with('status', 'Mot de passe réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }

}
