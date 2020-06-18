<?php
$serv = $_POST['serv'] ?? 0;
$serv = intval($serv);
if($serv == 0) die;
if(empty($_COOKIE['path'])) die;
include_once $_SERVER['DOCUMENT_ROOT'].'/includs/usercheck.php';

qwe("REPLACE INTO `user_servers` (`user_id`, `server`) VALUES ('$user_id', '$serv')");

$sqldel="DELETE FROM `user_crafts` WHERE `user_id` = '$user_id' and `isbest` <2";
qwe($sqldel);


$path_white = [
'packtable' => '/packtable.php',
'user_customs' => '/user_customs.php',
'catalog' => '/catalog.php',
'user_prices' => '/user_prices.php',
'users' => '/users.php',
'packres' => '/packres.php'
];
$path = $path_white[$_COOKIE['path']];

if(in_array($path,$path_white))
	$url = $path;
else 
	$url = 'packtable.php';
header ('Location: '.$url);?>
<meta http-equiv="refresh" content="0; url=<?php echo $url?>">
