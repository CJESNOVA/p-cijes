<?php
$url = 'https://testolympia.cjes.africa/api/v1/auth/login';
$data = [
    'email' => 'yokamly@gmail.com',
    'password' => 'Yokamly@123'
];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$body = curl_exec($ch);
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$out = [
    'http_code' => $code,
    'error' => $err,
    'body' => $body,
];
file_put_contents(__DIR__ . '/login_output.json', json_encode($out, JSON_PRETTY_PRINT));
echo "WROTE:" . __DIR__ . '/login_output.json' . PHP_EOL;
