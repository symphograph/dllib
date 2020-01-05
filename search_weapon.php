<?php

if(!empty($_GET['query'])){
	$href = '<a href="enchant.php?';
	$href2 = '" text-decoration: none; style="color: #6C3F00;" ';
	include 'includs/config.php'; // Подключение к БД.
		$query = mysqli_real_escape_string($dbLink,$_GET['query']);
		$array = array();
		$request  = qwe("
		SELECT `item_name`, `item_id` 
		FROM `items` 
		WHERE (
		`item_name` LIKE '%".$query."%')
		AND `item_name` is not Null  
		AND `on_off` = 1
		AND `categ_id` in (SELECT `id` FROM `item_categories` WHERE `item_group` = 1) 
		ORDER BY `craftable` DESC, `ismat` DESC, `item_id`, `category`, `item_name`, `personal` LIMIT 0, 50");
		while($data = mysqli_fetch_assoc($request)){
			$name = $data['item_name'];
			$item_id = $data['item_id'];
			$data_id = 'data-id="'.$item_id.'">';
			$q_name= 'query='.str_replace(" ","+",$name);
			$q_id = '&query_id='.$item_id;
			$item_link= $q_name.$q_id.$href2.$data_id;
			$array[] =$href.$item_link.'<div class="advice_variant" data-id="'.$item_id.'"><img id="icon" src="img/icons/'.$data['item_id'].'.png">  '.$name.'</div></a>';
		}

		echo "['".implode("','", $array)."']";
	}
	exit();
	?>