<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

use Supabase\Storage\StorageClient;

class SupabaseStorageService
{
    protected $url;
    protected $key;
    protected $bucket;

    public function __construct()
    {
        $this->url = env('SUPABASE_URL');
        $this->key = env('SUPABASE_SERVICE_ROLE_KEY');
        $this->bucket = env('SUPABASE_BUCKET');
    }

    public function upload($filePath, $fileContent)
    {
        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key,
        ])->attach('file', $fileContent, basename($filePath))
          ->post("{$this->url}/storage/v1/object/{$this->bucket}/{$filePath}");

        if ($response->successful()) {
            return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$filePath}";
        }

        throw new \Exception('Upload failed: ' . $response->body());
    }
}