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

if(!isset($siol))
    $siol = 0;
else
    $siol = 5;

require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/functions/functs.php';
//printr($p);

$qwe = qwe("
SELECT 
fresh_data.fresh_tstart,
fresh_data.fresh_per,
fresh_data.fresh_lvl,
fresh_data.fresh_group,
fresh_data.fresh_type,
item_name,
round(pack_prices.pack_price/130*100,0) as pack_price
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


$salary = $pack_price*(1+$siol/100);
$salary = $salary*($per/100);
$Factory_list = $salary;
$salary = $salary*(1+$fresh_per/100);
$salary = $salary*1.02;
$salary = round($salary,0);

$salary = esyprice($salary);
$pack_price = esyprice($pack_price);
$Factory_list = round($Factory_list,0);
$Factory_list = esyprice($Factory_list);
$freguency = 100+$fresh_per;


?>
<div class="pinfo_row">
    <span class="pharam">Товар: [<?php echo $item_name?>]</span>
</div><br>
<div class="pinfo_row">
    <span class="pharam"><b>Фактическая выручка</b></span>
    <span class="value" data-tooltip="Сколько вы получите из письма"><b><?php echo $salary?></b></span>
</div>
<hr><br>
<div class="pinfo_row">
    <span class="pharam">Оновная плата</span>
    <span class="value" data-tooltip="Чистыми без всего при 100%"><?php echo $pack_price?></span>
</div>

<div class="pinfo_row">
    <span class="pharam">Льгота</span>
    <span class="value" data-tooltip="Сиоль"><?php echo $siol?>%</span>
</div>
<div class="pinfo_row">
    <span class="pharam">Ставка</span>
    <span class="value" data-tooltip="Текущий процент у торговца"><?php echo $per?>%</span>
</div>
<div class="pinfo_row">
    <span class="pharam">Срок годности</span>
    <span class="value" data-tooltip="Свежесть"><?php echo $freguency?>%</span>
</div>
<div class="pinfo_row">
    <span class="pharam">Дополнительная надбавка</span>
    <span class="value">2%</span>
</div>
<hr>
<div class="pinfo_row">
    <span class="pharam">В списке фактории</span>
    <span class="value" data-tooltip="Отображается в списке цен в фактории"><?php echo $Factory_list?></span>
</div>
<?php
if($myip)
{
?>
<div class="pinfo_row">
        <span class="pharam">Поправьте, если неверно.</span>
    <span class="value">

        <div>
            <form id="editprice">
            <input type="number" id="newprice" name="newprice">
            <img src="img/bronze.png" width="15" height="15" alt="b">
            <button type="button" id="sendprice" class="def_button">ok</button>
            </form>
            <br><span style="color: red">Настройки сиоли и процента должны совпадать!</span>
        </div>

    </span>
</div>
    <?php
}
?>
