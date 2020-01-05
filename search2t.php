<?php

if(!empty($_GET['query'])){
	$href = '<a href="catalog-test.php?query=';
	$href2 = '" text-decoration: none; style="color: #6C3F00;">';
	include 'includs/config.php'; // Подключение к БД.
		$query = mysqli_real_escape_string($dbLink,$_GET['query']);
		$array = array();
		$request  = qwe("SELECT DISTINCT `category`, `item_name`, `item_id` FROM `items` WHERE `category` LIKE '%".$query. "%' AND `item_name` is not Null AND `on_off` = 1 OR `item_name` LIKE '%".$query. "%' AND `item_name` is not Null AND `on_off` = 1 ORDER BY `item_name` LIMIT 0, 30");
		while($data = mysqli_fetch_assoc($request)){
			$name = $data['item_name'];
			$item_link= str_replace(" ","+",$name).$href2;
			$array[] =$href.$item_link.'<div class="advice_variant"><img id="icon" src="img/icons/'.$data['item_id'].'.png">  '.$name.'</div></a>';
		}

		echo "['".implode("','", $array)."']";
	}
	exit();
	?>