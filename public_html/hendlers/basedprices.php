<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}


$User = new User;
if(!$User->byIdenty())
	die('<span style="color: red">Oh!<span>');
$user_id = $User->id;
?>
<h3>Базовые ресурсы</h3>
<p>Стоит определиться с их стоимостью</p>
<div class="PrColorsInfo">
    <span style="background-color:#f35454">Чужая цена</span>
    <span style="background-color:#dcde4f">Цена друга</span>
    <span style="background-color:#79f148">Ваша цена</span>
</div>
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
AND `prices`.`user_id` = '$User->id'
AND `server_group` = '$User->server_group'
WHERE `items`.`item_id` IN (32103, 32106,2,3,4,23633,32038,8007,32039,3712,27545,41488)");

//$folows = Folows($user_id);

foreach($query as $q)
{
	$q = (object) $q;

	PriceCell($q->item_id,$q->item_name,$q->icon,$q->basic_grade,null,0);
}

?>


<div class="line"></div>

<div class="clear"></div>

</div>

<br>
<br>
<a href="user_prices.php"><button class="def_button">Мои цены</button></a>
<hr>
<br><br>	
<div class="line"></div>