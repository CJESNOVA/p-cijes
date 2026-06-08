<?php

echo "=== COMPARAISON DÉTAILLÉE DES COLONNES ===\n\n";

$localFile = __DIR__ . '/local.sql';
$productionFile = __DIR__ . '/production.sql';

function parseSQLFile($file) {
    $content = file_get_contents($file);
    $tables = [];
    
    preg_match_all('/CREATE TABLE `([^`]+)` \((.*?)\)\s*ENGINE/s', $content, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $tableName = $match[1];
        $tableDef = $match[2];
        
        $columns = [];
        preg_match_all('/`([^`]+)`\s+([^,\n)]+)([^,\n]*)/', $tableDef, $colMatches, PREG_SET_ORDER);
        
        foreach ($colMatches as $colMatch) {
            $colName = $colMatch[1];
            $colType = trim($colMatch[2]);
            $extra = trim($colMatch[3]);
            $fullDef = $colType . ' ' . $extra;
            $columns[$colName] = trim($fullDef);
        }
        
        $tables[$tableName] = $columns;
    }
    
    return $tables;
}

$localTables = parseSQLFile($localFile);
$productionTables = parseSQLFile($productionFile);

// Tables importantes pour les récompenses
$rewardTables = [
    'membres',
    'users', 
    'recompenses',
    'alertes',
    'actions',
    'ressourcecomptes',
    'ressourcetransactions',
    'ressourcetypes',
    'notifications',
    'jobs'
];

echo "🎯 ANALYSE DÉTAILLÉE DES TABLES DE RÉCOMPENSES :\n";
echo str_repeat("=", 80) . "\n\n";

foreach ($rewardTables as $tableName) {
    echo "📋 TABLE : `{$tableName}`\n";
    echo str_repeat("-", 60) . "\n";
    
    $localExists = isset($localTables[$tableName]);
    $prodExists = isset($productionTables[$tableName]);
    
    if ($localExists && $prodExists) {
        $localCols = $localTables[$tableName];
        $prodCols = $productionTables[$tableName];
        
        $allCols = array_unique(array_merge(array_keys($localCols), array_keys($prodCols)));
        
        echo str_pad("COLONNE", 30) . str_pad("LOCAL", 40) . str_pad("PRODUCTION", 40) . "DIFF\n";
        echo str_repeat("-", 110) . "\n";
        
        foreach ($allCols as $col) {
            $localDef = $localCols[$col] ?? 'MANQUANTE';
            $prodDef = $prodCols[$col] ?? 'MANQUANTE';
            
            $diff = ($localDef !== $prodDef) ? '❌' : '✅';
            
            echo str_pad($col, 30) . str_pad(substr($localDef, 0, 37), 40) . str_pad(substr($prodDef, 0, 37), 40) . $diff . "\n";
        }
        
    } elseif ($localExists) {
        echo "❌ EXISTE SEULEMENT EN LOCAL\n";
        foreach ($localTables[$tableName] as $col => $def) {
            echo "   - {$col}: {$def}\n";
        }
    } elseif ($prodExists) {
        echo "❌ EXISTE SEULEMENT EN PRODUCTION\n";
        foreach ($productionTables[$tableName] as $col => $def) {
            echo "   - {$col}: {$def}\n";
        }
    } else {
        echo "❌ N'EXISTE DANS AUCUNE BASE\n";
    }
    
    echo "\n";
}

echo str_repeat("=", 80) . "\n";
echo "🔍 ANALYSE SPÉCIFIQUE POUR LE PROBLÈME DE RÉCOMPENSES :\n\n";

echo "1. Vérification de la table 'ressourcetransactions' :\n";
if (isset($localTables['ressourcetransactions']) && isset($productionTables['ressourcetransactions'])) {
    $localTrans = $localTables['ressourcetransactions'];
    $prodTrans = $productionTables['ressourcetransactions'];
    
    if (isset($localTrans['description']) && !isset($prodTrans['description'])) {
        echo "❌ La colonne 'description' existe en local mais PAS en production !\n";
        echo "   C'est la cause de l'erreur SQL !\n";
    } elseif (!isset($localTrans['description']) && isset($prodTrans['description'])) {
        echo "❌ La colonne 'description' existe en production mais PAS en local !\n";
    } elseif (isset($localTrans['description']) && isset($prodTrans['description'])) {
        echo "✅ La colonne 'description' existe dans les deux bases\n";
    } else {
        echo "❌ La colonne 'description' n'existe dans AUCUNE base\n";
    }
}

echo "\n2. Tables manquantes critiques :\n";
$criticalTables = ['notifications', 'jobs'];
foreach ($criticalTables as $table) {
    $localHas = isset($localTables[$table]);
    $prodHas = isset($productionTables[$table]);
    
    if (!$localHas || !$prodHas) {
        echo "❌ Table '{$table}' : Local=" . ($localHas ? '✅' : '❌') . " Production=" . ($prodHas ? '✅' : '❌') . "\n";
    }
}

echo "\n3. Colonnes potentiellement problématiques :\n";
$problematicCols = [];
foreach ($rewardTables as $tableName) {
    if (isset($localTables[$tableName]) && isset($productionTables[$tableName])) {
        $missingInProd = array_diff_key($localTables[$tableName], $productionTables[$tableName]);
        $missingInLocal = array_diff_key($productionTables[$tableName], $localTables[$tableName]);
        
        if (!empty($missingInProd)) {
            $problematicCols[] = "Table {$tableName}: manquantes en production - " . implode(', ', array_keys($missingInProd));
        }
        if (!empty($missingInLocal)) {
            $problematicCols[] = "Table {$tableName}: manquantes en local - " . implode(', ', array_keys($missingInLocal));
        }
    }
}

if (!empty($problematicCols)) {
    foreach ($problematicCols as $issue) {
        echo "❌ {$issue}\n";
    }
} else {
    echo "✅ Aucune différence critique trouvée dans les tables de récompenses\n";
}
