<?php
if(empty($_POST['prices'])){
	die('no data');
}
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}

$User = new User;
if(!$User->byIdenty())
	die('user');
$responce = 'ok';
foreach ($_POST['prices'] as $k => $p){
	$p = (object) $p;
	$item_id = intval($p->item_id);
	$price = intval($p->sum);
	if(!$price or $price < 0){
		//$responce = 'havingnull';
		continue;
	}


	$qwe = qwe("REPLACE INTO `prices` 
	(`user_id`, `item_id`, `auc_price`, `server_group`,`time`)
	VALUES 
	('$User->id', '$item_id', '$price', '$User->server_group', now())");

	if(!$qwe){
		die('dbError');
	}
}

qwe("DELETE FROM `user_crafts` WHERE `user_id` = '$User->id' and `isbest` <2");
echo json_encode(['ok']);
?>
