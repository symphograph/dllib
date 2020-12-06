<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}


$muser = $_POST['muser'] ?? 0;
$muser = intval($muser);//Юзер, которого надо сделать основным на этом девайсе.
if($muser == 0) die;

$userinfo_arr = UserInfo();
if(!$userinfo_arr) die;
$user_id = $userinfo_arr['muser'];//Текущий юзер, который залогинен с этого девайса.

//Проверяем, что акаунт, который юзер хочет сделать основным, логинился на этом девайсе.
$sessmark = OnlyText($_COOKIE['sessmark']);	
	if(iconv_strlen($sessmark) != 12)
		die('error_sess');

$qwe = qwe("
SELECT * FROM `sessions` 
WHERE `sessmark` = '$sessmark'
AND `user_id` = '$muser'
");
if(!$qwe or $qwe->num_rows != 1)
	die('error_user');

$qwe = qwe("
SELECT * FROM `mailusers` 
WHERE `mail_id` = '$muser'
");
if(!$qwe or $qwe->num_rows != 1) die('error_query');
foreach($qwe as $q)
{
	$newIdenty = $q['identy'];
}
$unix_time = time();
$datetime = date('Y-m-d H:i:s',$unix_time);
$cooktime = $unix_time+60*60*24*365*5;
setcookie('identy',$newIdenty,$cooktime,'/','',true,true);
header("Location: /profile.php");
?>