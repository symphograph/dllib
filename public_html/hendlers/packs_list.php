<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
if(empty($_POST))
exit();

require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functs.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';
$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die('<span style="color: red">Oh!<span>');


//die;
extract($userinfo_arr);
$user_id = $muser;

$pack_age = $_POST['pack_age'] ?? 0;
$pack_age = intval($pack_age);
$per = $_POST['perc'] ?? 0;
$per = intval($per);
$side = $_POST['side'] ?? 0;
$side = intval($side);
$siol = $_POST['siol'] ?? 0;
//$qsort = intval($siol);
$side = intval($side);
$qsort = $_POST['sort'] ?? 0;
$qsort = intval($qsort);

$types = [];


if(isset($_POST['type']))
{
	foreach($_POST['type'] as $tk => $tv)
	{
		$tk = intval($tk);
		$types[$tk] = intval($tv);
	}	
}

$pack_settings =
[
    'perc' => $per,
    //'siol' => $siol,
    'side' => $side,
    'type' => $types,
    'pack_age' => $pack_age,
    'sort' => $qsort
];
$cooktime = time()+60*60*24*360;
setcookie("pack_settings",serialize($pack_settings),$cooktime,'/');

if(!count($types)>0)
	die('<h2>Не вижу вид паков</h2>');

$typess = implode(',',$types);

$coalprice = PriceMode(32103,$user_id) ?? 0;
if($coalprice)
{
	$coalprice = $coalprice['auc_price'];
}

$shellprice = PriceMode(32106,$user_id) ?? 0;
if($shellprice)
{
	$shellprice = $shellprice['auc_price'];
}
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/cat-funcs.php';
include $_SERVER['DOCUMENT_ROOT'].'/edit/funct-obhod2.php';
if(in_array(4,$types))
{
        if(!$coalprice)
            $lost[] = 32103;
        if(!$shellprice)
            $lost[] = 32106;
}


	
//Запускаем расчет себестоимостей.

//$server = ServerInfo($user_id, 'server');
$prof_q = qwe("SELECT * FROM `user_profs` where `user_id` ='$user_id'");
$packs_q = qwe("SELECT DISTINCT `item_id` FROM `packs` 
WHERE `item_id` not in (SELECT `item_id` FROM `user_crafts` WHERE user_id = '$user_id')
AND `side` = '$side'
AND `item_id` in (SELECT `item_id` FROM `packs` WHERE `pack_t_id` IN (".$typess."))
");
//if(mysqli_num_rows($packs_q)>0)
//echo 'Надо подождать';

qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");
foreach($packs_q as $pack)
{
	$itemq = $item_id = $pack['item_id'];
	CraftsObhod($item_id,$dbLink,$user_id,$server_group,$server,$prof_q);

	unset($total, $itog, $craft_id, $rec_name, $item_id, $forlostnames, $orcost, $repprice, $honorprice, $dzprice, $soverprice, $mat_deep,
		$crafts, $crdeep, $deeptmp, $craftsq, $icrft,$crftorder,$craftarr);
}
qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");

if(isset($lost) and count($lost)>0)
{

	MissedList($lost);
	qwe("delete FROM user_crafts where user_id = '$user_id' AND isbest < 2");
	exit();
}


$sorts = [
	'profit DESC',
	'zone_name, profit DESC',
	'zname_to, profit DESC',
	'take DESC',
	'profitor DESC'
];
$sort = $sorts[$qsort];

$agent = get_browser(null, true);
$mobile = $agent['ismobiledevice'];
$imgor = '<img src="../img/icons/50/2.png" width="15px" height="15px"/>';
?>

<div class="packs_area_t1">
		
<?php
$craft_price = 1;
$TradeLvl = ProfLvl($user_id,5);


$query = qwe("
SELECT *, take*coal_mul - craft_price as profit,
ROUND((take*coal_mul - craft_price)/labor_all) as profitor
FROM
(SELECT 
items.item_id,
items.icon,
IFNULL(packs.pack_sname,items.item_name) as item_name,
pack_types.pack_t_name as pack_type,
zs_from.zone_name,
zones.zone_name as zname_to,
pack_prices.zone_to,
round(pack_prices.pack_price/mul,0) as pack_price,
user_crafts.craft_price,
pack_prices.valuta_id,
fresh_data.fresh_per,
fresh_lvls.fresh_name,
round(
    round(pack_price/130*100)
        *if(packs.pack_t_id=4,1,(1+".($siol/100)."))
        *$per/100
        *(1+fresh_data.fresh_per/100)
        *1.02
        )
 as take,
 if(packs.pack_t_id=4,round(coal_price.auc_price/100),1) as coal_mul,
round(`pass_labor` * (100 - IFNULL(`save_or`,0)) / 100 + user_crafts.labor_total) AS `labor_all`
FROM pack_prices 
INNER JOIN items 
	on pack_prices.item_id = items.item_id
	AND items.on_off 
INNER JOIN packs ON pack_prices.item_id = packs.item_id 
	AND pack_prices.zone_id = packs.zone_id
INNER JOIN pack_types ON packs.pack_t_id = pack_types.pack_t_id 
	AND pack_types.pack_t_id IN (".implode(',',$types).")
INNER JOIN (SELECT * FROM zones) as zs_from 
	ON zs_from.zone_id = pack_prices.zone_id
	AND zs_from.side = '$side'
INNER JOIN zones ON zones.zone_id = pack_prices.zone_to
INNER JOIN fresh_data ON fresh_data.fresh_type = zs_from.fresh_type AND pack_types.fresh_group = fresh_data.fresh_group
 AND '$pack_age' between fresh_data.fresh_tstart and fresh_data.fresh_tstop 
LEFT JOIN user_crafts ON user_crafts.user_id = '$user_id' AND pack_prices.item_id = user_crafts.item_id AND user_crafts.isbest
LEFT JOIN (
			SELECT 32103 as item_id, ROUND($coalprice*0.9) as auc_price
			UNION
			SELECT 32106 as item_id, ROUND($shellprice*0.9) as auc_price
			) 
as `coal_price`
on coal_price.item_id = pack_prices.valuta_id
LEFT JOIN `prof_lvls` ON `prof_lvls`.`lvl` = '$TradeLvl'
INNER JOIN fresh_lvls on fresh_lvls.fresh_lvl = fresh_data.fresh_lvl
) as tmp
ORDER BY ".$sort
);

$zone_name2 = '';
$numrows = mysqli_num_rows($query);
$i=0; $n=0; $open = false;
$agent = get_browser(null, true);
extract($agent);
foreach($query as $v)
{   $i++;

	$craft_price = 0;
 	$zone_to = $v['zone_to'];
	$item_id = $v['item_id'];
	$freshency = $v['fresh_name'];
	$fres_per = $v['fresh_per'];
	$valuta = $v['valuta_id'];
    $pack_price = $v['pack_price'];
    $fresh_per = $v['fresh_per'];

	if($valuta != 500)
	    $price = round($v['take']/100);
	else
	    $price = $v['take'];

	$profit = $v['profit'];
	$zname_from = $v['zone_name'];
	$zname_to = $v['zname_to'];

	$price_1 = price_str($price,$valuta);
	$profit_1 = price_str($profit,500);
 
 	$labor_all = $v['labor_all'];
 	$ProfitOr = $v['profitor'];
 	//var_dump($ProfitOr);
 	$ProfitOr_1 = esyprice($ProfitOr,10,1);



	$pack_type = $v['pack_type'];
	$pack_name = $v['pack_sname'] ?? $v['item_name'];
	if(preg_match('/Груз компоста/',$pack_name))
	$pack_name = 'Груз компоста';
	if(preg_match('/Груз зрелого сыра/',$pack_name))
	$pack_name = 'Груз сыра';
	if(preg_match('/Груз домашней наливки/',$pack_name))
	$pack_name = 'Груз наливки';
	if(preg_match('/Груз меда/',$pack_name))
	$pack_name = 'Груз меда';
	if($pack_name == 'Вяленые припасы Заболоченных низин')
	$pack_name = 'Вяленые припасы';

	$zone_name = $v['zone_name'];

    if($device_type != 'Desktop')
        $hrefactive = 'onClick="return false;"';
    else
        $hrefactive = '';
    ?>

    <div class="pack_row<?php echo $n?>">
        <div class="piconandpname">
            <div itid="<?php echo $item_id?>" id="<?php echo $item_id.'_'.$zone_to?>" class="pack_icon" style="background-image: url(img/icons/50/<?php echo $v['icon']?>.png)">
                <div class="itdigp"><?php echo $fresh_per;?>%</div>
            </div>

            <div id="pmats_<?php echo $item_id?>" class="pkmats_area">
                <?php //PackMatsDisplay($item_id)?>
            </div>

            <div class="pack_name">
                <div class="pack_mname"><b><?php echo $pack_name;?></b></div>

                <div class="znames">
                    <div class="znamesrows">
                        <div class="zname"></div>
                        <div class="zname"><?php echo $zname_from?></div>
                        <div class="zname"></div>
                    </div>
                    <div class="znamesrows">
                        <div class="zname2"></div>
                        <div class="zname2"></div>
                        <div class="zname2"><?php echo $zname_to?></div>
                    </div>
                </div>
            </div>


        </div>
        <div class="pprices">
            <?php $tttip = SalaryLetter($per,$pack_price,$siol,$fresh_per,$item_id,$valuta); ?>
            <div class="pprice" data-tooltip="<?php echo htmlspecialchars($tttip);?>">
                <?php echo $price_1?>
                <a href="/packpost.php?item_id=<?php echo $item_id?>">
                    <img width="15px" src="../img/icons/50/quest/icon_item_quest023.png"/>
                </a>
            </div>
            <div class="pprice"><?php echo $profit_1.'<br>'.$ProfitOr_1.'/'.$imgor?></div>
        </div>
    </div>

<hr>
<?php
$zone_name2 = $zone_name;

if($n == 0)
    $n++;
else
    $n = 0;
}
?>

<div>
<?php
function ProfLvl($user_id,$prof_id)
{
	$qwe = qwe("
	SELECT * FROM user_profs 
	WHERE user_id = '$user_id'
	AND prof_id = '$prof_id'
	");
	if(!$qwe or $qwe->num_rows == 0)
		return 0;
	
	$qwe = mysqli_fetch_assoc($qwe);
	return $qwe['lvl'];
}
?>
