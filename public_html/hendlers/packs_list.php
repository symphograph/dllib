<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if(empty($_POST))
exit();

$tstart = microtime(true);

$User = new User;
if(!$User->byIdenty())
	die('<span style="color: red">Oh!<span>');
//printr($_POST);
$user_id = $User->id;

$pack_age = $_POST['pack_age'] ?? 0;
$pack_age = intval($pack_age);

$per = $_POST['per'] ?? 0;
$per = intval($per);

$side = $_POST['side'] ?? 0;
$side = intval($side);
if(!$side)
    die('side');


$siol = $_POST['siol'] ?? 0;
($siol === 'true') ? $siol = 5 : $siol = 0;

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
//printr($types);
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

if(!count($types))
	die('notypes');

$typess = implode(',',$types);

$Price = new Price(32103);
$Price->byMode();
$coalprice = $Price->price;

$Price = new Price(32106);
$Price->byMode();
$shellprice = $Price->price;

if(in_array(4,$types))
{
    if(!$coalprice)
        $lost[] = 32103;
    if(!$shellprice)
        $lost[] = 32106;
}

$packs_q = qwe("SELECT DISTINCT packs.item_id FROM `packs`
INNER JOIN items i on packs.item_id = i.item_id AND i.on_off
WHERE packs.item_id not in (SELECT `item_id` FROM `user_crafts` WHERE user_id = '$user_id')
AND `side` = '$side'
AND packs.item_id in (SELECT `item_id` FROM `packs` WHERE `pack_t_id` IN (".$typess."))
");
if($packs_q and $packs_q->num_rows)
{
    //Запускаем расчет себестоимостей.
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../functions/funct-obhod2.php';
    qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
    qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");
    foreach($packs_q as $pack)
    {
        $Item = new Item();
        $Item->getFromDB($pack['item_id']);
        $Item->RecountBestCraft();
        //CraftsObhod($pack['item_id'], $user_id);
    }
    qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
    qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");
}



if(isset($lost) and count($lost)>0)
{
	MissedList($lost);
	qwe("delete FROM user_crafts where user_id = '$user_id' AND isbest < 2");
	exit();
}


$sorts = [
	' profit DESC',
	' zone_name, profit DESC',
	' zname_to, profit DESC',
	' take DESC',
	' profitor DESC'
];
$sort = $sorts[$qsort];

$agent = get_browser(null, true);
$mobile = $agent['ismobiledevice'];

$craft_price = 1;
$TradeLvl = ProfLvl($user_id,5);

//printr($types);
/*
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
            SELECT 32103 as item_id, ROUND({$coalprice} * 0.9) as auc_price
            UNION
            SELECT 32106 as item_id, ROUND({$shellprice} * 0.9) as auc_price
            )
as `coal_price`
on coal_price.item_id = pack_prices.valuta_id
LEFT JOIN `prof_lvls` ON `prof_lvls`.`lvl` = '$TradeLvl'
INNER JOIN fresh_lvls on fresh_lvls.fresh_lvl = fresh_data.fresh_lvl
) as tmp
ORDER BY ".$sort
);
*/

$Prof = new Prof;
$Prof->InitForUser(5);


$typesStr = implode(',',$types);
$qwe = qwe("SELECT 
items.*,
pack_prices.zone_to, 
pack_prices.pack_price, 
pack_prices.valuta_id, 
pack_prices.mul, 
packs.zone_from, 
packs.pack_sname, 
pt.pack_t_id, 
pt.pack_t_name, 
pt.pass_labor,     
pt.fresh_group, 
uc.craft_price, 
uc.labor_total, 
uc.spmu
FROM packs
INNER JOIN pack_prices ON packs.item_id = pack_prices.item_id
AND side = '$side'
AND packs.zone_from = pack_prices.zone_id
AND packs.pack_t_id in ($typesStr)
INNER JOIN items ON packs.item_id = items.item_id AND items.on_off
INNER JOIN user_crafts uc on pack_prices.item_id = uc.item_id AND uc.user_id = '$User->id' and isbest
INNER JOIN pack_types pt on packs.pack_t_id = pt.pack_t_id
ORDER BY packs.item_id");

$i=0; $n=0; $open = false;
$packData = [];
foreach($qwe as $v)
{   $i++;

    $Pack = new Pack();
    $Pack->byQ($v);
    $Pack->freshGet(age: $pack_age);
    $Pack->getBestCraft();
    $Pack->bestCraft->setCountedData();
    $Pack->printRow($per ?? 130, $siol ?? 0);
    $packData[] = [
            //'data' => $Pack->printRow($per ?? 130, $siol ?? 0),
            'item_id' => $Pack->item_id,
            'salary' => $Pack->PackPrice->finalSalary,
            'goldsalary' => $Pack->PackPrice->finalGoldSalary,
            'profit' => $Pack->PackPrice->profit,
            'profitor' => $Pack->PackPrice->profitOr,
            'Pack' => $Pack,

    ];

    /*
    ?>
    <div class="pack_row<?php echo $n?>">
        <?php $Pack->printRow($per ?? 130,$siol ?? 0)?>
    </div>
    <hr>
    <?php
    */
    //printr($Pack);

    (!$n) ? $n++ : $n = 0;
}
echo json_encode($packData);

//if ($cfg->myip)
//    echo '<br><br>'. (microtime(true) - $tstart);


function ProfLvl(int $user_id, int $prof_id)
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
