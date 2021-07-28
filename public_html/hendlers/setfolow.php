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
	
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

$User = new User();
if(!$User->byIdenty())
	die('user error');


if($folow)
{
	qwe("DELETE FROM `folows` WHERE `user_id` = '$User->id'");
	foreach($folow as $folow_id => $v)
	{
		$folow_id = intval($folow_id);
		if(!$folow_id or !$v) continue;
		qwe("REPLACE INTO `folows` 
		(`user_id`, `folow_id`)
		VALUES
		('$User->id', '$folow_id')
		");
	}
}elseif($condition == 1)
{
	qwe("
	DELETE FROM `folows` 
	WHERE `user_id` = '$User->id'
	AND `folow_id` = '$sfolow_id'
	");
}elseif($condition == 2)
{
	qwe("REPLACE INTO `folows` 
		(`user_id`, `folow_id`)
		VALUES
		('$User->id', '$sfolow_id')
		");
}