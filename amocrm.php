<?php
// Настройки AmoCRM
$subdomain = "mirjalol"; // Например, example.amocrm.ru
$client_id = "f3f0ec2f-a7ef-491b-8209-eac76698a0d7";
$client_secret = "Z4qre63K9GlbZ3cJ3R3gUrkkvbUrGMl0uPoAzg9thJ0HYKhuFF5ImtR1Xj2Zv51e";
$redirect_uri = "https://dd34-192-166-229-78.ngrok-free.app/oauth_callback.php"; // Ссылка для редиректа
$tokens_file = "tokens.json"; // Файл для хранения токенов

// ID воронки и стадии (предположим, что у вас уже есть воронка с ID 123 и стадия с ID 456)
$pipeline_id = 123; // Замените на свой ID воронки
$status_id = 456;  // Замените на свой ID стадии

// Функция для загрузки токенов из файла
function getTokens() {
    global $tokens_file;
    if (!file_exists($tokens_file)) return null;
    return json_decode(file_get_contents($tokens_file), true);
}

// Функция для сохранения токенов
function saveTokens($tokens) {
    global $tokens_file;
    file_put_contents($tokens_file, json_encode($tokens, JSON_PRETTY_PRINT));
}

// Функция для обновления токена
function refreshToken() {
    global $subdomain, $client_id, $client_secret, $redirect_uri;

    $tokens = getTokens();
    if (!$tokens || !isset($tokens["refresh_token"])) {
        die("Ошибка: нет refresh_token, требуется повторная авторизация.");
    }

    $data = [
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "grant_type" => "refresh_token",
        "refresh_token" => $tokens["refresh_token"],
        "redirect_uri" => $redirect_uri
    ];

    // Логирование запроса на обновление токена
    echo "Запрос на обновление токена: ";
    print_r($data);

    $response = makeRequest("https://{$subdomain}.amocrm.ru/oauth2/access_token", "POST", $data);

    // Логирование ответа
    echo "Ответ на запрос обновления токена: ";
    print_r($response);

    if (!isset($response["access_token"])) {
        die("Ошибка обновления токена.");
    }

    saveTokens($response);
    return $response["access_token"];
}

// Функция для выполнения запросов к AmoCRM
function makeRequest($url, $method = "GET", $data = null) {
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

// Функция для отправки контакта в AmoCRM
function sendContact($name, $phone) {
    global $subdomain, $pipeline_id, $status_id;

    // Получаем новый токен при каждом запросе
    $access_token = refreshToken();  // Обновляем токен перед отправкой

    // Данные для AmoCRM
    $data = [
        [
            "name" => $name,
            "custom_fields_values" => [
                [
                    "field_code" => "PHONE",
                    "values" => [["value" => $phone]]
                ]
            ],
            "notes" => [
                ["text" => "Контакт из сайта elt-status.ge"]
            ],
            // Добавляем контакт на воронку
            "pipeline_id" => $pipeline_id,
            "status_id" => $status_id
        ]
    ];

    // Логирование данных для отправки
    echo "Данные для отправки в AmoCRM: ";
    print_r($data);

    $url = "https://{$subdomain}.amocrm.ru/api/v4/contacts";
    $headers = [
        "Authorization: Bearer {$access_token}",
        "Content-Type: application/json"
    ];

    // Выполнение запроса
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);
    
    $response = curl_exec($curl);
    curl_close($curl);

    // Логирование ответа от AmoCRM
    echo "Ответ от AmoCRM при добавлении контакта: ";
    print_r($response);

    $response_data = json_decode($response, true);

    if (isset($response_data["_embedded"]["contacts"][0]["id"])) {
        return "Контакт успешно добавлен в AmoCRM и помещен в воронку!";
    } else {
        return "Ошибка добавления контакта.";
    }
}

// Обработка формы
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ?? "Без имени";
    $phone = $_POST["phone"] ?? "Не указан";

    echo sendContact($name, $phone);
}
?>
