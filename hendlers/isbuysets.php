<?php

require_once '../includs/ip.php';

$vals = ['false','true'];
$val = $_POST['value'] ?? 0;

if(!in_array($val,$vals)) 
	die('val');
	
$val = array_search($val,$vals);

$item_id = $_POST['item_id'] ?? 0;
$item_id = intval($item_id);
if($item_id == 0) exit('id');


require_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/functions/functs.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';

$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die('<span style="color: red">Oh!<span>');
extract($userinfo_arr);
$user_id = $muser;

if($val)
{
	$auc_price = PriceMode($item_id,$user_id)['auc_price'] ?? false;
	if(!$auc_price) die('<span style="color:red">Не нашел цену!</span>');
	
	qwe("REPLACE INTO `user_crafts`
	(`user_id`, `craft_id`, `item_id`, `isbest`)
	VALUES
	('$user_id',(SELECT `craft_id` FROM `crafts` WHERE result_item_id = '$item_id' LIMIT 1), '$item_id', 3)
	");
	/*
	qwe("
	UPDATE `user_crafts`
	SET `isbest` = 3,
	auc_price = '$auc_price'
	WHERE `item_id` = '$item_id'
	AND `user_id` = '$user_id'
	");
	*/
	qwe("
	DELETE FROM `user_crafts`
	WHERE `user_id` = '$user_id' 
	AND `isbest` < 2
	");
	
	qwe("
	REPLACE INTO `prices`
	(`item_id`,`user_id`,`auc_price`,`server_group`,`time`)
	VALUES
	('$item_id','$user_id','$auc_price','$server_group',NOW())
	");
	echo 'Добавлено';
}
else
{
	//$auc_price = PriceMode($item_id,$user_id)['auc_price'] ?? false;
	qwe("
	DELETE FROM `user_crafts`
	WHERE `user_id` = '$user_id' 
	AND (`item_id` = '$item_id' OR `isbest` < 2)
	");
	echo 'Удалено';
}	
?>