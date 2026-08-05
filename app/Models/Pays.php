<?php

namespace App\Models;

use App\Services\SupabaseService;

class Pays
{
    protected $supabase;
    protected $table = 'countries'; // Table distante Supabase

    public function __construct()
    {
        $this->supabase = app(SupabaseService::class);
    }

    /**
     * Récupérer tous les pays
     */
    public function all()
    {
        $response = $this->supabase->get($this->table);

        // En cas d'erreur, Supabase renvoie un objet (ex: {"code":..., "message":...})
        // au lieu d'une liste de pays : on l'ignore pour ne pas casser les vues qui
        // itèrent sur le résultat en attendant des objets pays.
        if (!is_array($response) || !array_is_list($response)) {
            \Log::error("Réponse Supabase invalide pour la table {$this->table}", ['response' => $response]);
            return [];
        }

        return json_decode(json_encode($response));
    }

    /**
     * Récupérer un pays par ID
     */
    public function find($id)
    {
        $data = $this->supabase->get($this->table, ['id' => "eq.$id"]);
        return count($data) ? (object) $data[0] : null;
    }

    /**
     * Créer un pays
     */
    public function create(array $attributes)
    {
        return $this->supabase->insert($this->table, [$attributes]);
    }

    /**
     * Mettre à jour un pays
     */
    public function update($id, array $attributes)
    {
        return $this->supabase->update($this->table, $id, $attributes);
    }

    /**
     * Supprimer un pays
     */
    public function delete($id)
    {
        return $this->supabase->delete($this->table, $id);
    }



}
