<?php
if(!isset($_POST['item_id'])) die();

$item_id = intval($_POST['item_id']);
if(!$item_id) die();

require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';
if(!isset($user_id) or !$user_id)
    die();

$prof_q = qwe("SELECT * FROM `user_profs` WHERE `user_id` = '$user_id'");
qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/cat-funcs.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/../functions/funct-obhod2.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/recurs.php';
qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");

$po = PackObject($item_id);
$craft_price = $po['craft_price'];
$po['esyprice'] = esyprice($craft_price);

$po = json_encode($po);
echo $po;
?>