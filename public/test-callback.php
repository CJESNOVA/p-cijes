<?php
// Test endpoint pour vérifier si l'API callback est accessible
header('Content-Type: application/json');

// Log de test
file_put_contents(
    __DIR__ . '/../storage/logs/test-callback.log', 
    date('Y-m-d H:i:s') . " - Test callback reçu\n" . 
    json_encode($_REQUEST) . "\n" . 
    json_encode(file_get_contents('php://input')) . "\n\n",
    FILE_APPEND
);

echo json_encode([
    'status' => 'ok',
    'message' => 'Callback test endpoint accessible',
    'timestamp' => date('Y-m-d H:i:s'),
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'request_uri' => $_SERVER['REQUEST_URI'],
    'post_data' => $_POST,
    'raw_input' => file_get_contents('php://input')
]);
?>
