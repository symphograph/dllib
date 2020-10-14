<?php 
/**
Classic cattle code.
In memory of a nightmare.
*/
$ip = $_SERVER['REMOTE_ADDR'];
if($ip == '37.194.65.246'){
 ini_set('display_errors',1);
error_reporting(E_ALL);}
else exit();
$start = microtime(true);
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';
$craftq = qwe("SELECT * FROM `crafts` where `on_off` = 1 order by `deep` DESC");
$matsq = qwe("SELECT * FROM `craft_materials` where craft_id > 0");
//$itemsq = qwe("SELECT * FROM `items` `on_off` = 1");
$resul_item = 8017;
function tree($craftq, $matsq, $resul_item){
	global $craftq, $resul_item;
foreach($craftq as $key){
	if($key['result_item_id'] == $resul_item)
	{$craft_id = $key['craft_id']; echo $craft_id;
	foreach($matsq as $mat){
		if($mat['craft_id'] == $craft_id){
		$mats[] = $mat['item_id'];
		echo ' '.$mat['item_id'].'<br>';}
	//$allmats[$key['craft_id']][] = $key['item_id'];
	}
	
	for($i=0;$i< count($mats);$i++){
	$new_item = $mats[$i];
	tree($craftq, $matsq, $new_item);
	}
	//tree($craftq, $matsq, $resul_item);
	
	//$found_crafts[$key['result_item_id']][] = $key['craft_id'];
	};}}
	tree($craftq, $matsq, $resul_item);
	//$found_crafts = tree($craftq, $resul_item);
/*foreach($matsq as $key){
	$allmats[$key['craft_id']][] = $key['item_id'];
	}
	foreach($found_crafts as $key => $v){
		
		echo $key.' имеет '.count($v).' рецепта: '.implode(",", $v).'<br>';
		if(count($v) > 0){
			for($i=0;$i< count($v);$i++){
			echo $key.' '.$v[$i].'<br>';}
			};
	};*/
echo '<br>Время выполнения скрипта: '.(microtime(true) - $start).' сек.<br>';
?>