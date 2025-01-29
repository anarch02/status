<?php
$subdomain = "mirjalol"; 
$client_id = "f3f0ec2f-a7ef-491b-8209-eac76698a0d7";
$client_secret = "Z4qre63K9GlbZ3cJ3R3gUrkkvbUrGMl0uPoAzg9thJ0HYKhuFF5ImtR1Xj2Zv51e";
$redirect_uri = "https://dd34-192-166-229-78.ngrok-free.app/oauth_callback.php";
$tokens_file = "tokens.json";

// Читаем refresh_token
$tokens = json_decode(file_get_contents($tokens_file), true);
if (!$tokens || !isset($tokens["refresh_token"])) {
    die("Ошибка: нет refresh_token. Пройди авторизацию заново.");
}

$data = [
    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "grant_type" => "refresh_token",
    "refresh_token" => $tokens["refresh_token"],
    "redirect_uri" => $redirect_uri
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://{$subdomain}.amocrm.ru/oauth2/access_token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

$response = json_decode(curl_exec($ch), true);
curl_close($ch);

if (!isset($response["access_token"])) {
    die("Ошибка обновления токена: " . print_r($response, true));
}

// Сохраняем новые токены
file_put_contents($tokens_file, json_encode($response, JSON_PRETTY_PRINT));
echo "Токен успешно обновлён!";
?>
