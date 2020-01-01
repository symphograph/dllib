<?php
$ip = $_SERVER['REMOTE_ADDR'];
if($ip !== '37.194.65.246')
exit();
 ini_set('display_errors',1);
error_reporting(E_ALL);
$start = microtime(true); 
include '../includs/config.php';
include 'funct-obhod.php';
$orquery = qwe("SELECT price FROM items WHERE item_id = 1");
$ormass = mysqli_fetch_assoc($orquery);
$orcost = $ormass['price']*2/86000;

$result_item_id = 8017;
$craftq = qwe("SELECT craft_id, dood_name, `next` FROM crafts where result_item_id = '$result_item_id' AND on_off > 0 AND craft_id > 0");
$count_crafts = mysqli_num_rows($craftq);
echo $count_crafts.' Рецептов<br>';
     for ($i = 1; $i <= $count_crafts; $i++){
		

	rescost($craftq, $orcost);
	
	
$mycost= round($total/$itog,0);
$krytery = $mycost/$itog;
if (!$mycost == 0){
$krytery2[$craft_id] = $mycost/$itog;
$mycost2[$craft_id] = $mycost;};

echo '<p>Предложено: '.$craft_id.' '.$item_id.' '.$rec_name.' '.$dood.' '.$mycost.' $krytery='.$krytery.'</p>';



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