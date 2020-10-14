<?php
$item_id = $_POST['item_id'] ?? $_GET['item_id'] ?? 0;
$item_id = intval($item_id);
if($item_id == 0)
	die();

require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functs.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';

$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die();
extract($userinfo_arr);
$user_id = $muser;

$sql = "
DELETE FROM `user_crafts` 
WHERE `user_id` = '$user_id' 
AND `item_id` = '$item_id'
";
qwe($sql);

$sql="DELETE FROM `user_crafts` WHERE `user_id` = '$user_id' and `isbest` <2";
qwe($sql);
echo 'ok';
?>