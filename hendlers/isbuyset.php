<?php

require_once '../includs/ip.php';
$item_id = $_POST['item_id'] ?? $_GET['item_id'] ?? 0;
$item_id = intval($item_id);
if($item_id == 0) exit();

$isbuy = $_POST['isbuy'] ?? $_GET['isbuy'] ?? 0;
$isbuy = intval($isbuy);
if($isbuy == 0) exit();

require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/functions/functs.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';

$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die('<span style="color: red">Oh!<span>');
extract($userinfo_arr);
$user_id = $muser;
//$item_id = ResultItemId($craft_id);
if($isbuy == 3)
{
	$auc_price = PriceMode($item_id,$user_id)['auc_price'] ?? false;
	if(!$auc_price) die('no_price');
	qwe("
	UPDATE `user_crafts`
	SET `isbest` = 3,
	auc_price = '$auc_price'
	WHERE `item_id` = '$item_id'
	AND `user_id` = '$user_id'
	AND `isbest` > 0
	");
	
	qwe("
	DELETE FROM `user_crafts`
	WHERE `user_id` = '$user_id' 
	AND `isbest` < 2
	");
	
	qwe("
	INSERT IGNORE INTO `prices`
	(`item_id`,`user_id`,`auc_price`,`server_group`,`time`)
	VALUES
	('$item_id','$user_id','$auc_price','$server_group',NOW())
	");
	echo 'ok';
}
if($isbuy == 1)
{
	//$auc_price = PriceMode($item_id,$user_id)['auc_price'] ?? false;
	qwe("
	DELETE FROM `user_crafts`
	WHERE `user_id` = '$user_id' 
	AND (`item_id` = '$item_id' OR `isbest` < 2)
	");
	echo 'ok';
}	
?>