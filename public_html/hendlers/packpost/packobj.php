<?php
if(!isset($_POST['item_id'])) die();

$item_id = intval($_POST['item_id']);
if(!$item_id) die();

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User;
$User->check();
$user_id = $User->id;
if(!isset($user_id) or !$user_id)
    die();

qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/cat-funcs.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/funct-obhod2.php';
$Item = new Item();
$Item->getFromDB($item_id);
$Item->RecountBestCraft();
qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");

$po = PackObject($item_id);
$craft_price = $po['craft_price'];
$po['esyprice'] = esyprice($craft_price);

$po = json_encode($po);
echo $po;