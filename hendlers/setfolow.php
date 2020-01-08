<?php
//var_dump($_POST);
$folow = $_POST['folow'] ?? 0;
$sfolow_id = $_POST['sfolow_id'] ?? 0;
$condition = $_POST['condition'] ?? 0;
$sfolow_id = intval($sfolow_id);
$condition = intval($condition);
if((!$folow) and (!$sfolow_id) and (!$condition))
	die();
if($folow)
{
	if(!is_array($folow))
		die();
	if(!count($folow))
		die();	
}

$reports = ['<span style="color: red">ой!<span>','ок'];
	
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
include_once '../functions/functions.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functs.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';


$userinfo_arr = UserInfo();

if(!$userinfo_arr)
	die();

extract($userinfo_arr);
$user_id = $muser;

if($folow)
{
	qwe("DELETE FROM `folows` WHERE `user_id` = '$user_id'");
	foreach($folow as $folow_id => $v)
	{
		$folow_id = intval($folow_id);
		if(!$folow_id or !$v) continue;
		qwe("REPLACE INTO `folows` 
		(`user_id`, `folow_id`)
		VALUES
		('$user_id', '$folow_id')
		");
	}
}elseif($condition == 1)
{
	qwe("
	DELETE FROM `folows` 
	WHERE `user_id` = '$user_id'
	AND `folow_id` = '$sfolow_id'
	");
}elseif($condition == 2)
{
	qwe("REPLACE INTO `folows` 
		(`user_id`, `folow_id`)
		VALUES
		('$user_id', '$sfolow_id')
		");
}

//echo 'ok';
?>
