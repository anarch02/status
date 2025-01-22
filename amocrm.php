<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

<?php

require __DIR__ . '/vendor/autoload.php';
// include_once __DIR__ . '/unsorted/accept.php';

if(isset($_POST['phone'])) {

  try {
    
      // Создание клиента
      $subdomain = '';            // Поддомен в амо срм
      $login     = '';            // Логин в амо срм
      $apikey    = '';            // api ключ


      $amo = new \AmoCRM\Client($subdomain, $login, $apikey);

        // Вывести полученые из амо данные
        // echo '<pre>';
        // print_r($amo->account->apiCurrent());
        // echo '</pre>';

        // создаем лида
        $lead = $amo->lead;
        $lead['name'] = $_POST['product_name'];
        // $lead['responsible_user_id'] = 2462338; // ID ответсвенного 
        // $lead['pipeline_id'] = 1207249; // ID воронки

        // $lead->addCustomField(305117, [ // ID  поля в которое будт приходить заявки
        //     [$_POST['city']], // сюда вписать значение из атрибута "name" пример: <input name="phone">
        // ]);

        $id = $lead->apiAdd();

      // Получение экземпляра модели для работы с контактами
      $contact = $amo->contact;

      // Заполнение полей модели
      $contact['name'] = isset($_POST['name']) ? $_POST['name'] : 'Не указано';
      $contact['linked_leads_id'] = [(int)$id];


      // Добавление нового контакта и получение его ID
      $id = $contact->apiAdd();


  } catch (\AmoCRM\Exception $e) {
      printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
  }

}

?>


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ваша заявка успешно отправлена</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'IBM Plex Sans', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 20px;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="title">
            <br><span style="font-size:33px;font-weight:500;">Спасибо!</span><br><br>
            Ваша заявка успешно отправлена.<br>

            <?php if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) { ?>
                <br><br><a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" style="text-decoration: none; border-bottom: 1px dotted">Вернуться назад</a>
             <?php } ?>
        </div>
    </div>
</div>

</body>
</html>
