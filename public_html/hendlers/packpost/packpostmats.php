<?php
$item_id = $_POST['item_id'] ?? 0;
$item_id = intval($item_id);
if(!$item_id) die();

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User;
if(!$User->byIdenty())
    die();
$user_id = $User->id;

$craft_id = BestCraftForItem($user_id,$item_id);
if(!$craft_id)
{
    qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
    qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");
    require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/cat-funcs.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../functions/funct-obhod2.php';
    $Item = new Item();
    $Item->getFromDB($item_id);
    $Item->RecountBestCraft();
    qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
    qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");

    $craft_id = BestCraftForItem($user_id,$item_id);
}

$qwe = qwe("
SELECT * from user_crafts
where  user_id = $user_id 
and craft_id = '$craft_id' 
");
$q = mysqli_fetch_assoc($qwe);
?><div id="craft_price"><?php
echo esyprice($q['craft_price']);
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
and craft_materials.craft_id = '$craft_id'
LEFT join user_crafts on craft_materials.item_id = user_crafts.item_id
and user_crafts.user_id = '$user_id'
and isbest > 0
");
//var_dump($qwe);
if(!$qwe or !$qwe->num_rows)
    die('err');

?><div class="pkmats_area">

<?php
UserPriceList($qwe);
?>


</div>