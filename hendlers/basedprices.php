<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functs.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';

$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die('<span style="color: red">Oh!<span>');
extract($userinfo_arr);
$user_id = $muser;
?>
<h3>Базовые ресурсы</h3>
<p>Стоит определиться с их стоимостью</p>
<div class="prices">

<?php
	$query = qwe("SELECT 
`items`.`item_id`, 
`items`.`item_name`,
`items`.`icon`,
`items`.`basic_grade`,
`prices`.`auc_price` 
FROM `items`
LEFT JOIN `prices` 
ON `items`.`item_id` = `prices`.`item_id`
AND `prices`.`user_id` = '$user_id'
AND `server_group` = '$server_group'
WHERE `items`.`item_id` IN (32103, 32106,2,3,4,23633,32038,8007,32039,3712,27545,41488)");
	
foreach($query as $pr)
{
	extract($pr);
	$auc_price =  PriceMode($item_id,$user_id,3)['auc_price'];

	PriceCell($item_id,$auc_price,$item_name,$icon,$basic_grade);
}
?>


<div class="line"></div>

<div class="clear"></div>

</div>

<br>
<br>
<a href="user_prices.php"><button class="def_button">Все цены</button></a>
<hr>
<?php
	$query = qwe("
	SELECT 
`user_crafts`.`item_id`,
`user_crafts`.`auc_price`,
`user_crafts`.`craft_id`,
`items`.`item_name`,
`items`.`icon`
FROM `user_crafts`
INNER JOIN `items` ON `items`.`item_id` = `user_crafts`.`item_id`
AND `user_id` = '$user_id'
AND `isbest` = 3");

if(mysqli_num_rows($query)>0)
{
	?><br>
<h3>Эти ресурсы Вы покупаете</h3>
	<p>В дочерних рецептах используется указанная цена.</p>
	<div class="prices">
	
	<?php	
	foreach($query as $it)	
	{
		extract($it);
		$auc_price =  PriceMode($item_id,$user_id)['auc_price'];	
	?>
		
	<div class="price_cell" id="aucraft_<?php echo $item_id?>">
		<?php PriceCell($item_id,$auc_price,$item_name,$icon,$basic_grade);?>
		<br>
		<button type="button" class="def_button" onClick="AucraftDel(<?php echo $item_id;?>)">Удалить</button><hr>
	</div>
	
	<?php
	}
	?>
	
	</div>
<?php
}	

?>
<br><br>	
<div class="line"></div>
</div>