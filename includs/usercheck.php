<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functs.php';
$BotName = is_bot();
if(empty($_COOKIE['identy']) and (!$BotName))
CookieTest();
$userinfo_arr = Metka($ip,$BotName);
//setcookie("test","");
$user_id = $userinfo_arr['muser'];

$identy = $userinfo_arr['identy'];
$server_group = $userinfo_arr['server_group'];
$server = $userinfo_arr['server'];
$fname = $userinfo_arr['fname'];
$avatar = $userinfo_arr['avatar'];
$email = $userinfo_arr['email'];
$siol = $userinfo_arr['siol'] ?? 0;
$user_nick = $userinfo_arr['user_nick'];
$uncustomed = ProfUnEmper($user_id);
$mode = $userinfo_arr['mode'];
$agent = get_browser(null, true);
$ismobiledevice = $agent['ismobiledevice'];

$based_prices = '32103,32106,2,3,4,23633,32038,8007,32039,3712,27545,41488';

function ProfUnEmper($user_id)
{
	$prof_q = qwe("SELECT * FROM `user_profs` where `user_id` ='$user_id'");
	if(mysqli_num_rows($prof_q) > 0)
		return false;
	$query = qwe("
	SELECT *
	FROM `profs`
	WHERE `used` = 1");
	foreach($query as $q)
	{
		extract($q);
		qwe("
		REPLACE INTO `user_profs` 
		(`user_id`, `prof_id`, `lvl`) 
		VALUES 
		('$user_id', '$prof_id', 0)");
	}
	return true;
}

function CookieTest()
{
	if(!empty($_COOKIE["test"]))
	{
		if(!empty($_GET["cookie"]))
		header("Location: $_SERVER[PHP_SELF]");
		else
		return true;
	}
	
  
	if(empty($_GET["cookie"]))
	{
		// посылаем заголовок переадресации на страницу,
		// с которой будет предпринята попытка установить cookie 
		setcookie("test","1");
		header("Location: $_SERVER[PHP_SELF]?cookie=1");
		// устанавливаем cookie с именем "test"
	  	exit();
	}
	//Если добрались до этого места, значит куки не работают.
	exit('<meta charset="utf-8"><h3>Для корректной работы приложения необходимо включить cookies</h3>');
}
?>