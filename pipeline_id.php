<?php
// Загружаем токены
$tokens_file = "tokens.json";
$tokens = json_decode(file_get_contents($tokens_file), true);
$access_token = $tokens["access_token"];
$subdomain = "mirjalol"; // Например, example

// Функция запроса
function makeRequest($url, $method = "GET", $token = null) {
    $curl = curl_init();
    $headers = [
        "Authorization: Bearer $token",
        "Content-Type: application/json",
    ];

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

// Запрашиваем список воронок
$url = "https://{$subdomain}.amocrm.ru/api/v4/leads/pipelines";
$response = makeRequest($url, "GET", $access_token);

// Проверяем ответ
if (!isset($response["_embedded"]["pipelines"])) {
    die("Ошибка: Не удалось получить воронки.");
}

// Выводим список воронок
foreach ($response["_embedded"]["pipelines"] as $pipeline) {
    echo "ID: " . $pipeline["id"] . " - Название: " . $pipeline["name"] . "<br>";
}
?>
