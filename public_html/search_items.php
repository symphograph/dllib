﻿<?php
if(empty($_GET['query'])) die;

require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php'; // Подключение к БД.
$query = mysqli_real_escape_string($dbLink,$_GET['query']);
$array = array();
$request  = qwe("
SELECT `category`, `item_name`, `item_id`, `icon`
FROM `items` 
WHERE (
`item_name` LIKE '%".$query."%')
AND `item_name` is not Null  
AND `on_off` = 1 
ORDER BY `item_name`, `craftable` DESC, `ismat` DESC, `category`, `personal`, `item_id` LIMIT 0, 50");
while($data = mysqli_fetch_assoc($request))
{
	$name = $data['item_name'];
	$item_id = $data['item_id'];
	$icon = mysqli_real_escape_string($dbLink,$data['icon']);
	
	$array[] ='<div class="advice_variant" id="'.$item_id.'" data-id="'.$item_id.'"><img id="icon" width="40px" src="img/icons/50/'.$icon.'.png">'.$name.'</div>';
}

echo "['".implode("','", $array)."']";

exit();
?>