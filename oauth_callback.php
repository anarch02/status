<?php
// Настройки AmoCRM
$subdomain = "mirjalol"; // Например, example.amocrm.ru
$client_id = "f3f0ec2f-a7ef-491b-8209-eac76698a0d7";
$client_secret = "Z4qre63K9GlbZ3cJ3R3gUrkkvbUrGMl0uPoAzg9thJ0HYKhuFF5ImtR1Xj2Zv51e";
$redirect_uri = "https://dd34-192-166-229-78.ngrok-free.app/oauth_callback.php"; // Ссылка для редиректа
$tokens_file = "tokens.json"; // Файл хранения токенов

// Функция для выполнения HTTP-запросов
function makeRequest($url, $method = "POST", $data = null) {
    $curl = curl_init();
    $headers = ["Content-Type: application/json"];

    if ($method !== "GET" && $data) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers
    ]);

    $response = curl_exec($curl);
    curl_close($curl);
    
    return json_decode($response, true);
}

$auth_code = 'def50200abcf5d93127f2ece259adc748082ac8b85b19b7b264e1ff2962f330b5ae87d9dd69ec44d3ed59ad86d7085511e1937cacbe7f8899e648cd91368a35e54df8aa5c4a9c714e8b09c7c7ac11aebac6a63f7c5a3dbde0192fde699b9b338a87fb618fe45ec5898e3fc6aaedf8075bc6b5804b93151771b278a42393e4236274a9c4225daa30a2f643cc35451b8922b0c2d2b6a18e8a49686ba20e49b451c99ac3ff00e39205dd2cc81a5d7550d242da36cd5dfbd2513ae9f2b867b0c99d3ef742801c0462b61c6de2adca6f72457eed2302fe4170dfe4db807ab186dd8c368f23630020c8c292c19a13879e9e08bec29dae8ca47d1c2b6091ea5d83eba38081148da537bfcf923810fd617e9f8671dc7f2fa2011c96020864c6432f4c7a5a131cb30b5f5c4f03623b34a809418cd841702aad484facd65be4cfdd127f3ed793636ba8b42200f2a5dc4e79261dd790110eb2d79ca35ede7a40356a9f882d50024ded1e578d4cfb924fdc43119ac92b54fd6941b3d9b86f644e4c6cbc2edb0846320daf9fb18073a7550b61735f64dcef7b0da992e084111ef524c33637d5156e9ee72fe800e9e80b045fd6c0d53ab1e4b9d766cb8998bc6c818829507ff8200ed18ca8311ae8519240848c42f4ba30365cd51821436dd519019aff105580029da360f82e9efad5ce54dbe8f1e1f0a292e3c3a7d3f34e7b46377f7cadb3b0722a1bf641d9d5a3981b73eaed03a9f';

// Отправляем запрос на получение токенов
$data = [
    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "grant_type" => "authorization_code",
    "code" => $auth_code,
    "redirect_uri" => $redirect_uri
];

$response = makeRequest("https://{$subdomain}.amocrm.ru/oauth2/access_token", "POST", $data);

// Проверяем успешное получение токенов
if (!isset($response["access_token"])) {
    die("Ошибка получения токена.");
}

// Сохраняем токены в файл
file_put_contents($tokens_file, json_encode($response, JSON_PRETTY_PRINT));

echo "Токен успешно получен и сохранен!";
?>
