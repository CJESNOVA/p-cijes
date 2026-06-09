<?php
// Check curl extension
$hasCurl = function_exists('curl_version');
echo "curl_installed:" . ($hasCurl ? '1' : '0') . PHP_EOL;

// Simple HTTP GET to API host
$url = 'https://testolympia.cjes.africa/';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$body = curl_exec($ch);
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "http_code:" . $code . PHP_EOL;
if ($err) echo "curl_error:" . $err . PHP_EOL;
else echo "body_preview:" . substr($body, 0, 200) . PHP_EOL;
