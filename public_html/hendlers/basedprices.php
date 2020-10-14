<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functs.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';

$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die('<span style="color: red">Oh!<span>');
extract($userinfo_arr);
$user_id = $muser;
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
AND `prices`.`user_id` = '$user_id'
AND `server_group` = '$server_group'
WHERE `items`.`item_id` IN (32103, 32106,2,3,4,23633,32038,8007,32039,3712,27545,41488)");

$folows = Folows($user_id);

foreach($query as $pr)
{
	extract($pr);
	$auc_arr =  PriceMode($item_id,$user_id);
    $auc_price = $auc_arr['auc_price'] ?? false;

    $iscolor = ColorPrice($auc_arr);

	PriceCell($item_id,$auc_price,$item_name,$icon,$basic_grade,null,0,$iscolor);
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
</div>