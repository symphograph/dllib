<?php
//var_dump($_POST);
if(!isset($_POST['siol']))
	exit();
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$User = new User;
$User->check();
$user_id = $User->id;
$setsiol = intval($_POST['siol']);
qwe("UPDATE `mailusers` SET `siol` = '$setsiol' WHERE BINARY `identy` = '$identy'");
//echo $_SERVER['HTTP_HOST'];
header("Location: https://".$_SERVER['HTTP_HOST']."/packtable.php");
?>