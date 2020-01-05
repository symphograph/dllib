<?php

if(!empty($_GET['query'])){
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
		AND `roll_group` = 1 
		ORDER BY `craftable` DESC, `ismat` DESC, `item_id`, `category`, `item_name`, `personal` LIMIT 0, 50");
		while($data = mysqli_fetch_assoc($request)){
			$name = $data['item_name'];
			$item_id = $data['item_id'];
			$array[] ='<div class="advice_variant" onClick="document.forms.form.submit() (.form name="search_form")" data-id="'.$item_id.'"><img id="icon" src="img/icons/'.$data['item_id'].'.png">'.$name.'</div>';
		}

		echo "['".implode("','", $array)."']";
	}
	exit();
	?>