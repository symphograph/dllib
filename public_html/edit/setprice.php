<?php
die();
if(empty($_POST['sendprice']))
exit();

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$User = new User;
$User->check();
$user_id = $User->id;

$setprise = PriceValidator([$_POST['setgold'],$_POST['setsilver'],$_POST['setbronze']]);
//var_dump($setprise);
if(!$setprise)
	die();
$item_id = $_POST['item_id'] ?? 0;
$item_id = intval($item_id);
if($item_id == 0) die;
qwe("REPLACE INTO `prices` 
(`user_id`, `item_id`, `auc_price`, `server_group`,`time`)
VALUES 
('$User->id', '$item_id', '$setprise', '$User->server_group', now())");

qwe("DELETE FROM `user_crafts` 
WHERE `user_id` = '$User->id' 
AND `isbest` <2");

qwe("UPDATE `user_crafts` SET `auc_price` = '$setprise'  
WHERE `user_id` = '$User->id' 
AND `item_id` = '$item_id' 
AND `auc_price` > 1");



	echo '<meta http-equiv="refresh" content="0; url=/catalog.php">';	
	?>
