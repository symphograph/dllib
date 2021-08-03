<?php

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$item_id = $_POST['item_id'] ?? $_GET['item_id'] ?? 0;
$item_id = intval($item_id);
if(!$item_id) exit();

$isbuy = $_POST['isbuy'] ?? $_GET['isbuy'] ?? 0;
$isbuy = intval($isbuy);
if(!$isbuy) exit();


$User = new User;
if(!$User->byIdenty())
	die('<span style="color: red">Oh!<span>');

if($isbuy == 3)
{
    $Item = new Item();
    $Item->byId($item_id);
    $Item->initPrice();
    $try = $Item->setAsBuy();
    if($try != 'ok'){
        die($try);
    }
}

if($isbuy == 1)
{
	qwe("
	DELETE FROM `user_crafts`
	WHERE `user_id` = '$User->id' 
	AND (`item_id` = '$item_id' OR `isbest` < 2)
	");

	qwe("DELETE FROM user_buys
    WHERE user_id = '$User->id'
    AND item_id = '$item_id'
    ");
	echo 'ok';
}
qwe("UPDATE `user_crafts` SET `craft_price` = NULL WHERE `user_id` = '$User->id'");