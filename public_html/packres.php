<?php
setcookie('path', 'packres');
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User();
$User->check();
$user_id = $User->id;
$ver = random_str(8);

?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Ресурсы для паков</title>
    <?php CssMeta(['default.css','user_prices.css']);?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php'; ?>
<main>
<div id="rent">

	<div class="navcustoms">
	    <h2>Ресурсы для паков</h2><br>
        <div class="prmenu">
            <?php $User->ServerSelect();?>

            <br>
            <a href="user_prices.php"><button class="def_button">Мои цены</button></a>
            <a href="user_customs.php"><button class="def_button">Настройки</button></a>
            <a href="packpost.php"><button class="def_button">Пак-инфо</button></a>

        </div><br><hr>
        <div class="modes"></div>
        <?php modes($User->mode); ?>

        <div class="PrColorsInfo">
            <span style="background-color:#f35454">Чужая цена</span>
            <span style="background-color:#dcde4f">Цена друга</span>
            <span style="background-color:#79f148">Ваша цена</span>
        </div>
        <div class = "responses"><div id = "responses"></div></div>
	</div>

<div id="rent_in" class="rent_in">
<div class="clear"></div>

<div class="all_info_area">
<div class="all_info" id="all_info">
<div id="items">
<div class="prices">
<?php
$sql = "
SELECT 
items.item_id, 
items.item_name, 
items.craftable,  
items.personal,
items.is_trade_npc,
items.valut_id,
items.icon,
items.basic_grade,
prices.auc_price,
prices.time,
isbest,
(isbest = 3) as isbuy,
user_crafts.craft_price
FROM
(
	SELECT item_id, result_item_id 
	FROM craft_materials 
	WHERE result_item_id IN 
	(
		SELECT DISTINCT item_id 
		FROM packs WHERE item_id IN 
		(SELECT result_item_id FROM crafts WHERE on_off)
	)
	OR result_item_id in (49003, 49033, 49034)
	OR result_item_id in 
	(
		SELECT item_id 
		FROM craft_materials 
		WHERE result_item_id 
		IN  (49003, 49033, 49034)
	)
	GROUP BY item_id
) as tmp
INNER JOIN items 
	ON items.item_id = tmp.item_id 
	AND items.on_off 
	AND items.item_id != 500
    AND (!(items.valut_id = 500 AND items.is_trade_npc))
LEFT JOIN prices 
	ON prices.user_id = '$User->id' 
	AND prices.item_id = items.item_id 
	AND prices.server_group = '$User->server_group'
LEFT JOIN user_crafts 
	ON user_crafts.user_id = '$User->id' 
	AND user_crafts.item_id = items.item_id 
	AND user_crafts.isbest > 0
	ORDER BY isbuy DESC, item_name
	";
$qwe = qwe($sql);


require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/cat-funcs.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/../functions/funct-obhod2.php';
qwe("DELETE FROM craft_buffer WHERE `user_id` = '$User->id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$User->id'");
foreach($qwe as $q)
{
    //extract($q);
    if($q['craft_price']) continue;
    if(!$q['craftable']) continue;
    if($q['is_trade_npc'] and $q['valut_id'] == 500) continue;

    CraftsObhod($q['item_id'], $User->id);

}
qwe("DELETE FROM craft_buffer WHERE `user_id` = '$User->id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$User->id'");

$folows = Folows($User->id);
$qwe = qwe($sql);
UserPriceList($qwe);

?>
</div>



</div>
</div></div>
</div></div>
</main>

<?php
function CraftPriceForItem($item_id,$user_id)
{
    $qwe = qwe("
    SELECT * FROM user_crafts
    WHERE user_id = '$user_id'
    and item_id = '$item_id'
    order by isbest desc 
    limit 1
    ");
    if(!$qwe or $qwe->num_rows == 0)
        return false;
    $qwe = mysqli_fetch_assoc($qwe);
    return $qwe['craft_price'];
}

include_once 'pageb/footer.php';
addScript('js/setbuy.js');
if(!$User->ismobiledevice)
    addScript('js/tooltips.js');
addScript('js/packres.js');
?>
</body>

</html>