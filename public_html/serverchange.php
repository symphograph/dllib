<?php
$serv = $_POST['serv'] ?? 0;
$serv = intval($serv);
if(!$serv) die;

if(empty($_COOKIE['path'])) die;
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$User = new User;
if(!$User->byIdenty()){
    header ('Location: /packtable.php');
    die();
}

$User->setServer($serv);

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

header ('Location: '.$url);
