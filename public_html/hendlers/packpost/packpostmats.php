<?php
$item_id = $_POST['item_id'] ?? 0;
$item_id = intval($item_id);
if(!$item_id) die();

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$User = new User;
if(!$User->byIdenty())
    die();
$user_id = $User->id;


$Item = new Item();
$Item->getFromDB($item_id);
$Item->getBestCraft();
if(!$Item->isCounted()){
    $Item->RecountBestCraft(1);
    $Item = new Item();
    $Item->getFromDB($item_id);

}
if(!$Item->isCounted()){
    die('uncounted');
}

?><div id="craft_price"><?php
echo 'Себестоимость: '.esyprice($Item->craft_price);
?></div><?php

$qwe = qwe("
SELECT 
items.item_id,
items.item_name,
items.icon,
items.basic_grade,
items.valut_id,
items.price_buy,
craft_materials.mater_need,
craft_materials.mat_grade,
items.is_trade_npc,
items.craftable,
user_crafts.isbest,
user_crafts.craft_price    
FROM craft_materials
inner join `items` on craft_materials.item_id = `items`.item_id
and craft_materials.craft_id = '$Item->bestCraftId'
LEFT join user_crafts on craft_materials.item_id = user_crafts.item_id
and user_crafts.user_id = '$user_id'
and isbest > 0
");
//var_dump($qwe);
if(!$qwe or !$qwe->rowCount())
    die('err');

?><div class="pkmats_area">

<?php
UserPriceList($qwe);
?>


</div>