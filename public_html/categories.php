<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if(!$cfg->myip) exit();
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Категории</title>
</head>
<?php
$query = qwe("SELECT * FROM `item_groups` WHERE `id` !=19 ORDER BY `name`");
$sel_group_id = 0;
if(!empty($_POST['item_group']))
$sel_group_id = intval($_POST['item_group']);
$sel_cat = 0;
if(!empty($_POST['category']))
$sel_cat = intval($_POST['category']);
?>
<form method="post" action="">
	<label for="item_group">Группа</label>
	<select name="item_group" id="item_group" onchange="this.form.submit()">
	<?php
	SelectOpts($query, 'id', 'name', $sel_group_id, 'Группа не выбрана');	

	?>
	</select>
	<label for="category">Категория</label>
	<select name="category" id="category" onchange="this.form.submit()">
	<?php
	$query = qwe("SELECT * FROM `item_categories` WHERE `item_group` !=19 and `item_group` = '$sel_group_id'");
	$defoult = 'Категория не выбрана';
	SelectOpts($query, 'id', 'name', $sel_cat, 'Категория не выбрана');

	?>
	</select>
</form>
<?php
$query = qwe("SELECT * FROM `items` WHERE `categ_id` = '$sel_cat' AND `on_off` = 1 ORDER BY `slot`, `lvl`");
echo $query->num_rows.'<br>';
	foreach($query as $itm)
	{
		$item_id = $itm['item_id'];
		$item_name = $itm['item_name'];
		echo '<p>'.$item_name.'</p>';
	}
?>
<body>
</body>
</html>