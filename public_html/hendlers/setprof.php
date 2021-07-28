<?php
if(empty($_POST['prof_id']))
	exit();

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

$User = new User;
if(!$User->byIdenty())
	die('<span style="color: red">Oh!<span>');



$prof_id = intval($_POST['prof_id']);
$lvl = intval($_POST['lvl']);


$qwe = qwe("REPLACE INTO `user_profs` 
(`user_id`, `prof_id`, `lvl`,`time`)
VALUES 
('$User->id', '$prof_id', '$lvl', now())");

if($qwe)
{
	qwe("DELETE FROM `user_crafts` WHERE `user_id` = '$User->id' and `isbest` < 2");
	echo 'ок';
}	
else
	echo '<span style="color: red">Ой!<span>';
?>
