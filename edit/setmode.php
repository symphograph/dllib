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
$path_white = [
    'packtable' => 'packtable.php',
    'user_customs' => 'user_customs.php',
    'catalog' => 'catalog.php',
    'user_prices' => 'user_prices.php',
    'users' => 'users.php',
    'packres' => 'packres.php'
];
$path = $path_white[$_COOKIE['path']];

if(in_array($path,$path_white))
    $url = $path;
else
    $url = 'packtable.php';

header("Location: https://".$_SERVER['HTTP_HOST']."/".$url);
?>
