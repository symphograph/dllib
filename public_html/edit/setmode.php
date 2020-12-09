<?php
//var_dump($_POST);
if(!isset($_POST['mode']))
	exit();
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User;
$User->check();
$user_id = $User->id;

$setmode = intval($_POST['mode']);
$setmode = $setmode ?? 1;
qwe("UPDATE `mailusers` SET `mode` = '$setmode' WHERE mail_id = '$User->id'");
$sql="DELETE FROM `user_crafts` WHERE `user_id` = '$User->id' and `isbest` <2";
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
