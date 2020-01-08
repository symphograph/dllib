<meta charset="utf-8">
<?php
require_once 'includs/ip.php';
if(!$myip) exit();
include_once 'functions/functions.php';
include_once 'functions/functs.php';
include_once 'includs/config.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Время роста</title>
</head>

<body>
<?php
$tmuls = ['день.' => 1440,'дн.' => 1440,'д.' => 1440,'ч.'=>60,'мин.'=>1];
$qwe = qwe("
SELECT
craft_materials.item_id,
craft_materials.craft_id,
crafts.result_item_name,
items.item_name,
items.description,
crafts.result_item_id,
it2.category,
SUBSTRING_INDEX(SUBSTRING_INDEX(items.description,'Время роста: ',-1),'<br>',1) as ttime
FROM
craft_materials
INNER JOIN crafts ON craft_materials.craft_id = crafts.craft_id
AND dood_id = 9108
AND crafts.on_off
AND !mins
INNER JOIN items ON craft_materials.item_id = items.item_id
inner JOIN items as it2 ON crafts.result_item_id = it2.item_id 
AND mater_need > 0 AND items.on_off
AND items.description LIKE '%Время роста:%'
");	
foreach($qwe as $q)
{
	extract($q);
	$ttime = preg_replace('|[\s]+|s', ' ', $ttime);

	$tarr = explode(' ',$ttime);
	echo $result_item_name.'<br>';
	//echo $description.'<hr>';
	$tarr = array_reverse($tarr);
	//printr($tarr); continue;
	$mins = 0;
	foreach($tarr as $k=>$v)
	{
		$v= trim($v);
		if(array_key_exists($v,$tmuls))
			$mul = $tmuls[$v];
		else
			$mins = $mins+$v*$mul;
	}
	$mins = intval($mins);
	echo $mins.'<br>';
	/*qwe("UPDATE crafts 
	SET mins = '$mins'
	WHERE craft_id = '$craft_id'
	");*/
}
?>

</body>
</html>