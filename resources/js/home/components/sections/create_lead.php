<?php
$data = $_POST;

$queryUrl = 'https://infinitys.bitrix24.kz/rest/94132/bwv77lob7c5jof7a/';

$queryData = http_build_query([
 'is_need_callback' => '0', // Для автоматического использования обратного звонка при отправке контакта и сделки нужно поменять 0 на 1
  'fields' => [
    'TITLE' => "Jobtron.org - " . $data['name'],
		'NAME' => $data['name'],
      'PHONE' => [
          "n0" => [
              "VALUE" => $data['phone'],
              "VALUE_TYPE" => "WORK",
          ],
    ],
]);

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_POST => 1,
  CURLOPT_HEADER => 0,
  CURLOPT_RETURNTRANSFER => 1,
  CURLOPT_URL => $queryUrl,
  CURLOPT_POSTFIELDS => $queryData,
));
$result = curl_exec($curl);
curl_close($curl);
$result = json_decode($result, 1);
if (array_key_exists('error', $result)) {
  echo "Ошибка при сохранении лида: ".$result['error_description']."<br/>";
} else {
  echo '<script>
  location.href= "https://job.bpartners.kz/new_site/dist/index.html";
</script>';
}


?>
