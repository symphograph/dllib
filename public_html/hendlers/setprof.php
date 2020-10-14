<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
if(empty($_POST['prof_id']))
exit();
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';
$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die('<span style="color: red">Oh!<span>');
$prof_id = intval($_POST['prof_id']);
$lvl = intval($_POST['lvl']);
extract($userinfo_arr);
$query = qwe("REPLACE INTO `user_profs` 
(`user_id`, `prof_id`, `lvl`,`time`)
VALUES 
('$muser', '$prof_id', '$lvl', now())");

if($query)
{
	echo 'ок';
	$sql="DELETE FROM `user_crafts` WHERE `user_id` = '$muser' and `isbest` <2";
	qwe($sql);
}	
else
	echo '<span style="color: red">Ой!<span>';
?>
