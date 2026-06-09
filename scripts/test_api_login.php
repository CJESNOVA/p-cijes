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
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$response = curl_exec($ch);
if ($response === false) {
    echo "CURL_ERROR:" . curl_error($ch) . PHP_EOL;
    exit(1);
}
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
curl_close($ch);

echo "HTTP_CODE:" . $httpCode . PHP_EOL;
echo "HEADERS:\n" . $headers . PHP_EOL;
echo "BODY:\n" . $body . PHP_EOL;
