<?php
var_dump($_POST); die;
include '../includs/ip.php';
$craft_id = $_POST['craft_id'] ?? $_GET['craft_id'] ?? 0;
$craft_id = intval($craft_id);
if($craft_id == 0) exit();


include_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functs.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';

$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die('<span style="color: red">Oh!<span>');
extract($userinfo_arr);
$user_id = $muser;
$item_id = ResultItemId($craft_id);


qwe("UPDATE `user_crafts`
SET `isbest` = 2
WHERE `craft_id` = '$craft_id'
AND `user_id` = '$user_id'
");

function ResultItemId($craft_id)
{
	$craft_id = intval($craft_id);
	$qwe = qwe("
	SELECT `result_item_id` FROM `crafrs`
	WHERE `craft_id`= '$craft_id'
	");
	foreach($qwe as $q)
		return $q['result_item_id'];
}

/*
$sqldel="
DELETE FROM `user_crafts` 
WHERE `user_id` = '$user_id' 
AND `item_id` = '$item_id'";
qwe($sqldel);

$sqldel="
DELETE FROM `user_crafts` 
WHERE `user_id` = '$user_id' 
AND `isbest` <2";
qwe($sqldel);
*/	
?>
<meta http-equiv="refresh" content="0; url=/catalog.php">