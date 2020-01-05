<?php
if(empty($_POST['craft_id']))
exit();

include_once $_SERVER['DOCUMENT_ROOT'].'/includs/usercheck.php';

$craft_id = intval($_POST['craft_id']);

$uri_from = $_SERVER['HTTP_REFERER'];

$query = qwe("REPLACE INTO `hide_cl` 
(`user_id`, `craft_id`)
VALUES 
('$user_id', '$craft_id')");
if($query)
	echo 'Ok';
?>
