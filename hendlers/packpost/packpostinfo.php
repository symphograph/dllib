<?php

if(!isset($_POST)) die();

foreach ($_POST as $k => $v)
{
    $p[$k] = intval($v);
}
$freshtime =  $freshtime ?? 0;
if($p['per'] > 130) die('>130!');
if(!$p['item_id']) die('item_id');
extract($p);

if(!isset($psiol))
    $psiol = 0;
else
    $psiol = 5;
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/usercheck.php';

$cook_settings =
    [
        'per' => $per,
        'psiol' => $psiol,
        'from_id' => $from_id,
        'to_id' => $to_id,
        'freshtime' => $freshtime,
        'item_id' => $item_id
    ];
$cooktime = time()+60*60*24*360;
setcookie("packpost",serialize($cook_settings),$cooktime,'/');

//printr($p);

$qwe = qwe("
SELECT 
fresh_data.fresh_tstart,
fresh_data.fresh_per,
fresh_data.fresh_lvl,
fresh_data.fresh_group,
fresh_data.fresh_type,
item_name,
pack_prices.valuta_id as valuta,       
pack_prices.pack_price/pack_prices.mul as pack_price
FROM
packs
INNER JOIN pack_prices ON pack_prices.item_id= packs.item_id 
AND pack_prices.zone_id = packs.zone_id
AND packs.item_id = '$item_id'
AND pack_prices.zone_id = '$from_id'
AND pack_prices.zone_to = '$to_id'
INNER JOIN zones ON zones.zone_id = pack_prices.zone_id 
INNER JOIN pack_types ON packs.pack_t_id = pack_types.pack_t_id 
INNER JOIN fresh_data ON pack_types.fresh_group = fresh_data.fresh_group  
AND zones.fresh_type = fresh_data.fresh_type
AND fresh_data.fresh_tstart = '$freshtime'
INNER JOIN items ON items.item_id = packs.item_id
");
//var_dump($qwe);
if((!$qwe) or (!$qwe->num_rows))
    die('err');
$qwe = mysqli_fetch_assoc($qwe);
extract($qwe);
?>
<div class="pinfo_row">
        <span class="pharam">Товар: [<?php echo $item_name?>]</span>
    </div><br>
<?php

echo SalaryLetter($per,$pack_price,$psiol,$fresh_per,$item_name,$valuta);

if($myip)
{
    $Factory_list = PackPercents($pack_price,$siol,$per,$fresh_per,2,1);

?>
<div class="pinfo_row">
        <span class="pharam">Исправить:</span>
    <span class="value">

        <div>
            <form id="editprice">
            <input type="number" id="newprice" name="newprice" value="<?php echo $Factory_list?>">
            <img src="img/bronze.png" width="15" height="15" alt="b">
                <input type="hidden" name="item_id" value="<?php echo $item_id?>">
                <input type="hidden" name="from_id" value="<?php echo $from_id?>">
                <input type="hidden" name="to_id" value="<?php echo $to_id?>">
                <input type="hidden" name="siol" value="<?php echo $siol?>">
                <input type="hidden" name="per" value="<?php echo $per?>">
            <button type="button" id="sendprice" class="def_button">ok</button>
            </form>

        </div>

    </span>
</div>
    <?php
}
?>
