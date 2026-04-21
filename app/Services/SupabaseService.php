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
        return Http::withHeaders($this->headers($useServiceRole))
            ->get("{$this->baseUrl}/{$table}", $query)
            ->json();
    }

    public function insert($table, $data, $useServiceRole = true)
    {
        return Http::withHeaders($this->headers($useServiceRole))
            ->post("{$this->baseUrl}/{$table}", $data)
            ->json();
    }

    public function update($table, $id, $data, $useServiceRole = true)
    {
        return Http::withHeaders($this->headers($useServiceRole))
            ->patch("{$this->baseUrl}/{$table}?id=eq.{$id}", $data)
            ->json();
    }

    public function delete($table, $id, $useServiceRole = true)
    {
        return Http::withHeaders($this->headers($useServiceRole))
            ->delete("{$this->baseUrl}/{$table}?id=eq.{$id}")
            ->json();
    }

    /** ===== Authentification ===== **/
    public function login($email, $password)
    {
        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->authUrl}/token?grant_type=password", [
            'email' => $email,
            'password' => $password,
        ]);

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

    $response = Http::withHeaders([
        'apikey' => $serviceKey,
        'Authorization' => 'Bearer ' . $serviceKey,
        'Content-Type' => 'application/json',
    ])->post($url, $payload);

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

    $response = Http::withHeaders([
        'apikey' => $apiKey,
        'Content-Type' => 'application/json',
    ])->post($url, $payload);

/*dd([
    'url' => $url,
    'headers' => [
        'apikey' => $apiKey,
        'Content-Type' => 'application/json'
    ],
    'payload' => $payload,
    'response_status' => $response->status(),
    'response_body' => $response->body()
]);*/
    return $response->json();
}


    public function updateUser(string $accessToken, array $data)
    {
        $baseUrl = rtrim(env('SUPABASE_URL'), '/');
        $apiKey = env('SUPABASE_API_KEY');

        $url = $baseUrl . '/auth/v1/user';

        $response = Http::withHeaders([
            'apikey' => $apiKey,
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->put($url, $data);

        return $response->json();
    }

}