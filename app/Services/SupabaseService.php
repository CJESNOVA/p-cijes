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

    private function headers()
    {
        return [
            'apikey' => $this->apiKey,
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    /** ===== CRUD sur les tables ===== **/
    public function get($table, $query = [])
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/{$table}", $query)
            ->json();
    }

    public function insert($table, $data)
    {
        return Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/{$table}", $data)
            ->json();
    }

    public function update($table, $id, $data)
    {
        return Http::withHeaders($this->headers())
            ->patch("{$this->baseUrl}/{$table}?id=eq.{$id}", $data)
            ->json();
    }

    public function delete($table, $id)
    {
        return Http::withHeaders($this->headers())
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
    $url = rtrim(env('SUPABASE_URL'), '/') . '/auth/v1/signup';
    $apiKey = env('SUPABASE_API_KEY');

    $payload = [
        'email' => $email,
        'password' => $password,
        'data' => $data,
        'email_confirm' => true, // ✅ Comme dans ton test cURL
    ];

    $response = Http::withHeaders([
        'apikey' => $apiKey,
        'Authorization' => 'Bearer ' . $apiKey,
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
        return Http::withHeaders($this->headers())
            ->post("{$this->authUrl}/token?grant_type=password", [
                'email'    => $email,
                'password' => $password,
            ])
            ->json();
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
