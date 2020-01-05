<?php
//var_dump($_POST);
if(!isset($_POST['siol']))
	exit();
include_once '../includs/usercheck.php';
$setsiol = intval($_POST['siol']);
qwe("UPDATE `mailusers` SET `siol` = '$setsiol' WHERE BINARY `identy` = '$identy'");
//echo $_SERVER['HTTP_HOST'];
header("Location: https://".$_SERVER['HTTP_HOST']."/packtable.php");
?>