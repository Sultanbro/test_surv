<?php
  // формируем URL, на который будем отправлять запрос в битрикс24
  $queryURL = "https://infinitys.bitrix24.kz/rest/1/t6jous7hbiy1dnlb/crm.lead.add.json";

  //собираем данные из формы
  $sName = htmlspecialchars($_POST["NAME"]);
  $sPhone = htmlspecialchars($_POST["PHONE"]);

	// формируем параметры для создания лида
	$queryData = http_build_query(array(
		"fields" => array(
			"TITLE" => "jobtron.org - перезвоните мне " .$sName,
			"NAME" => $sName,	// имя
			"PHONE" => $sPhone, // телефон
		),
		'params' => array("REGISTER_SONET_EVENT" => "Y")	// Y = произвести регистрацию события добавления лида в живой ленте. Дополнительно будет отправлено уведомление ответственному за лид.
	));

	// отправляем запрос в Б24 и обрабатываем ответ
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POST => 1,
		CURLOPT_HEADER => 0,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $queryURL,
		CURLOPT_POSTFIELDS => $queryData,
	));
	$result = curl_exec($curl);
	curl_close($curl);
	$result = json_decode($result,1);

	echo "Мы перезвоним Вам в ближайшее время";

	// если произошла какая-то ошибка - выведем её
	if(array_key_exists('error', $result)){
		die("Ошибка при сохранении лида: ".$result['error_description']);
	}
?>

