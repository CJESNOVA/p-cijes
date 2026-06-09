<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CrudController extends Controller
{
    /**
     * Validate that a table name contains only safe characters.
     */
    private function isValidTableName(string $tableName): bool
    {
        return (bool) preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName);
    }

    /**
     * Liste toutes les tables disponibles
     */
    public function index()
    {
        try {
            // Récupérer toutes les tables (sauf les tables système)
            $tables = DB::select('SHOW TABLES');
            $tableData = [];
            
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                
                // Ignorer les tables système et migrations
                if (in_array($tableName, ['migrations', 'password_reset_tokens', 'failed_jobs', 'admins'])) {
                    continue;
                }
                
                // Récupérer le nombre d'enregistrements
                $count = DB::table($tableName)->count();
                
                // Récupérer les colonnes
                $columns = Schema::getColumnListing($tableName);
                
                // Déterminer le type de table
                $type = $this->getTableType($tableName);
                $color = $this->getTableColor($type);
                
                $tableData[] = [
                    'name' => $tableName,
                    'row_count' => $count,
                    'columns' => $columns,
                    'type' => $type,
                    'color' => $color
                ];
            }
            
            // Trier par nom
            usort($tableData, function($a, $b) {
                return strcasecmp($a['name'], $b['name']);
            });
            
        } catch (\Exception $e) {
            $tableData = [];
            session('error', 'Erreur lors de la récupération des tables: ' . $e->getMessage());
        }
        
        return view('admin.crud.index', compact('tableData'));
    }
    
    /**
     * Affiche les données d'une table spécifique
     */
    public function showTable(Request $request, $tableName)
    {
        try {
            if (!$this->isValidTableName($tableName)) {
                return redirect()->route('admin.crud.index')->with('error', 'Nom de table invalide.');
            }

            // Vérifier si la table existe
            if (!Schema::hasTable($tableName)) {
                return redirect()->route('admin.crud.index')->with('error', "La table '$tableName' n'existe pas.");
            }
            
            // Récupérer les colonnes
            $columns = Schema::getColumnListing($tableName);
            
            // Construire la requête avec recherche
            $query = DB::table($tableName);
            
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($columns, $searchTerm) {
                    foreach ($columns as $column) {
                        if (!in_array($column, ['password', 'remember_token'])) {
                            $q->orWhere($column, 'LIKE', "%{$searchTerm}%");
                        }
                    }
                });
            }
            
            // Pagination
            $perPage = $request->get('per_page', 10);
            $data = $query->orderBy('id', 'desc')->paginate($perPage);
            
        } catch (\Exception $e) {
            return redirect()->route('admin.crud.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
        
        // Préparer les options pour les clés étrangères (pour l'affichage)
        $foreignKeyOptions = [];
        
        // Debug: Vérifier le contenu de $columns
        if (!is_array($columns)) {
            \Log::error('$columns n\'est pas un tableau: ' . gettype($columns));
            $columns = $this->getTableColumnsInfo($tableName);
        }
        
        foreach ($columns as $column => $info) {
            if (!is_array($info)) {
                \Log::error('$info n\'est pas un tableau pour la colonne ' . $column . ': ' . gettype($info));
                continue;
            }
            
            if (str_contains($info['type'], 'bigint')) {
                $options = $this->getForeignKeyOptions($tableName, $column);
                if ($options) {
                    $foreignKeyOptions[$column] = $options;
                }
            }
        }

        return view('admin.crud.table', compact('tableName', 'columns', 'data', 'foreignKeyOptions'));
    }
    
    /**
     * Affiche les détails d'un enregistrement
     */
    public function show($tableName, $id)
    {
        try {
            if (!$this->isValidTableName($tableName) || !Schema::hasTable($tableName)) {
                return redirect()->route('admin.crud.index')->with('error', "La table '$tableName' n'existe pas.");
            }
            
            $data = DB::table($tableName)->where('id', $id)->first();
            if (!$data) {
                return redirect()->route('admin.crud.table', $tableName)->with('error', 'Enregistrement introuvable.');
            }
            
            $columns = $this->getTableColumnsInfo($tableName);
            
            // Préparer les options pour les clés étrangères (pour l'affichage)
            $foreignKeyOptions = [];
            
            // Debug: Vérifier le contenu de $columns
            if (!is_array($columns)) {
                \Log::error('$columns n\'est pas un tableau dans show: ' . gettype($columns));
                $columns = $this->getTableColumnsInfo($tableName);
            }
            
            foreach ($columns as $column => $info) {
                if (!is_array($info)) {
                    \Log::error('$info n\'est pas un tableau dans show pour la colonne ' . $column . ': ' . gettype($info));
                    continue;
                }
                
                if (str_contains($info['type'], 'bigint')) {
                    $options = $this->getForeignKeyOptions($tableName, $column);
                    if ($options) {
                        $foreignKeyOptions[$column] = $options;
                    }
                }
            }

            return view('admin.crud.show', compact('tableName', 'data', 'columns', 'foreignKeyOptions'));
            
        } catch (\Exception $e) {
            return redirect()->route('admin.crud.table', $tableName)->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche le formulaire de création
     */
    public function create($tableName)
    {
        try {
            if (!$this->isValidTableName($tableName) || !Schema::hasTable($tableName)) {
                return redirect()->route('admin.crud.index')->with('error', "La table '$tableName' n'existe pas.");
            }
            
            $columns = $this->getTableColumnsInfo($tableName);
            $isEdit = false;
            
        } catch (\Exception $e) {
            return redirect()->route('admin.crud.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
        
        // Préparer les options pour les clés étrangères
        $foreignKeyOptions = [];
        
        // Debug: Vérifier le contenu de $columns
        if (!is_array($columns)) {
            \Log::error('$columns n\'est pas un tableau dans create: ' . gettype($columns));
            $columns = $this->getTableColumnsInfo($tableName);
        }
        
        foreach ($columns as $column => $info) {
            if (!is_array($info)) {
                \Log::error('$info n\'est pas un tableau dans create pour la colonne ' . $column . ': ' . gettype($info));
                continue;
            }
            
            if (str_contains($info['type'], 'bigint')) {
                $options = $this->getForeignKeyOptions($tableName, $column);
                if ($options) {
                    $foreignKeyOptions[$column] = $options;
                }
            }
        }

        return view('admin.crud.form', compact('tableName', 'columns', 'foreignKeyOptions', 'isEdit'));
    }
    
    /**
     * Affiche le formulaire d'édition
     */
    public function edit($tableName, $id)
    {
        try {
            if (!$this->isValidTableName($tableName) || !Schema::hasTable($tableName)) {
                return redirect()->route('admin.crud.index')->with('error', "La table '$tableName' n'existe pas.");
            }
            
            $data = DB::table($tableName)->find($id);
            if (!$data) {
                return redirect()->route('admin.crud.table', $tableName)->with('error', "Enregistrement introuvable.");
            }
            
            $columns = $this->getTableColumnsInfo($tableName);
            $isEdit = true;
            
        } catch (\Exception $e) {
            return redirect()->route('admin.crud.table', $tableName)->with('error', 'Erreur: ' . $e->getMessage());
        }
        
        // Préparer les options pour les clés étrangères
        $foreignKeyOptions = [];
        
        // Debug: Vérifier le contenu de $columns
        if (!is_array($columns)) {
            \Log::error('$columns n\'est pas un tableau dans edit: ' . gettype($columns));
            $columns = $this->getTableColumnsInfo($tableName);
        }
        
        foreach ($columns as $column => $info) {
            if (!is_array($info)) {
                \Log::error('$info n\'est pas un tableau dans edit pour la colonne ' . $column . ': ' . gettype($info));
                continue;
            }
            
            if (str_contains($info['type'], 'bigint')) {
                $options = $this->getForeignKeyOptions($tableName, $column);
                if ($options) {
                    $foreignKeyOptions[$column] = $options;
                }
            }
        }

        return view('admin.crud.form', compact('tableName', 'columns', 'data', 'foreignKeyOptions', 'isEdit'));
    }
    
    /**
     * Enregistre une nouvelle donnée
     */
    public function store(Request $request, $tableName)
    {
        try {
            if (!$this->isValidTableName($tableName) || !Schema::hasTable($tableName)) {
                return redirect()->route('admin.crud.index')->with('error', "La table '$tableName' n'existe pas.");
            }
            
            $columns = $this->getTableColumnsInfo($tableName);
            $data = [];
            
            foreach ($columns as $column => $info) {
                if (!in_array($column, ['id', 'created_at', 'updated_at', 'password', 'remember_token']) && !str_contains($info['type'], 'timestamp') && !str_contains($info['type'], 'tinyint(1)')) {
                    $value = $request->input($column);
                    
                    // Gérer les champs vides pour les colonnes nullable
                    if ($value === null || $value === '') {
                        if ($info['nullable'] === 'YES') {
                            $data[$column] = null;
                        } else {
                            return redirect()->back()
                                ->withInput()
                                ->with('error', "Le champ '$column' est obligatoire.");
                        }
                    } else {
                        $data[$column] = $value;
                    }
                }
            }
            
            // Ajouter les timestamps si la table les a
            if (Schema::hasColumn($tableName, 'created_at') && Schema::hasColumn($tableName, 'updated_at')) {
                $data['created_at'] = now();
                $data['updated_at'] = now();
            }
            
            // Automatisation des tinyint(1) - booléens
            foreach ($columns as $column => $info) {
                if (str_contains($info['type'], 'tinyint(1)')) {
                    if (str_contains($column, 'etat')) {
                        $data[$column] = 1; // état = 1
                    } else {
                        $data[$column] = 0; // autres = 0
                    }
                }
            }
            
            $id = DB::table($tableName)->insertGetId($data);
            
            return redirect()->route('admin.crud.table', $tableName)
                ->with('success', "Enregistrement créé avec succès (ID: $id)");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }
    
    /**
     * Met à jour une donnée
     */
    public function update(Request $request, $tableName, $id)
    {
        try {
            if (!$this->isValidTableName($tableName) || !Schema::hasTable($tableName)) {
                return redirect()->route('admin.crud.index')->with('error', "La table '$tableName' n'existe pas.");
            }
            
            $columns = $this->getTableColumnsInfo($tableName);
            $data = [];
            
            foreach ($columns as $column => $info) {
                if (!in_array($column, ['id', 'created_at', 'password', 'remember_token']) && !str_contains($info['type'], 'timestamp') && !str_contains($info['type'], 'tinyint(1)')) {
                    $value = $request->input($column);
                    
                    // Ne pas mettre à jour si le champ est vide et nullable
                    if ($value !== null && $value !== '') {
                        $data[$column] = $value;
                    } elseif ($info['nullable'] === 'NO') {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', "Le champ '$column' est obligatoire.");
                    }
                }
            }
            
            // Ajouter updated_at si la table l'a
            if (Schema::hasColumn($tableName, 'updated_at')) {
                $data['updated_at'] = now();
            }
            
            DB::table($tableName)->where('id', $id)->update($data);
            
            return redirect()->route('admin.crud.table', $tableName)
                ->with('success', "Enregistrement mis à jour avec succès");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }
    
    /**
     * Supprime une donnée
     */
    public function destroy($tableName, $id)
    {
        try {
            if (!$this->isValidTableName($tableName) || !Schema::hasTable($tableName)) {
                return redirect()->route('admin.crud.index')->with('error', "La table '$tableName' n'existe pas.");
            }
            
            DB::table($tableName)->where('id', $id)->delete();
            
            return redirect()->route('admin.crud.table', $tableName)
                ->with('success', "Enregistrement supprimé avec succès");
                
        } catch (\Exception $e) {
            return redirect()->route('admin.crud.table', $tableName)
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
    
    /**
     * Récupère les informations détaillées des colonnes
     */
    private function getTableColumnsInfo($tableName)
    {
        $columns = [];
        $columnInfo = DB::select("DESCRIBE `" . str_replace('`', '``', $tableName) . "`");
        
        foreach ($columnInfo as $info) {
            $columns[$info->Field] = [
                'type' => $info->Type,
                'nullable' => $info->Null,
                'key' => $info->Key,
                'default' => $info->Default
            ];
        }
        
        return $columns;
    }
    
    /**
     * Récupère les options pour les clés étrangères
     */
    private function getForeignKeyOptions($tableName, $columnName)
    {
        try {
            // Récupérer les contraintes de clé étrangère
            $constraints = DB::select("
                SELECT 
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND COLUMN_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$tableName, $columnName]);

            if (empty($constraints)) {
                return null;
            }

            $constraint = $constraints[0];
            $referencedTable = $constraint->REFERENCED_TABLE_NAME;
            $referencedColumn = $constraint->REFERENCED_COLUMN_NAME;

            // Récupérer le premier champ texte de la table référencée pour l'affichage
            $displayColumn = $this->getDisplayColumn($referencedTable);

            // Récupérer les options
            $options = DB::table($referencedTable)
                ->select($referencedColumn . ' as id', $displayColumn . ' as display')
                ->orderBy($displayColumn)
                ->get();

            return [
                'table' => $referencedTable,
                'id_column' => $referencedColumn,
                'display_column' => $displayColumn,
                'options' => $options
            ];

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Détermine le meilleur champ d'affichage pour une table
     */
    private function getDisplayColumn($tableName)
    {
        $columns = $this->getTableColumnsInfo($tableName);
        
        // Ordre de priorité pour les champs d'affichage
        $priorityColumns = ['nom', 'name', 'titre', 'title', 'libelle', 'label', 'designation', 'denomination'];
        
        foreach ($priorityColumns as $col) {
            if (isset($columns[$col])) {
                return $col;
            }
        }
        
        // Si aucun des champs prioritaires, prendre le premier varchar/text
        foreach ($columns as $col => $info) {
            if (str_contains($info['type'], 'varchar') || str_contains($info['type'], 'char')) {
                return $col;
            }
        }
        
        // Fallback sur le premier champ non-ID
        foreach ($columns as $col => $info) {
            if ($col !== 'id' && !str_contains($info['type'], 'timestamp') && !str_contains($info['type'], 'tinyint(1)')) {
                return $col;
            }
        }
        
        return 'id'; // Dernier recours
    }
    
    /**
     * Toggle les champs tinyint(1) (booléens)
     */
    public function toggleBoolean($tableName, $id, $column)
    {
        try {
            if (!$this->isValidTableName($tableName) || !Schema::hasTable($tableName)) {
                return response()->json(['error' => "La table '$tableName' n'existe pas."], 404);
            }
            
            $columns = $this->getTableColumnsInfo($tableName);
            
            if (!isset($columns[$column]) || !str_contains($columns[$column]['type'], 'tinyint(1)')) {
                return response()->json(['error' => "Le champ '$column' n'est pas un booléen."], 400);
            }
            
            $record = DB::table($tableName)->where('id', $id)->first();
            if (!$record) {
                return response()->json(['error' => "Enregistrement non trouvé."], 404);
            }
            
            $newValue = $record->{$column} == 1 ? 0 : 1;
            
            DB::table($tableName)->where('id', $id)->update([
                $column => $newValue,
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'new_value' => $newValue,
                'message' => "Le champ '$column' a été mis à jour."
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Détermine le type de table
     */
    private function getTableType($tableName)
    {
        if (str_contains($tableName, 'user') || str_contains($tableName, 'membre') || str_contains($tableName, 'admin')) {
            return 'users';
        } elseif (str_contains($tableName, 'migration') || str_contains($tableName, 'job') || str_contains($tableName, 'cache')) {
            return 'system';
        } else {
            return 'business';
        }
    }
    
    /**
     * Détermine la couleur pour le type de table
     */
    private function getTableColor($type)
    {
        return match($type) {
            'users' => '#e74c3c',
            'business' => '#27ae60',
            'system' => '#95a5a6',
            default => '#3498db'
        };
    }
}
