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
        // Vérifier d'abord si le fichier existe
        if ($this->fileExists($filePath)) {
            return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$filePath}";
        }

        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key,
        ])->attach('file', $fileContent, basename($filePath))
          ->post("{$this->url}/storage/v1/object/{$this->bucket}/{$filePath}");

        if ($response->successful()) {
            return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$filePath}";
        }

        // Gérer le cas où le fichier existe déjà (au cas où la vérification précédente a échoué)
        if ($response->status() === 409) {
            return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$filePath}";
        }

        throw new \Exception('Upload failed: ' . $response->body());
    }

    /**
     * Vérifier si un fichier existe dans le bucket Supabase
     */
    public function fileExists($filePath)
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->key,
                'Authorization' => 'Bearer ' . $this->key,
            ])->get("{$this->url}/storage/v1/object/{$this->bucket}/{$filePath}");

            if ($response->status() === 404) {
                return false;
            }

            if ($response->serverError()) {
                \Log::warning('Supabase Storage fileExists server error', [
                    'file' => $filePath,
                    'status' => $response->status(),
                ]);
                throw new \RuntimeException("Supabase Storage returned status {$response->status()} for file check");
            }

            return $response->successful();
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Supabase Storage fileExists network error', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Unable to check file existence in Supabase Storage: ' . $e->getMessage(), 0, $e);
        }
    }
}