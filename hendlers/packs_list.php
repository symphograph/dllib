<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
if(empty($_POST))
exit();

include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
include '../functions/pack_functs.php';
include '../functions/functs.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';
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
$qsort = intval($siol);
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

$coalprice = PriceMode(32103,$user_id);
if($coalprice)
{
	$coalprice = $coalprice['auc_price'];
	/*
	$coalprice = intval($coalprice);
	if($coalprice > 0)
	qwe("REPLACE INTO `prices` (`user_id`, `item_id`, `server_group`, `auc_price`, `time`) 
	VALUES
	('$user_id','32103', '$server_group', '$coalprice', '2018-02-02 00:00:00')
	");
	*/
}

$shellprice = PriceMode(32106,$user_id);
if($shellprice)
{
	$shellprice = $shellprice['auc_price'];
	/*
	$shellprice = intval($shellprice);
	if($shellprice > 0)
	qwe("REPLACE INTO `prices` (`user_id`, `item_id`, `server_group`, `auc_price`, `time`) 
	VALUES
	('$user_id','32106', '$server_group', '$shellprice', '2018-02-02 00:00:00')
	");
	*/
}
include '../cat-funcs.php';
include '../edit/funct-obhod2.php';
if(in_array(4,$types))
{
	if(!$coalprice or !$shellprice) 
	{
		MissedList([32103,32106]);
		echo '<br><hr>';
		//die('<h2>Настройте цену растворов</h2>');
	}
		
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
	exit();
}


$sorts = [
	'profit DESC',
	'zone_name, profit DESC',
	'zname_to, profit DESC',
	'fresh_price DESC',
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
SELECT *,
ROUND(profit/labor_all,0) as profitor
FROM
(
SELECT
packjoin.item_id,
items.item_name,
items.icon,
packjoin.side,
packjoin.zone_id,
packjoin.pack_t_id,
packjoin.pack_type,
packjoin.zone_name,
packjoin.zone_to,
(SELECT zone_name FROM zones WHERE zone_id = zone_to) as zname_to,
fresh_lvls.fresh_name,
packjoin.db_price,
packjoin.quantity,
packjoin.pack_sname,
packjoin.`pass_labor2`,
ROUND(packjoin.`pass_labor2` + user_crafts.labor_total,0) as labor_all,
IF(packjoin.pack_t_id = 6,0,fresh_data.fresh_per) as fresh_per,
packjoin.valuta_id,
ROUND(packjoin.quantity*(IF(packjoin.valuta_id = 500,1,coal_price.auc_price))*IF(packjoin.valuta_id = 500,1,0.9)) as fresh_price,
user_crafts.craft_price,
user_crafts.labor_total,
ROUND(packjoin.quantity*(IF(packjoin.valuta_id = 500,1,coal_price.auc_price))*IF(packjoin.valuta_id = 500,1,0.9)) - user_crafts.craft_price as profit
FROM
fresh_data
INNER JOIN 
(
	SELECT pack_prices.item_id,  
	pack_prices.zone_to,
	zones.zone_id,
	zones.side,
	zones.zone_name,
	fresh_data.fresh_lvl,
	packs.pack_t_id,
	fresh_data.fresh_type,
	packs.pack_type,
	packs.pack_sname,
	pack_prices.valuta_id,
	fresh_data.fresh_tstart,
	fresh_data.fresh_tstop,
	pack_types.pass_labor,
	round(`pass_labor` * (100 - IFNULL(`save_or`,0)) / 100,0) AS `pass_labor2`,
	pack_prices.pack_price as db_price,
	ROUND(ROUND(pack_prices.pack_price/mul + pack_prices.pack_price/mul * IF(packs.pack_t_id = 6,1,`fresh_data`.`fresh_per`)/100)/130*IF(`pack_prices`.`valuta_id` = 500,".($per+$siol).",130)) as `quantity`
	FROM `fresh_data`
	INNER JOIN zones ON zones.fresh_type = `fresh_data`.`fresh_type` AND `zones`.`side` = '$side'
	INNER JOIN packs ON packs.zone_id = zones.zone_id AND `packs`.`pack_t_id` IN (".$typess.")
	INNER JOIN pack_prices ON pack_prices.zone_id = zones.zone_id AND pack_prices.item_id = packs.item_id
	INNER JOIN pack_types ON pack_types.pack_t_id = packs.pack_t_id
	LEFT JOIN `prof_lvls` ON `prof_lvls`.`lvl` = '$TradeLvl'
	WHERE ('$pack_age' BETWEEN fresh_data.fresh_tstart AND fresh_data.fresh_tstop-1)
) as packjoin
ON fresh_data.fresh_type = packjoin.fresh_type AND fresh_data.fresh_lvl = packjoin.fresh_lvl
INNER JOIN items ON items.item_id = packjoin.item_id AND items.on_off = 1
INNER JOIN fresh_lvls ON fresh_lvls.fresh_lvl = packjoin.fresh_lvl
LEFT JOIN user_crafts ON user_crafts.item_id = items.item_id AND user_crafts.isbest > 0 AND `user_id` = '$user_id'
LEFT JOIN (
			SELECT 32103 as item_id, '$coalprice' as auc_price
			UNION
			SELECT 32106 as item_id, '$shellprice' as auc_price
			) 
as `coal_price`
on coal_price.item_id = packjoin.valuta_id
) as `tttmp`
ORDER BY ".$sort);
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
	if($valuta != 500)
	 $price = $v['quantity'];
	else
	$price = $v['fresh_price'];
	$profit = $v['profit'];
	$zname_from = $v['zone_name'];
	$zname_to = $v['zname_to'];

	$price_1 = price_str($price,$valuta);
	$profit_1 = price_str($profit,500);
 
 	$labor_all = $v['labor_all'];
 	$ProfitOr = $v['profitor'];
 	//var_dump($ProfitOr);
 	$ProfitOr_1 = esyprice($ProfitOr,10,1);
	//$viruch = '<div class="itemprompt" data-title="Выручка">';
	$price_row = '<div class="colz">'.$zname_to.'</div>'.'<div class="colz">'.$price_1.'</div>'.'<div class="colz">'.$profit_1.'</div>';


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
	//$pack_name = $pack_name.'<br><span class="zname">'.$zname_from.'</span>';
	$zone_name = $v['zone_name'];
	 
	 
	 if (false): 
		if($zone_name == $zone_name2 /*and $i<$numrows*/)
			
			{ 
				if($open) echo '</div><hr>';
				?>
				<div class="pack_row">
	    			<div class="zone_row"><?php echo $zone_name;?></div>
	    		</div>
	    		<div class="zone_area">
		    	<?php
			    $open = true;
			}
	 endif; 
	    $item_link= str_replace(" ","+",$pack_name);
	 	?>
		<div class="pack_row<?php echo $n?>">
			<div class="piconandpname">
				
			<?php
			if($device_type != 'Desktop')
				$hrefactive = 'onClick="return false;"';
 			else 
				$hrefactive = '';
			?>
			
						<div itid="<?php echo $item_id?>" id="<?php echo $item_id.'_'.$zone_to?>" class="pack_icon" style="background-image: url(img/icons/50/<?php echo $v['icon']?>.png)">
						
				
						<div class="itdigp"><?php echo $fres_per;?>%</div>
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
				<div class="pprice"><?php echo $price_1?></div>
				<div class="pprice"><?php echo $profit_1.'<br>'.$ProfitOr_1.'/'.$imgor?></div>
			</div>
		</div>
		<hr>
		<?php
		//echo $price_row;
	 
	    //echo '</div>';
		//if($open and $i == $numrows)
		//echo '</div>';
		$zone_name2 = $zone_name;
	 	$n++;
		if($n==2) $n=0;
		//$price_1=$price_2=$price_3='';
}
	echo '<div>';
	
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
