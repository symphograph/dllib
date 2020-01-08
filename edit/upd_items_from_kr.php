<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Обновить из KR</title>
</head>
<?php
require_once '../includs/ip.php';
if(!$myip) exit();
if(isset($_POST['upd']))
{
	include '../includs/config.php';
	$query = qwe("SELECT * FROM `items_kr`");
	foreach($query as $v)
	{   
		$item_id = $v['item_id'];
		$item_name = $v['item_name'];
		$price_buy = $v['price_buy'];
		$price_type = $v['price_type'];
		$price_sale = $v['price_sale'];
		$description = $v['description'];
		$category = $v['category'];
		$is_trade_npc = $v['is_trade_npc'];
		$personal = $v['personal'];

			
		qwe("REPLACE INTO `items` 
		(
		`item_id`,
		`item_name`,
		`price_buy`,
		`price_type`,
		`price_sale`,
		`description`,
		`category`,
		`is_trade_npc`,
		`personal`
		)
		VALUES 
		(
		'$item_id',
		'$item_name',
		'$price_buy',
		'$price_type',
		'$price_sale',
		'$description',
		'$category',
		'$is_trade_npc',
		'$personal'
		)");
		
	}
	qwe("DELETE from `items` where `item_name` rlike '[_]'");
	
	qwe("
	 UPDATE `items`, `categs_transl` 
	 SET `items`.`category` = `categs_transl`.`categ_ru`
     WHERE `items`.`category` = `categs_transl`.`categ_kr`");
	 
	echo 'Скрипт выполнен'; exit();
}
?>


<body>
Эта кнопка обновит базу из items_kr.
<form action="" method="post">
<input type="hidden" value="1" name="upd">
<input type="submit" value="Обновить">
</form>
</body>
</html>