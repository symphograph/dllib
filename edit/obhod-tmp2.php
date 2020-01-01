<?php
$ip = $_SERVER['REMOTE_ADDR'];
if(!$myip)
exit();
 ini_set('display_errors',1);
error_reporting(E_ALL);
$start = microtime(true); 
include '../includs/config.php';

$orquery = qwe("SELECT `price` FROM `items` WHERE `item_id` = 1");
$ormass = mysqli_fetch_assoc($orquery);
$orcost = $ormass['price']*2/86000;

$result_item_id = 31175;
$craftq = qwe("SELECT `craft_id`, `dood_name`, `next` FROM `crafts` where `result_item_id` = '$result_item_id' AND `on_off` > 0 AND `craft_id` > 0");
$count_crafts = mysqli_num_rows($craftq);
echo $count_crafts.' Рецептов<br>';



     for ($i = 1; $i <= $count_crafts; $i++){
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
$query2 = qwe("SELECT craft_materials.mater_need, items.price, items.price_type, items.item_id FROM craft_materials, items WHERE craft_materials.craft_id = '$craft_id' AND items.item_id = craft_materials.item_id and items.on_off = '1' ORDER BY item_id");
$sum = 0;
foreach($query2 as $key){
	$mater_need = $key['mater_need'];
	$price = $key['price'];
	if($next == 1 and $mater_need < 0) $price = $price*0.9;
	$mater = $key['item_id'];
	$matsum = $mater_need*$price;
	$sum = $sum+$matsum;}
	$total = $sum+$or_need*$orcost;
	
$mycost= round($total/$itog,0);
$krytery = $mycost/$itog;
if (!$mycost == 0){
$krytery2[$craft_id] = $mycost/$itog;
$mycost2[$craft_id] = $mycost;};

echo '<p>Предложено: '.$craft_id.' '.$item_id.' '.$rec_name.' '.$dood_name.' '.$mycost.' $krytery='.$krytery.'</p>';



   };
   if($next == 1)
echo '<p>Проходной</p>';
if (!$mycost == 0){
   print_r($krytery2);
echo '<br><br>';
print_r($mycost2);


$recomend_craft = array_search(min($krytery2),$krytery2);
$recomend_mycost = $mycost2[$recomend_craft]; 
echo '<p>Рекомендуемый рецепт: '.$recomend_craft.'</p>';
echo '<p>Стоимость предмета по этому рецепту: '.$recomend_mycost.'</p>';
echo '<p>Пишем в базу</p>';
//qwe("UPDATE items SET price = '$recomend_mycost', recomend_craft = '$recomend_craft' WHERE item_id = '$item_id' AND looked is NULL AND is_trade_npc < '1'");
echo '<p>'.$item_id.'</p>';
$krytery2= array();
$mycost2 = array();}
else 
echo '<p>Что-то не так</p>';

$long = microtime(true) - $start;
//qwe("INSERT INTO obhods (`mess`, `time`, `howlongobhod`) VALUES ('Выполнен одиночный обход', now(), '$long')");
echo '<br>Время выполнения скрипта: '.$long.' сек.';
//
?>