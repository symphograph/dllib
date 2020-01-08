<?php
//var_dump($_POST);
$item_id = $_POST['item_id'] ?? $_GET['item_id'] ?? 0;
$item_id = intval($item_id);
if($item_id == 0)
	die();

$reports = ['<span style="color: red">ой!<span>','ок'];


	
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functs.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';




$userinfo_arr = UserInfo();

$report = 1;
if(!$userinfo_arr)
	die($reports[0]);
extract($userinfo_arr);
$user_id = $muser;

if(!empty($_POST['del']) and $_POST['del'] == 'del')
{
	$qwe = qwe("
	DELETE FROM `prices`
	WHERE `user_id` = '$user_id'
	AND `item_id` = '$item_id'
	AND `server_group` = '$server_group';
	");
	if(!$qwe)
		$report = 0;
}else
{
	$setprise = PriceValidator([$_POST['setgold'],$_POST['setsilver'],$_POST['setbronze']]);
	//var_dump($setprise);
	if(!$setprise)
		die($reports[0]);

	$query = qwe("REPLACE INTO `prices` 
	(`user_id`, `item_id`, `auc_price`, `server_group`,`time`)
	VALUES 
	('$user_id', '$item_id', '$setprise', '$server_group', now())");
	if(!$query)
		$report = 0;
}
$sql="DELETE FROM `user_crafts` WHERE `user_id` = '$user_id' and `isbest` <2";
qwe($sql);
echo $reports[$report];
?>
