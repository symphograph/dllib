<meta charset="utf-8">
<?php
$start = microtime(true);
require_once 'includs/ip.php';
if(!$myip) exit();
?>
<form method="post" action="">
<input type="submit" name="go" value="go">	
</form>
<?php
if(empty($_POST['go'])) exit();
include 'includs/config.php';
include 'functions/pars_functs.php';


$start_id = 98;
$id = $start_id;
$stop_id = 13649;
$query = qwe("SELECT `item_id` as `id` FROM `parsed_items` WHERE `item_id` >= '$start_id' and `item_id` <= '$stop_id'");
if($query)
	foreach($query as $mxitm)
	{
		$id = $mxitm['id'];
	qwe("UPDATE `parsed_last` SET `item_id` = '$id' WHERE `id` = 1");
	echo '<hr>';
	$plink = 'http://archeagedatabase.net/ru/item/'.$id;
	$somepage = curl($plink);
	$is_trade_npc = 0;
	
	//exit();
	if(!$somepage) echo 'error';
	preg_match_all('#<td colspan="2">ID:(.+?)<div class="addon_info">#is', $somepage, $arr);
		
	if(!$arr[0][0]) {echo 'Предмет пуст<hr>'; continue;}
	else
	$table = $arr[0][0];
	$price_type = '';
	
	$item_id = AboutCraft('#ID: (.+?)td>#is', 'digits', $table);
	echo '<p>Id: '.$id.' | ';

	if($item_id < 1) {echo 'Предмет пуст<hr>'; continue;}
	$item_name = AboutCraft('#id="item_name"(.+?)</span>#is', 'item_names', $table);
	if(preg_match('/deprecated|test|тестовый|NO_NAME|Не используется/ui',$item_name)) continue;
	echo $item_name.'</p>';
	
	$icon = AboutCraft('#<td class="item-icon"><div style="position: relative; left: 0; top: 0;"><img src="http://archeagedatabase.net/items/(.+?)\.png#is', 'img', $table);
	
	//echo '<p>'.$icon.'</p>';
	$img = file_get_contents('http://archeagedatabase.net/items/'.$icon.'.png');
	file_put_contents('img/icons/45/'.$item_id.'.png', $img);
	//echo '<img src="'.$img.'">';
	echo '<p>Сохранил</p>';
	/*
	unset($all_mat_inf);
	unset($itmarr);
	unset($arr);
	*/
}
echo 'Время выполнения скрипта: '.(microtime(true) - $start).' сек.';
?>
