<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}

$BotName = is_bot();
if(empty($_COOKIE['identy']) and (!$BotName))
CookieTest();
$userinfo_arr = Metka($BotName);

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




function CookieTest()
{
	if(!empty($_COOKIE["test"]))
	{
		if(!empty($_GET["cookie"]))
        {
            setcookie("test","1");
            header("Location: {$_SERVER['SCRIPT_NAME']}");
            die();
        }

        return true;
	}
	
  
	if(empty($_GET["cookie"]))
	{
		// посылаем заголовок переадресации на страницу,
		// с которой будет предпринята попытка установить cookie 
		setcookie("test","1");
		header("Location: {$_SERVER['SCRIPT_NAME']}?cookie=1");
		// устанавливаем cookie с именем "test"
	  	exit();
	}
	//Если добрались до этого места, значит куки не работают.
	exit('<meta charset="utf-8"><h3>Для корректной работы приложения необходимо включить cookies</h3>');
}
?>