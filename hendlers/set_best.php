<?php
//var_dump($_POST); die;
include '../includs/ip.php';
$craft_id = $_POST['craft_id'] ?? $_GET['craft_id'] ?? 0;
$craft_id = intval($craft_id);
if($craft_id == 0) exit();
//var_dump($_POST);

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
//$auc_price = PriceMode($item_id,$user_id)['auc_price'];
$isbest = isBest($craft_id);
if($isbest)
{
	qwe("
	DELETE FROM `user_crafts` 
	WHERE `user_id` = '$user_id' AND
	`craft_id` ='$craft_id'
	");

}else
{
	qwe("UPDATE `user_crafts`
	SET `isbest` = 2
	WHERE `craft_id` = '$craft_id'
	AND `user_id` = '$user_id'
	");	
}


$sqldel="
DELETE FROM `user_crafts` 
WHERE `user_id` = '$user_id' AND
	(`isbest` <2
	OR 
	(`item_id` = '$item_id' AND `craft_id` !='$craft_id'))";
qwe($sqldel);



function isBest($craft_id)
{
	global $user_id;
	$qwe = qwe("
	SELECT `isbest` FROM `user_crafts`
	WHERE `craft_id` = '$craft_id'
	AND `user_id` = '$user_id'
	");
	if((!$qwe) or ($qwe->num_rows == 0)) return false;
	$qwe = mysqli_fetch_assoc($qwe);
	extract($qwe);
	return $isbest;
}
?>