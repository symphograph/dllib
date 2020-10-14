<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';
if(!$myip) exit();



?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Документ без названия</title>
</head>

<body>
	
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/cat-funcs.php';
include 'edit/funct-obhod2.php';
$server_group = ServerInfo($user_id);
$server = ServerInfo($user_id, 'server');
$prof_q = qwe("SELECT * FROM `user_profs` where `user_id` ='$user_id'");
$packs_q = qwe("SELECT DISTINCT `item_id` FROM `packs` 
WHERE `item_id` not in (SELECT `item_id` FROM `user_crafts` WHERE user_id = '$user_id')
AND `side` = 2
");

	
foreach($packs_q as $pack)
{
	$itemq = $item_id = $pack['item_id'];
	PacksObhod($item_id,$dbLink,$user_id,$server_group,$server,$prof_q);
	
	unset($total, $itog, $craft_id, $rec_name, $item_id, $lost, $forlostnames, $orcost, $repprice, $honorprice, $dzprice, $soverprice, $mat_deep, 
		$crafts, $crdeep, $deeptmp, $craftsq, $icrft,$crftorder);
}


?>
</body>
</html>