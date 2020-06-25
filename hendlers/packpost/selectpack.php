<?php

if(!isset($_POST['from_id'])) die();

$from_id = intval($_POST['from_id']);
if(!$from_id) die();

if($from_id == 100)
{
    $and = '';
}else
{
    $and = "AND pack_prices.zone_id = '$from_id'";
}

require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/functions/functs.php';

$qwe = qwe(" 
select 
pack_prices.item_id,
packs.pack_name as item_name,
pack_prices.zone_id,
packs.zone_name,
packs.pack_t_id       
FROM packs
INNER JOIN pack_prices ON pack_prices.item_id = packs.item_id
    AND  pack_prices.zone_id = packs.zone_id
INNER JOIN zones on zones.zone_id = packs.zone_id
".$and."
and pack_prices.zone_id < 30
group by pack_prices.item_id, pack_prices.zone_id
order by packs.pack_t_id, item_name
");
//SelectOpts($qwe,'item_id','item_name');
foreach ($qwe as $q)
{
    extract($q);
    if(in_array($pack_t_id,[2,3]) and $from_id == 100)
        $item_name = $item_name.' - '.$zone_name;
    ?><option value="<?php echo $item_id?>" data-id="<?php echo $zone_id?>"><?php echo $item_name?></option><?php
}
?>