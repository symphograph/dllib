<?php

if(!isset($_POST)) die();

foreach ($_POST as $k => $v)
{
    $p[$k] = intval($v);
}

if(!$p['item_id'])
    die('item_id');

extract($p); //Никогда так не делайте.


require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';
if(!isset($user_id) or !$user_id)
    die();
$User = new User();
$User->getById($user_id);

$craft_id = BestCraftForItem($user_id,$item_id);
if(!$craft_id)
{
    $prof_q = qwe("SELECT * FROM `user_profs` WHERE `user_id` = '$user_id'");
    qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
    qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");
    require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/cat-funcs.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../functions/funct-obhod2.php';
    CraftsObhod($item_id,$dbLink,$User->id,$User->server_group,$User->server,$prof_q);
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
if((!$qwe) or (!$qwe->num_rows))
    die('err');

?><div class="pkmats_area">

<?php
UserPriceList($qwe);
?>


</div>