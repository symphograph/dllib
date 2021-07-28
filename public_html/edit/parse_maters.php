<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Развернуть KR рецепты</title>
</head>

<body>
<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
if(!$cfg->myip) exit;

	if(isset($_POST['mater_add']))
{
qwe("DELETE FROM `crafts` WHERE `craft_id` < 20000");
$craftsq = qwe("SELECT * FROM `crafts_kr` where on_off = 1 AND `craft_id` > 47 AND `craft_id` < 20000 ORDER BY `craft_id`");
$i=0;
	foreach($craftsq as $arrcr)
	{
	$craft_id = $arrcr['craft_id'];
    $rec_name = $arrcr['rec_name'];
	$dood_id = $arrcr['dood_id'];
	$dood_name = $arrcr['dood_name'];
	$labor_need = $arrcr['labor_need'];
	$profession = $arrcr['profession'];
	$prof_need = $arrcr['prof_need'];
	$result_amount = $arrcr['result_amount'];
	$matidsids = trim($arrcr['mater_ids']);
	$crafdata = trim($arrcr['rec_data']);
	$matids = array_unique(explode(",",$matidsids));
	$resul_item_ids= array_pop($matids);
	$result_item_id = trim($arrcr['result_item_id']);
	$result_item_name = trim($arrcr['result_item_name']);
	$name = explode("<br>",$crafdata);
	$matids2=array_values($matids);
	$arr = $name;
	qwe("
	REPLACE INTO `crafts` (
		`craft_id`,
		`rec_name`,
		`dood_id`,
		`dood_name`,
		`result_item_id`,
		`result_item_name`,
		`labor_need`,
		`profession`,
		`prof_need`,
		`result_amount`
	     ) 
		 VALUES (
		    '$craft_id',
			'$rec_name',
			'$dood_id',
			'$dood_name',
			'$result_item_id',
			'$result_item_name',
			'$labor_need',
			'$profession',
			'$prof_need',
			'$result_amount'
		 )");
	     $r=0;
		foreach ($arr as $value) 
		{

			$value = explode(" x ",trim($value));
			$item_name=$value['0'];
			$mater_need=$value['1'];
			$item_id = $matids2[$r];
			//print_r($value);
			//echo $item_id.' x '.$mater_need.'<br>';
		qwe("
		REPLACE INTO `craft_materials` 
		(`craft_id`, `item_id`, `result_item_id`, `mater_need`, `item_name`, `result_item_name`)
		VALUES 
		('$craft_id', '$item_id', '$result_item_id', '$mater_need', (select `item_name` FROM `items` WHERE `item_id` = '$item_id'), '$result_item_name')");

		$r++;

		}
      $i++;
	}
	qwe("DELETE FROM `crafts` WHERE `craft_id` in (SELECT `craft_id` FROM `craft_materials` WHERE `item_id` = `result_item_id`)");
	qwe("UPDATE `crafts`, `doods_ru` SET `crafts`.`dood_name` = `doods_ru`.`dood_name_` WHERE `crafts`.`dood_id` = `doods_ru`.`dood_id`");
	qwe("UPDATE `crafts`, `profs_kr` SET `crafts`.`profession` = `profs_kr`.`ru` WHERE `crafts`.`profession` = `profs_kr`.`kr`");
	require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/craftable.php';
	echo 'Развернул '.$i.' рецептов.';
	exit();
}
?>
Эта кнопка развернёт материалы рецептов в базу.
<form action="" method="post">
<input type="hidden" value="1" name="mater_add">
<input type="submit" value="Развернуть материалы">
</form>
</body>
</html>