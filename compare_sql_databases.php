<?php

echo "=== COMPARAISON DES BASES DE DONNÉES LOCAL VS PRODUCTION ===\n\n";

$localFile = __DIR__ . '/local.sql';
$productionFile = __DIR__ . '/production.sql';

if (!file_exists($localFile) || !file_exists($productionFile)) {
    echo "❌ Fichiers SQL introuvables\n";
    exit;
}

echo "Analyse des fichiers SQL...\n";
echo "Local : " . number_format(filesize($localFile)) . " octets\n";
echo "Production : " . number_format(filesize($productionFile)) . " octets\n\n";

// Parser les deux fichiers
function parseSQLFile($file) {
    $content = file_get_contents($file);
    $tables = [];
    
    // Extraire les CREATE TABLE
    preg_match_all('/CREATE TABLE `([^`]+)` \((.*?)\)\s*ENGINE/s', $content, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $tableName = $match[1];
        $tableDef = $match[2];
        
        // Extraire les colonnes
        $columns = [];
        preg_match_all('/`([^`]+)`\s+([^,\n]+)/', $tableDef, $colMatches, PREG_SET_ORDER);
        
        foreach ($colMatches as $colMatch) {
            $colName = $colMatch[1];
            $colDef = trim($colMatch[2]);
            $columns[$colName] = $colDef;
        }
        
        $tables[$tableName] = $columns;
    }
    
    return $tables;
}

$localTables = parseSQLFile($localFile);
$productionTables = parseSQLFile($productionFile);

echo "📊 STATISTIQUES :\n";
echo "Local : " . count($localTables) . " tables\n";
echo "Production : " . count($productionTables) . " tables\n\n";

// Tables importantes à comparer
$importantTables = [
    'membres',
    'users', 
    'recompenses',
    'alertes',
    'actions',
    'ressourcecomptes',
    'ressourcetransactions',
    'ressourcetypes'
];

echo "🔍 COMPARAISON DES TABLES IMPORTANTES :\n";
echo str_repeat("=", 80) . "\n";

foreach ($importantTables as $tableName) {
    echo "\n📋 TABLE : {$tableName}\n";
    echo str_repeat("-", 40) . "\n";
    
    $localExists = isset($localTables[$tableName]);
    $prodExists = isset($productionTables[$tableName]);
    
    if ($localExists && $prodExists) {
        echo "✅ Existe dans les deux bases\n";
        
        $localCols = $localTables[$tableName];
        $prodCols = $productionTables[$tableName];
        
        $missingInLocal = array_diff_key($prodCols, $localCols);
        $missingInProd = array_diff_key($localCols, $prodCols);
        
        if (!empty($missingInLocal)) {
            echo "❌ Manquantes en local :\n";
            foreach ($missingInLocal as $col => $def) {
                echo "   - {$col}: {$def}\n";
            }
        }
        
        if (!empty($missingInProd)) {
            echo "❌ Manquantes en production :\n";
            foreach ($missingInProd as $col => $def) {
                echo "   - {$col}: {$def}\n";
            }
        }
        
        if (empty($missingInLocal) && empty($missingInProd)) {
            echo "✅ Structures identiques\n";
        }
        
    } elseif ($localExists) {
        echo "❌ Existe en local SEULEMENT\n";
    } elseif ($prodExists) {
        echo "❌ Existe en production SEULEMENT\n";
    } else {
        echo "❌ N'existe dans AUCUNE base\n";
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "📊 RÉSUMÉ DES DIFFÉRENCES :\n\n";

// Toutes les tables
$allTables = array_unique(array_merge(array_keys($localTables), array_keys($productionTables)));

$onlyInLocal = array_diff_key($localTables, $productionTables);
$onlyInProd = array_diff_key($productionTables, $localTables);

if (!empty($onlyInLocal)) {
    echo "📂 Tables SEULEMENT en local (" . count($onlyInLocal) . "):\n";
    foreach ($onlyInLocal as $table => $cols) {
        echo "   - {$table} (" . count($cols) . " colonnes)\n";
    }
    echo "\n";
}

if (!empty($onlyInProd)) {
    echo "📂 Tables SEULEMENT en production (" . count($onlyInProd) . "):\n";
    foreach ($onlyInProd as $table => $cols) {
        echo "   - {$table} (" . count($cols) . " colonnes)\n";
    }
    echo "\n";
}

echo "🎯 RECOMMANDATIONS :\n";
echo "1. Vérifiez les différences de colonnes dans les tables importantes\n";
echo "2. Assurez-vous que les colonnes manquantes sont ajoutées\n";
echo "3. Faites attention aux colonnes qui pourraient causer des erreurs\n";
