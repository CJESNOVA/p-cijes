<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected $baseUrl;
    protected $authUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('SUPABASE_URL'), '/') . '/rest/v1';
        $this->authUrl = rtrim(env('SUPABASE_URL'), '/') . '/auth/v1';
        $this->apiKey = env('SUPABASE_API_KEY');
        $this->roleKey = env('SUPABASE_SERVICE_ROLE_KEY');
    }

    private function headers($useServiceRole = true)
    {
        $key = $useServiceRole ? $this->roleKey : $this->apiKey;
        
        return [
            'apikey' => $key,
            'Authorization' => 'Bearer ' . $key,
            'Content-Type' => 'application/json',
        ];
    }

    /** ===== CRUD sur les tables ===== **/
    public function get($table, $query = [], $useServiceRole = true)
    {
        $response = Http::withHeaders($this->headers($useServiceRole))
            ->get("{$this->baseUrl}/{$table}", $query);

        if ($response->failed()) {
            \Log::error('Supabase GET failed', [
                'table' => $table,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException("Supabase GET {$table} failed with status {$response->status()}");
        }

        return $response->json();
    }

    public function insert($table, $data, $useServiceRole = true)
    {
        $response = Http::withHeaders($this->headers($useServiceRole))
            ->post("{$this->baseUrl}/{$table}", $data);

        if ($response->failed()) {
            \Log::error('Supabase INSERT failed', [
                'table' => $table,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException("Supabase INSERT into {$table} failed with status {$response->status()}");
        }

        return $response->json();
    }

    public function update($table, $id, $data, $useServiceRole = true)
    {
        $response = Http::withHeaders($this->headers($useServiceRole))
            ->patch("{$this->baseUrl}/{$table}?id=eq.{$id}", $data);

        if ($response->failed()) {
            \Log::error('Supabase UPDATE failed', [
                'table' => $table,
                'id' => $id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException("Supabase UPDATE {$table} (id={$id}) failed with status {$response->status()}");
        }

        return $response->json();
    }

    public function delete($table, $id, $useServiceRole = true)
    {
        $response = Http::withHeaders($this->headers($useServiceRole))
            ->delete("{$this->baseUrl}/{$table}?id=eq.{$id}");

        if ($response->failed()) {
            \Log::error('Supabase DELETE failed', [
                'table' => $table,
                'id' => $id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException("Supabase DELETE {$table} (id={$id}) failed with status {$response->status()}");
        }

        return $response->json();
    }

    /** ===== Authentification ===== **/
    public function login($email, $password)
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->authUrl}/token?grant_type=password", [
                'email' => $email,
                'password' => $password,
            ]);
        } catch (\Exception $e) {
            \Log::error('Supabase login network error', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return ['error' => 'network_error', 'error_description' => 'Impossible de contacter le service d\'authentification.'];
        }

        if ($response->serverError()) {
            \Log::error('Supabase login server error', [
                'email' => $email,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return ['error' => 'server_error', 'error_description' => 'Le service d\'authentification est temporairement indisponible.'];
        }

        return $response->json();
    }

    // --- AUTH ---
    /*public function signUp($email, $password, $data = [])
    {
        $url = env('SUPABASE_URL') . '/auth/v1/signup';
        $apiKey = env('SUPABASE_API_KEY');

        $response = Http::withHeaders([
            'apikey' => $apiKey,
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'email' => $email,
            'password' => $password,
            'data' => $data, // metadata, ex: full_name, role, etc.
        ]);

        return $response->json();
    }*/

        
    public function signUp($email, $password, $data = [])
{
    // Utiliser l'endpoint admin avec SERVICE_ROLE_KEY pour contourner l'email
    $url = rtrim(env('SUPABASE_URL'), '/') . '/auth/v1/admin/users';
    $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY');

    $payload = [
        'email' => $email,
        'password' => $password,
        'email_confirm' => true, // Forcer la confirmation immédiate
        'user_metadata' => $data,
    ];

    try {
        $response = Http::withHeaders([
            'apikey' => $serviceKey,
            'Authorization' => 'Bearer ' . $serviceKey,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);
    } catch (\Exception $e) {
        \Log::error('Supabase signUp network error', [
            'email' => $email,
            'error' => $e->getMessage(),
        ]);
        return ['error' => 'network_error', 'error_description' => 'Impossible de contacter le service d\'inscription.'];
    }

    if ($response->serverError()) {
        \Log::error('Supabase signUp server error', [
            'email' => $email,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
        return ['error' => 'server_error', 'error_description' => 'Le service d\'inscription est temporairement indisponible.'];
    }

    return $response->json();
}

/*public function signUp2($email, $password, $data = [], $redirectTo = null)
    {
        $url = rtrim(env('SUPABASE_URL'), '/') . '/auth/v1/signup';
        $apiKey = env('SUPABASE_API_KEY');

        $payload = [
            'email'    => $email,
            'password' => $password,
            'data'     => $data,
        ];

        // 🔗 Si on veut que le lien dans l'email de confirmation renvoie vers notre app
        if ($redirectTo) {
            $payload['redirect_to'] = $redirectTo;
        }

        $response = Http::withHeaders([
            'apikey'        => $apiKey,
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->post($url, $payload);

        if ($response->failed()) {
            \Log::error('Erreur Supabase Signup', [
                'body' => $response->body(),
                'status' => $response->status(),
            ]);
        }

        return $response->json();
    }*/

    

    public function signIn(string $email, string $password)
    {
        $fullUrl = "{$this->authUrl}/token?grant_type=password";
        
        // Debug temporaire
        \Log::info('Supabase SignIn Debug', [
            'supabase_url' => env('SUPABASE_URL'),
            'auth_url' => $this->authUrl,
            'full_url' => $fullUrl,
            'email' => $email
        ]);
        
        try {
            return Http::withHeaders($this->headers())
                ->post($fullUrl, [
                    'email'    => $email,
                    'password' => $password,
                ])
                ->json();
        } catch (\Exception $e) {
            \Log::error('Supabase SignIn Error', [
                'error' => $e->getMessage(),
                'full_url' => $fullUrl,
                'auth_url' => $this->authUrl
            ]);
            throw $e;
        }
    }


    public function resetPasswordForEmail(string $email, array $options = [])
{
    $baseUrl = rtrim(env('SUPABASE_URL'), '/');
    $apiKey = env('SUPABASE_API_KEY');
    
    $url = $baseUrl . '/auth/v1/recover';

    $payload = array_merge([
        'email' => $email,
    ], $options);

    try {
        $response = Http::withHeaders([
            'apikey' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);
    } catch (\Exception $e) {
        \Log::error('Supabase resetPasswordForEmail network error', [
            'email' => $email,
            'error' => $e->getMessage(),
        ]);
        return ['error' => 'network_error', 'error_description' => 'Impossible de contacter le service de réinitialisation.'];
    }

    if ($response->failed()) {
        \Log::error('Supabase resetPasswordForEmail failed', [
            'email' => $email,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
    }

    return $response->json();
}


    public function updateUser(string $accessToken, array $data)
    {
        $baseUrl = rtrim(env('SUPABASE_URL'), '/');
        $apiKey = env('SUPABASE_API_KEY');

        $url = $baseUrl . '/auth/v1/user';

        try {
            $response = Http::withHeaders([
                'apikey' => $apiKey,
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->put($url, $data);
        } catch (\Exception $e) {
            \Log::error('Supabase updateUser network error', [
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Impossible de mettre à jour l\'utilisateur: ' . $e->getMessage(), 0, $e);
        }

        if ($response->failed()) {
            \Log::error('Supabase updateUser failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException("Supabase updateUser failed with status {$response->status()}");
        }

        return $response->json();
    }

}