<?php
include_once '../includs/config.php';
$tok = '664dc5a7b51ed6f412ba1f3dd7e2bed09bc7553e939cd7db7b31db3969478f10bbdd616af6990ea752428';
        $wall = file_get_contents("https://api.vk.com/method/board.getComments?v=5.59&group_id=74540255&topic_id=30531594&extended=1&sort=desc&count=100&access_token=".$tok);
 
    $wall = json_decode($wall);
    $wall = $wall->response->items;

   for ($i = 0; $i < count($wall); $i++) {
    $exp = explode(' ',$wall[$i]->text,2);
   $nick=preg_replace('/[^\p{L}0-9 ]/ui','',$exp[0]);
   $id = $wall[$i]->from_id;
   //echo $nick;
       // echo "<p><b>".($i + 1)."</b>. <i>".$wall[$i]->from_id."</i><br />".$wall[$i]->text."<span>".'<br>'.date("Y-m-d H:i:s", $wall[$i]->date)."</span></p>"; // Выводим записи
   
	$query = qwe("UPDATE tracker_users SET nick ='".$nick."' WHERE uservid ='vk-".$id."'");
    }
    ?>

