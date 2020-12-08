<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if(empty($_POST['sendprices'])) 
exit();

//exit(var_dump($_POST));
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User;
$User->check();
$user_id = $User->id;


$uri_from = $_SERVER['HTTP_REFERER'];


foreach($_POST['setgold'] as $k => $v)
{
	$setgold= intval($v);
	$setsilver = intval($_POST['setsilver'][$k]);
	$setbronze = intval($_POST['setbronze'][$k]);
	$item_id = $k;
	$setprise = $setgold*10000+$setsilver*100+$setbronze;
	//echo 'id: '.$item_id.' - prise: '.$setprise.'<br>';
	if(!$setprise >0) continue;
	$sql="REPLACE INTO `prices` 
	(`user_id`, `item_id`, `auc_price`, `server_group`,`time`)
	VALUES 
	('$user_id', '$item_id', '$setprise', '$server_group', now())";
	qwe($sql);
	$sqlupd="UPDATE `user_crafts` SET `auc_price` = '$setprise'  
	WHERE `user_id` = '$user_id' 
	and `item_id` = '$item_id' 
	and `auc_price` > 1";
	qwe($sqlupd);
}	

	 $sqldel="DELETE FROM `user_crafts` WHERE `user_id` = '$user_id' and `isbest` <2";
	 qwe($sqldel);

/*
$query = qwe("
SELECT *
 FROM `user_crafts` WHERE 
WHERE `user_id` = '$user_id' and `isbest` = 2");
foreach($query as $q)
{
	$ucrafts[] = $q['craft_id'];
}
*/

echo '<meta http-equiv="refresh" content="0; url='.$uri_from.'">';	
?>
