<?php
//var_dump($_POST);
if(!isset($_POST['mode']))
	exit();
include_once '../includs/usercheck.php';
$setmode = intval($_POST['mode']);
$setmode = $setmode ?? 1;
qwe("UPDATE `mailusers` SET `mode` = '$setmode' WHERE BINARY `identy` = '$identy'");
$sql="DELETE FROM `user_crafts` WHERE `user_id` = '$user_id' and `isbest` <2";
qwe($sql);
header("Location: https://".$_SERVER['HTTP_HOST']."/user_customs.php");
?>