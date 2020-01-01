<?php
include('includs/ip.php');
$ipbd = '';
$cldbid = 0;
$group_lvl = 0;
include 'includs/config.php';
//$cluid = $_COOKIE['cluid'];
    $query = qwe("SELECT `cluid`, `cldbid`, `ip`, `sh_nick`, `group_id` FROM `ts_users` WHERE `ip` = '$ip'");
   $group_lvl = 0;
	foreach($query as $key)
	{
		$ipbd = $key['ip'];
		$nick = $key['sh_nick'];
		$cluid = $key['cluid'];
		$cldbid = $key['cldbid'];
		$group_id = $key['group_id'];
		
	};
    
if($ipbd == $ip)

{
	 $query = qwe("SELECT max(`group_lvl`) AS `lvl`
FROM `ts_groups` WHERE `group_id` IN 
(SELECT`group_id` FROM `ts_us_groups`
WHERE `cldbid` = (SELECT `cldbid` FROM `ts_users` WHERE ip = '$ip'
ORDER BY `lastconnected` DESC LIMIT 1))");
		foreach($query as $key)
		{
			$group_lvl = $key['lvl'];
		}
setcookie('guest', $nick, time()-3600*24*31*12, "/"); 
setcookie('cldbid', $cldbid, time()+3600*24, "/");
setcookie('sh_nick', $nick, time()+3600*24, "/");
//setcookie('group_lvl', $group_lvl, time()+3600*24, "/");

	//Персонально для Гора
	if($cldbid == 56 or (isset($_COOKIE['gor']) and $_COOKIE['gor']==100))
	  { 
		setcookie('gor', '100', time()+3600*24*7, "/");	
		$group_lvl = 5; $nick = 'Гор'; $cldbid = 56; $group_id = 16;
	  }	
$verif = 1;
$prof = 6;
};
if($ipbd !== $ip)
{
$verif = 0;
$nick = 'guest';
$prof = 0;
};
//Персонально для Гора
	if($cldbid == 56 or (isset($_COOKIE['gor']) and $_COOKIE['gor']==100))
	  { 
		setcookie('gor', '100', time()+3600*24*7, "/");	
		$group_lvl = 5; $nick = 'Гор'; $cldbid = 56; $group_id = 16;
	  }
?>