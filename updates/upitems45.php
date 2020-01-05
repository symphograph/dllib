<?php
include '../includs/ip.php';
if(!$myip) exit();
include '../includs/config.php';
include '../functions/functions.php';
include '../functions/pars_functs.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Новые итемы 4.5</title>
</head>

<body>
<?php
//Сначала заведем новые итемы
$query = qwe("
SELECT * FROM `parsed_items` 
WHERE `item_id` in
(SELECT `item_id` FROM `New items 4.5`)
");
$i=0;
foreach($query as $q)
{
	//if($i>100) break;
	$item_id = intval($q['item_id']);
	$price_buy = intval($q['price_buy']);
	$price_type = mysqli_real_escape_string($dbLink,$q['price_type']);
	$price_sale = intval($q['price_sale']);
	$is_trade_npc = intval($q['is_trade_npc']);
	$category = mysqli_real_escape_string($dbLink,$q['category']);
	$item_name = mysqli_real_escape_string($dbLink,$q['item_name']);
	$description = mysqli_real_escape_string($dbLink,$q['description']);
	$on_off = intval($q['on_off']);
	$personal = intval($q['personal']);
	
	//$craftable = $q['craftable'];
	//$ismat = $q['ismat'];
	//$categ_id = $q['categ_id'];
	//$slot = $q['slot'];
	//$roll_group = $q['roll_group'];
	//$lvl = $q['lvl'];
	//$inst = $q['inst'];
	$basic_grade = intval($q['basic_grade']);
	//$forup_grade = $q['forup_grade'];
	if(preg_match('/deprecated|test|тестовый|NO_NAME|Не используется/ui',$item_name)) continue;
	if(in_array($category,['TESTitem','TESTNPC Packs','TESTBody Pack','TESTitem']))
		continue;
	$category = preg_replace('/ГравировкаНе/','Гравирвка',$category);
	
	$description = explode('Изготовление',$description);
	$description = $description[0];
	$description = explode('Стоимость:',$description);
	$description = $description[0];
	
	if(preg_match('/не нужен торговцам/',$description))
	{
		$price_sale = 0;
	}
	
	if(($price_buy + $price_sale)==0)
		$price_type = '';
	//echo $item_id.' '.$item_name.'<br>';
	qwe("
	INSERT INTO `items`
	(
		`item_id`,
		`price_buy`,
		`price_type`,
		`price_sale`,
		`is_trade_npc`,
		`category`,
		`item_name`,
		`description`,
		`on_off`,
		`personal`,
		`basic_grade`
	)
	VALUES
	(
		'$item_id',
		'$price_buy',
		'$price_type',
		'$price_sale',
		'$is_trade_npc',
		'$category',
		'$item_name',
		'$description',
		'1',
		'$personal',
		'$basic_grade'
	)
	");
	
	$i++;
}
echo 'Всего добавлено'.$i;
//Обновляем цены
$query = qwe("
SELECT * FROM `parsed_items`
");

foreach($query as $q)
{
	$item_id = intval($q['item_id']);
	$price_buy = intval($q['price_buy']);
	$price_sale = intval($q['price_sale']);
	$is_trade_npc = intval($q['is_trade_npc']);
	$description = Description($q['description']);
	$description = mysqli_real_escape_string($dbLink,$description);
	qwe("
	UPDATE `items`
	SET 
	`price_buy` = '$price_buy',
	`price_sale` = '$price_sale',
	`is_trade_npc` = '$is_trade_npc',
	`description` = '$description'
	WHERE `item_id` = '$item_id'
	");
}

qwe("UPDATE `items` SET `is_trade_npc` = 1 WHERE `price_type` = 'Честь'");

?>
</body>
</html>