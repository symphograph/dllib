<?php
function rescost($craftq, $orcost){
	global $itog, $item_id, $rec_name, $total, $dood, $next, $craft_id, $res_name;
	$craftmass =  mysqli_fetch_assoc($craftq);
		$craft_id = $craftmass['craft_id'];
		$qitog = qwe("SELECT `result_item_name`, `result_item_id`, `result_amount`, `dood_name`, `labor_need`, `prof_need`, `profession`, `rec_name`, `next` FROM `crafts` WHERE `craft_id` = '$craft_id' ORDER BY `result_item_id`") or die("ERROR: ".mysql_error());
	 $arritog = mysqli_fetch_assoc($qitog);
 		if ($arritog['result_amount']!==0 and ctype_digit($arritog['result_amount']))
		$itog = $arritog['result_amount'];
        $res_name = $arritog['result_item_name'];
		$item_id = $arritog['result_item_id'];
		$rec_name = $arritog['rec_name'];
		$or_need = $arritog['labor_need'];
		$profession = $arritog['profession'];
		if($or_need > 3) $or_need = $or_need*0.75;
		$dood = $arritog['dood_name'];
		$next = $arritog['next'];
		if($arritog['dood_name'] == 'Лаборатория')
		$itog = $itog*1.1;
$query2 = qwe("SELECT `craft_materials`.`mater_need`, `items`.`price`, `items`.`price_type`, `items`.`item_id` FROM `craft_materials`, `items` WHERE `craft_materials`.`craft_id` = '$craft_id' AND `items`.`item_id` = `craft_materials`.`item_id` and `items`.`on_off` = '1' ORDER BY `item_id`");
$sum = 0;
foreach($query2 as $key){
	$mater_need = $key['mater_need'];
	$price = $key['price'];
	if($next == 1 and $mater_need < 0) $price = $price*0.9;
	$mater = $key['item_id'];
	$matsum = $mater_need*$price;
	$sum = $sum+$matsum;}
	$total = $sum+$or_need*$orcost;
};
?>