<?php
if(empty($_POST['craft_id']))
exit();

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User;
$User->byIdenty();

$craft_id = intval($_POST['craft_id']);

$uri_from = $_SERVER['HTTP_REFERER'];

$qwe = qwe("REPLACE INTO `hide_cl` 
(`user_id`, `craft_id`)
VALUES 
('$User->id', '$craft_id')");
if($qwe)
	echo 'Ok';
