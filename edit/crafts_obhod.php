<?php
 ini_set('display_errors',1);
error_reporting(E_ALL);
$ip = $_SERVER['REMOTE_ADDR'];
include '../includs/config.php';
include 'funct-obhod.php';
if($ip !== '37.194.65.246' and $ip !== '87.236.16.100')
{echo 'Нет доступа';
qwe("INSERT INTO errors (`mess`, `time`, `ip`, `page`) VALUES ('Не пускает', now(), '$ip', 'big-obhod')");
exit();};
 
$updq = qwe("SELECT * from `updates` where id = (SELECT max(`id`) from `updates` where edit_type = 'setprice')");
foreach($updq as $key){
	$updatet = $key['time'];
	echo $key['item_name'].' '.$updatet.'<br>';}
$updq2 = qwe("SELECT * from `obhods` where id = (SELECT max(`id`) from `obhods` where mess = 'Выполнен обход')");
foreach($updq2 as $key){
	$obhodt = $key['time'];
	echo $key['mess'].' '.$obhodt.'<br>';}
	if($updatet < $obhodt){
	qwe("INSERT INTO obhods (mess, time) VALUES ('Не требуется', now())");
	exit();};
	$start = microtime(true);
$forhonor = 'Чертеж: бронированный танк';
qwe("INSERT INTO obhods (mess, time) VALUES ('Начат обход', now())");
$honorq = qwe("SELECT price/price_alt from items where item_name = '$forhonor'");
$honormass = mysqli_fetch_assoc($honorq);
$honorprice = round($honormass['price/price_alt'],0);
qwe("UPDATE `items` SET `price` = $honorprice*price_alt WHERE price_type = 'Честь' AND item_name !='$forhonor'");
$query = qwe("SELECT DISTINCT `result_item_id`, `result_item_name` from `crafts` WHERE `on_off` = '1' and `result_item_id` > '0' and `craft_id` > '0' GROUP BY `result_item_id` ORDER BY `deep` DESC, `result_item_id`");
//Считаем всё что за репу
//$forrep = 'Катушка суровых ниток';
 
$repq = qwe("SELECT `price` from `items` where `item_id` = '3'");
$repmass = mysqli_fetch_assoc($repq);
$repprice = round($repmass['price'],0);
qwe("UPDATE items SET price = '$repprice'*price_alt WHERE price_type = 'Ремесленная репутация' AND item_id !='3'");
qwe("UPDATE items SET `price` = '$repprice' WHERE `item_id` = 3");
$orquery = qwe("SELECT `price` FROM `items` WHERE `item_id` = 1");
$ormass = mysqli_fetch_assoc($orquery);
$orcost = $ormass['price']*2/86000;
$b = mysqli_num_rows($query);
$blist = mysqli_fetch_assoc($query);
echo $b.'<br>';
//print_r ($blist);
//exit();

for ($c = 1; $c <= $b; $c++) {
	$arrmass = mysqli_fetch_assoc($query);
$result_item_id = $arrmass['result_item_id'];
//$result_item_id = 30903;
$craftq = qwe("SELECT `craft_id` FROM `crafts` where `result_item_id` = '$result_item_id' AND on_off > 0");
$count_crafts = mysqli_num_rows($craftq);
echo $count_crafts.' Рецептов<br>';
if ($count_crafts>1){
     for ($i = 1; $i <= $count_crafts; $i++){
		rescost($craftq, $orcost);

$mycost= round($total/$itog,0);
$krytery = $mycost/$itog;
if (!$mycost == 0){
$krytery2[$craft_id] = $mycost/$itog;
$mycost2[$craft_id] = $mycost;};

echo '<p>Предложено: '.$craft_id.' '.$item_id.' '.$rec_name.' '.$mycost.' $krytery='.$krytery.'</p>';
if($mycost < 0)
qwe("INSERT INTO errors_obhod (`craft_id`, `time`, `mycost`, `res_name`) VALUES 
('$craft_id', now(), '$mycost', '$res_name')");
//};
   }
   print_r($krytery2);
echo '<br><br>';
print_r($mycost2);


$recomend_craft = array_search(min($krytery2),$krytery2);
$recomend_mycost = $mycost2[$recomend_craft]; 
echo '<p>Рекомендуемый рецепт: '.$recomend_craft.'</p>';
echo '<p>Стоимость предмета по этому рецепту: '.$recomend_mycost.'</p>';
echo '<p>Пишем в базу</p>';
qwe("UPDATE items SET price = '$recomend_mycost', recomend_craft = '$recomend_craft' WHERE item_id = '$item_id' AND looked is NULL AND is_trade_npc < '1'");
$krytery2= array();
$mycost2 = array();

}
else
{ if ($count_crafts>0){
	echo $arrmass['result_item_name'].' имеет один рецепт.';
rescost($craftq, $orcost);

$mycost= round($total/$itog,0);
echo '<p>Посчитано: '.$craft_id.' '.$item_id.' '.$rec_name.' '.$mycost.'</p>';
if($mycost < 0)
qwe("INSERT INTO errors_obhod (`craft_id`, `time`, `mycost`, `res_name`) VALUES 
('$craft_id', now(), '$mycost', '$res_name')");
qwe("UPDATE `items` SET `price` = '$mycost', `recomend_craft` = '$craft_id' WHERE `item_id` = '$item_id' AND `looked` is NULL AND `is_trade_npc` < '1'");
if($res_name == 'Груз компоста' or $res_name == 'Груз зрелого сыра' or $res_name == 'Груз домашней наливки' or $res_name == 'Груз меда')
qwe("UPDATE `packs` SET `price` = '$mycost' WHERE `pack_name` LIKE '$res_name%'");
if(preg_match('/региональных товаров/iu', $dood) or preg_match('/товаров общины/iu', $dood))
qwe("UPDATE `packs` SET `price` = '$mycost' WHERE `item_id` = '$item_id'");}
else 
echo '<p>Больше ничего не вижу.</p>';

};
}
$long = microtime(true) - $start;
qwe("INSERT INTO obhods (`mess`, `time`, `howlongobhod`) VALUES 
('Выполнен обход', now(), '$long')");
echo '<br>Время выполнения скрипта: '.(microtime(true) - $start).' сек.';
//
?>