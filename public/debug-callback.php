<?php
// Script de debug pour tester le callback SEMOA
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Callback SEMOA</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-btn { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; margin: 5px; }
        .result { margin: 10px 0; padding: 10px; background: #f8f9fa; border: 1px solid #ddd; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <h1>Test Callback SEMOA</h1>
    
    <h2>Test 1: Vérifier si l'endpoint API est accessible</h2>
    <button class="test-btn" onclick="testApiCallback()">Tester API Callback</button>
    <div id="api-result"></div>
    
    <h2>Test 2: Simuler un callback SEMOA</h2>
    <button class="test-btn" onclick="simulateCallback()">Simuler Callback</button>
    <div id="simulate-result"></div>
    
    <h2>Test 3: Vérifier les logs</h2>
    <button class="test-btn" onclick="checkLogs()">Vérifier Logs</button>
    <div id="logs-result"></div>

    <script>
        function testApiCallback() {
            fetch('/api/callback/ressourcecompte/999', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    state: 'success',
                    received_amount: 1000
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('api-result').innerHTML = 
                    '<div class="result success">✅ API Callback accessible: ' + JSON.stringify(data) + '</div>';
            })
            .catch(error => {
                document.getElementById('api-result').innerHTML = 
                    '<div class="result error">❌ Erreur API Callback: ' + error.message + '</div>';
            });
        }
        
        function simulateCallback() {
            fetch('/test-callback.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    state: 'paid',
                    transaction_id: 'test-123',
                    amount: 5000
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('simulate-result').innerHTML = 
                    '<div class="result success">✅ Callback simulé: ' + JSON.stringify(data) + '</div>';
            })
            .catch(error => {
                document.getElementById('simulate-result').innerHTML = 
                    '<div class="result error">❌ Erreur simulation: ' + error.message + '</div>';
            });
        }
        
        function checkLogs() {
            fetch('/debug-callback.php?action=logs')
            .then(response => response.text())
            .then(data => {
                document.getElementById('logs-result').innerHTML = 
                    '<div class="result"><pre>' + data + '</pre></div>';
            })
            .catch(error => {
                document.getElementById('logs-result').innerHTML = 
                    '<div class="result error">❌ Erreur logs: ' + error.message + '</div>';
            });
        }
        
        // Auto-check logs on load
        window.onload = function() {
            checkLogs();
        };
    </script>
</body>
</html>
