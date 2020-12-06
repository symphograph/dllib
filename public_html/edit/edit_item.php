<?php 
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if(!$cfg->myip)  exit();


$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die('<span style="color: red">Oh!<span>');
//printr($userinfo_arr);
extract($userinfo_arr);
$user_id = $muser;
?>
<!DOCTYPE HTML>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Редактор предмета</title>

</head>
<body>
<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php';
if(isset($_GET['item_id']))
{
$item_id = intval($_GET['item_id']);
//$item_name = $_GET['item_name'];
//$item_name = ItemNames($item_name);
	$query = qwe("SELECT * FROM `items` WHERE `item_id` = '$item_id'");
	foreach($query as $v)
	{
		$descr = $v['description'];
		$categ = $v['category'];
		$categ_id = $v['categ_id'];
		$slot = $v['slot'];
		$price_type = $v['price_type'];
		$valut_id = $v['valut_id'];
		$is_trade_npc = $v['is_trade_npc'];
		$personal = $v['personal'];
		$price_buy = intval($v['price_buy']);
		$item_name = $v['item_name'];
	}
	
	
	?>
<center>
	<form method="post" action="">
	Имя предмета<br>
	<input type="text" style="width: 400px" autocomplete="off" name="item_name" value="<?php echo $item_name?>"><br>

<br><label for="categ">Категория</label><br>
<select name="categ" id="categ" autocomplete="off">
<?php

	$query = qwe("SELECT * FROM `item_categories` WHERE `item_group` != 19 
	ORDER BY `name`");
	SelectOpts($query, 'id', 'name', $categ_id, 'Нет категории')
?>
	</select><br>
	<br><label for="descr">Описание</label><br>
<?php
	
	echo	
	'<input type="hidden" name="item_id" value="'.$item_id.'" autocomplete="off">
	<textarea name="descr" id="descr">'.$descr.'</textarea>
	';	
?>
<br><label for="slot">Слот</label><br>
<select name="slot" id="slot" autocomplete="off" autocomplete="off">
<?php
	$query = qwe("SELECT * FROM `slots` ORDER BY `slot_name`");
	SelectOpts($query, 'id', 'slot_name', $slot, 'Слот не выбран');
	
?>
</select><br>

<br><label for="price_type">Покупается за</label><br>
<select name="valut_id" id="valut_id" autocomplete="off">
<?php
	$query = qwe("SELECT * FROM `valutas` ORDER BY `valut_name`");
	SelectOpts($query, 'valut_id', 'valut_name', $valut_id, 'Тип не указан');
?>
</select><br>

<?php

?>
<br><label for="price_buy">Цена покупки у NPC</label><br>
<input type="number" id="price_buy" name="price_buy" autocomplete="off" value="<?php echo $price_buy;?>"/>

<?php
$checked1 = $checked0 = '';
if($is_trade_npc > 0) $checked1 = 'checked';
else $checked0 = 'checked';
?>
<p>Продается у NPC</p>
<p><input type="radio" autocomplete="off" name="is_trade_npc" id="is_trade_npc1" <?php echo $checked1;?> value="1"/>
<label for="is_trade_npc1">Да</label></p>
<p><input type="radio" autocomplete="off" name="is_trade_npc" id="is_trade_npc0" <?php echo $checked0;?> value="0"/>
<label for="is_trade_npc0">Нет</label></p>

<?php
$checked1 = $checked0 = '';
if($personal > 0) $checked1 = 'checked';
else $checked0 = 'checked';
?>
<p>Персональный предмет</p>
<p><input type="radio" autocomplete="off" name="personal" id="personal1" <?php echo $checked1;?> value="1"/>
<label for="personal1">Да</label></p>
<p><input type="radio" autocomplete="off" name="personal" id="personal0" <?php echo $checked0;?> value="0"/>
<label for="personal0">Нет</label></p>
<br><button type="submit" name="send" value = "1">Записать</button></form>
<?php
	
	
};
	

if(!empty($_POST['item_id']) and !empty($_POST['item_name']) and !empty($_POST['send']))
{
	
	$item_id = intval($_POST['item_id']);
 	$item_name = ItemNames($_POST['item_name']);
 	$descr =  Description($_POST['descr']);
    $cat_id = intval($_POST['categ']);
 	$slot = intval($_POST['slot']);
	$valut_id = $_POST['valut_id'];
 	$is_trade_npc = intval($_POST['is_trade_npc']);
 	$personal = intval($_POST['personal']);
 	$price_buy = intval($_POST['price_buy']);
 
	qwe("
	UPDATE `items` SET 
	`item_name` = '$item_name', 
	`description` = '$descr', 
	`categ_id` = '$cat_id', 
	`slot` = '$slot',
	`valut_id` = '$valut_id',
	`price_type` = (SELECT `valut_name` FROM valutas WHERE valut_id = '$valut_id'),
	`is_trade_npc` = '$is_trade_npc',
	`personal` = '$personal',
	`price_buy` = '$price_buy'
	WHERE `item_id` = '$item_id'");

	qwe("
	UPDATE `crafts` 
	SET `result_item_name` = '$item_name' 
	WHERE `result_item_id` = '$item_id'");
 
    echo '<meta http-equiv="refresh" content="0; url=../catalog.php?item_id='.$item_id.'"">';
};


?>
</center>	
</body>
</html>