<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\Cotisationtype;

echo "<h2>Types de cotisations disponibles</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Titre</th><th>Montant</th><th>État</th></tr>";

$types = Cotisationtype::all();
foreach($types as $type) {
    echo "<tr>";
    echo "<td>" . $type->id . "</td>";
    echo "<td>" . htmlspecialchars($type->titre) . "</td>";
    echo "<td>" . number_format($type->montant, 2) . "</td>";
    echo "<td>" . ($type->etat ? 'Actif' : 'Inactif') . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
