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
use App\Notifications\WelcomeNotification;
use App\Notifications\EmailVerifiedNotification;
use App\Notifications\PasswordResetNotification;
use App\Notifications\PasswordResetConfirmationNotification;

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
        'password' => [
            'required',
            'string',
            'confirmed',
            'min:8',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[@$!%*?&]/',
        ],
    ], [
        'password.required' => 'Le mot de passe est obligatoire.',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        'password.regex' => 'Le mot de passe doit contenir au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial (@$!%*?&).',
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

        // 📧 Envoyer l'email de bienvenue directement avec Laravel
        try {
            $user->notify(new WelcomeNotification($user->name));
        } catch (\Exception $e) {
            // Continue même si l'email échoue
            \Log::warning('Email de bienvenue non envoyé: ' . $e->getMessage());
        }

        // ✅ Rediriger vers la page de confirmation email
        return redirect()->route('emails.verify')
            ->with('status', 'Un e-mail de confirmation vous a été envoyé. Veuillez vérifier votre boîte de réception.');

        //return redirect()->intended(route('dashboard'));
    }

    // ❌ En cas d'échec, analyser l'erreur Supabase
    if (isset($response['error'])) {
        $errorMessage = $response['error_description'] ?? $response['error'] ?? 'Erreur inconnue';
        
        // Messages d'erreur personnalisés selon le type d'erreur
        if (strpos(strtolower($errorMessage), 'user_already_exists') !== false || 
            strpos(strtolower($errorMessage), 'already registered') !== false ||
            strpos(strtolower($errorMessage), 'duplicate') !== false) {
            return back()->withErrors([
                'email' => 'Cette adresse email est déjà utilisée. Veuillez vous connecter ou utiliser une autre adresse email.'
            ]);
        }
        
        if (strpos(strtolower($errorMessage), 'invalid_email') !== false) {
            return back()->withErrors([
                'email' => 'L\'adresse email n\'est pas valide.'
            ]);
        }
        
        if (strpos(strtolower($errorMessage), 'weak_password') !== false) {
            return back()->withErrors([
                'password' => 'Le mot de passe est trop faible. Veuillez choisir un mot de passe plus sécurisé.'
            ]);
        }
        
        // Message d'erreur générique mais plus informatif
        return back()->withErrors([
            'email' => 'Une erreur est survenue lors de la création du compte: ' . $errorMessage
        ]);
    }

    // ❌ En cas d'échec sans message d'erreur spécifique
    return back()->withErrors([
        'email' => 'Impossible de créer le compte. Veuillez vérifier vos informations et réessayer.'
    ]);
}

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

    // 📧 Envoyer l'email de réinitialisation directement avec Laravel
    $user = User::where('email', $request->email)->first();
    
    if ($user) {
        try {
            // Supprimer les anciens tokens
            \DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            // Générer un token sécurisé
            $resetToken = bin2hex(random_bytes(32));
            
            // Stocker le token
            \DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $resetToken,
                'created_at' => now(),
            ]);
            
            // Envoyer la notification
            $user->notify(new PasswordResetNotification($resetToken, $user->name));
        } catch (\Exception $e) {
            // Continue même si l'email échoue
            \Log::warning('Email de réinitialisation non envoyé: ' . $e->getMessage());
        }
    }

    return back()->with('status', 'Un lien de réinitialisation a été envoyé à votre adresse e-mail.');
}


    // --- Étape 3 : Vue "Nouveau mot de passe"
    public function resetPasswordView(Request $request)
    {
        // Vérifier le token
        $token = $request->query('token');
        
        if (!$token) {
            return redirect()->route('loginView')->withErrors(['email' => 'Lien invalide ou expiré.']);
        }
        
        // Vérifier si le token existe et n'est pas trop vieux (60 minutes)
        $resetToken = \DB::table('password_reset_tokens')
            ->where('token', $token)
            ->where('created_at', '>', now()->subMinutes(60))
            ->first();
            
        if (!$resetToken) {
            return redirect()->route('loginView')->withErrors(['email' => 'Lien invalide ou expiré.']);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $resetToken->email]);
    }

    // --- Étape 4 : Traitement du nouveau mot de passe
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial (@$!%*?&).',
        ]);

        // Vérifier le token
        $resetToken = \DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->where('created_at', '>', now()->subMinutes(60))
            ->first();
            
        if (!$resetToken) {
            return back()->withErrors(['email' => 'Lien invalide ou expiré.']);
        }

        // 📧 Envoyer la confirmation de réinitialisation directement avec Laravel
        try {
            $user = User::where('email', $request->email)->first();
            
            if ($user) {
                // Mettre à jour le mot de passe localement
                $user->password = Hash::make($request->password);
                $user->save();
                
                // Supprimer le token utilisé
                \DB::table('password_reset_tokens')->where('token', $request->token)->delete();
                
                // Envoyer la confirmation
                $user->notify(new PasswordResetConfirmationNotification($user->name));
            }
        } catch (\Exception $e) {
            // Continue même si l'email échoue
            \Log::warning('Email de confirmation de réinitialisation non envoyé: ' . $e->getMessage());
        }

        return redirect()->route('loginView')->with('status', 'Mot de passe réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }

    // --- Étape 5 : Confirmation d'email
    public function emailVerified(Request $request)
    {
        // Vérifier si l'utilisateur est connecté
        if (Auth::check()) {
            $user = Auth::user();
            
            // 📧 Envoyer l'email de confirmation directement avec Laravel
            try {
                $user->notify(new EmailVerifiedNotification($user->name));
            } catch (\Exception $e) {
                // Continue même si l'email échoue
                \Log::warning('Email de confirmation non envoyé: ' . $e->getMessage());
            }
        }
        
        return view('auth.verify-success');
    }

}
