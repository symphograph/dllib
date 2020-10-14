<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';

if(!preg_match('/user_customs.php/', $_SERVER['HTTP_REFERER']))
{echo '<meta http-equiv="refresh" content="0; url=../index.php">'; exit();};

foreach($_POST['prof'] as $prof_id => $lvl)
{
	$prof_id = intval($prof_id);
	$lvl = intval($lvl);
	//var_dump($lvl);

	qwe("REPLACE INTO `user_profs` 
	(`user_id`, `prof_id`, `lvl`,`time`) 
	VALUES 
	('$user_id', '$prof_id', '$lvl',NOW())
	");
}
qwe("DELETE FROM `user_crafts` WHERE `user_id` = '$user_id' AND `isbest` < 2");
//exit();
echo '<meta http-equiv="refresh" content="0; url=../user_customs.php">';
?>