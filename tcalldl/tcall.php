<?php 

if (!isset($_REQUEST)) { 
  return; 
} 

//Строка для подтверждения адреса сервера из настроек Callback API 
$confirmation_token = 'f847e3f3'; 

//Ключ доступа сообщества 
$token = '556e662655470af9274bba112b2b75159feba0f213a2b0c927980bfba687125480fb0db7e8b890abcce6f'; 

//Получаем и декодируем уведомление 
$data = json_decode(file_get_contents('php://input')); 

//Проверяем, что находится в поле "type" 
switch ($data->type) { 
  //Если это уведомление для подтверждения адреса сервера... 
  case 'confirmation': 
    //...отправляем строку для подтверждения адреса 
    echo $confirmation_token; 
    break; 

//Если это уведомление о новом сообщении... 
  case 'board_post_new': 
    //...получаем id его автора 
    $user_id = $data->object->user_id; 
    //затем с помощью users.get получаем данные об авторе 
    $user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&v=5.59")); 

//и извлекаем из ответа его имя 
    $user_name = $user_info->response[0]->first_name;
	$user =  '1234';
	require_once '../includs/config.php';
     
//С помощью messages.send и токена сообщества отправляем ответное сообщение 
    $request_params = array(
      'message' => "Hello, {$user_name}!", 
      'user_id' => $user_id, 
      'access_token' => $token, 
      'v' => '5.59' 
    ); 

$get_params = http_build_query($request_params); 

file_get_contents('https://api.vk.com/method/messages.send?'. $get_params); 
//mysqli_query($dbLink."INSERT INTO `get_vk_test` (`name`) VALUES ('$user')");
//Возвращаем "ok" серверу Callback API 
    echo('ok'); 

break; 
} 
?>