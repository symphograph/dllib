<?php
if(empty($_POST['craft_id']))
exit();

include_once $_SERVER['DOCUMENT_ROOT'].'/includs/usercheck.php';

$craft_id = intval($_POST['craft_id']);

$uri_from = $_SERVER['HTTP_REFERER'];

$query = qwe("
DELETE FROM `hide_cl` 
WHERE `user_id` = '$user_id' 
AND `craft_id` = '$craft_id'");

?>
