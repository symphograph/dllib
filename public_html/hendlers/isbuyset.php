<?php

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$item_id = $_POST['item_id'] ?? $_GET['item_id'] ?? 0;
$item_id = intval($item_id);
if($item_id == 0) exit();

$isbuy = $_POST['isbuy'] ?? $_GET['isbuy'] ?? 0;
$isbuy = intval($isbuy);
if($isbuy == 0) exit();


$User = new User;
if(!$User->byIdenty())
	die('<span style="color: red">Oh!<span>');

$user_id = $User->id;

if($isbuy == 3)
{
	$auc_price = PriceMode($item_id,$User->id)['auc_price'] ?? false;
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
	('$item_id','$user_id','$auc_price','$User->server_group',NOW())
	");

    qwe("
        REPLACE INTO user_buys
        (user_id, item_id)
        values 
        ('$user_id', '$item_id')
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

	qwe("DELETE FROM user_buys
    WHERE user_id = '$user_id'
    AND item_id = '$item_id'
    ");
	echo 'ok';
}