<?php
$item_id = $_POST['item_id'] ?? 0;
$item_id = intval($item_id);
if(!$item_id) die();

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$User = new User;
$User->check();
$user_id = $User->id;
if(!isset($user_id) or !$user_id)
    die();

qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");

require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/funct-obhod2.php';
$Item = new Item();
$Item->byId($item_id);
$Item->RecountBestCraft();
qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");

$po = PackObject($item_id);
$po->esyprice = esyprice($po->craft_price);

$po = json_encode($po);
echo $po;