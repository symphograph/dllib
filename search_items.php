﻿<?php
if(empty($_GET['query'])) die;

$href = '<a href="catalog.php?';
$href2 = '" text-decoration: none; style="color: #6C3F00;" ';
include 'includs/ip.php';
include 'includs/config.php'; // Подключение к БД.
$query = mysqli_real_escape_string($dbLink,$_GET['query']);
$array = array();
$request  = qwe("
SELECT `category`, `item_name`, `item_id`, `icon`
FROM `items` 
WHERE (
`item_name` LIKE '%".$query."%')
AND `item_name` is not Null  
AND `on_off` = 1 
ORDER BY `craftable` DESC, `ismat` DESC, `item_id`, `category`, `item_name`, `personal` LIMIT 0, 50");
while($data = mysqli_fetch_assoc($request))
{
	$name = $data['item_name'];
	$item_id = $data['item_id'];
	$data_id = 'data-id="'.$item_id.'">';
	$q_name= 'query='.str_replace(" ","+",$name);
	$q_id = '&query_id='.$item_id;
	$icon = mysqli_real_escape_string($dbLink,$data['icon']);
	//$item_link= $q_name.$q_id.$href2.$data_id;
	$item_link= 'query_id='.$item_id.$href2.$data_id;
	$array[] ='<div class="advice_variant" id="'.$item_id.'" data-id="'.$item_id.'"><img id="icon" width="40px" src="img/icons/50/'.$icon.'.png">'.$name.'</div>';
}

echo "['".implode("','", $array)."']";

exit();
	?>