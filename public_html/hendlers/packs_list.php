<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
if(empty($_POST))
exit();

$tstart = microtime(true);

$User = new User;
if(!$User->byIdenty())
	die('<span style="color: red">Oh!<span>');

$user_id = $User->id;

$pack_age = $_POST['pack_age'] ?? 0;
$pack_age = intval($pack_age);

$condition = $_POST['condition'] ?? 0;
$qcondition = intval($condition);

$per = $_POST['per'] ?? 0;
$per = intval($per);

$side = $_POST['side'] ?? 0;
$side = intval($side);
if(!$side)
    die('side');


$siol = $_POST['siol'] ?? 0;
($siol === 'true') ? $siol = 5 : $siol = 0;

$types = [];
if(isset($_POST['type']) and is_array($_POST['type']))
{
	foreach($_POST['type'] as $tk => $tv)
	{
		$tk = intval($tk);
		$types[$tk] = intval($tv);
	}	
}

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
WHERE packs.item_id not in (SELECT `item_id` FROM `user_crafts` WHERE user_id = '$User->id')
AND `side` = '$side'
AND packs.item_id in (SELECT `item_id` FROM `packs` WHERE `pack_t_id` IN (".$typess."))
");
if($packs_q and $packs_q->rowCount())
{
    //Запускаем расчет себестоимостей.
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../functions/funct-obhod2.php';
    qwe("DELETE FROM craft_buffer WHERE `user_id` = '$User->id'");
    qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$User->id'");
    foreach($packs_q as $pack)
    {
        $Item = new Item();
        $Item->byId($pack['item_id']);
        $Item->RecountBestCraft();
    }
    qwe("DELETE FROM craft_buffer WHERE `user_id` = '$User->id'");
    qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$User->id'");
}



if(isset($lost) and count($lost)>0)
{
    ob_start();
    $lost = MissedList($lost);
    ob_clean();
	qwe("delete FROM user_crafts where user_id = '$User->id' AND isbest < 2");
	$ll = [];
	$lost = array_unique($lost);
	foreach ($lost as $l){
	    $Litem = new Item();
	    $Litem->byId($l);
	    $ll[] = $Litem;
    }
	echo json_encode(['lost'=>$ll]);
	exit();
}

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
uc.spmu,
ft.fperdata,
ft.condType       
FROM packs
INNER JOIN pack_prices ON packs.item_id = pack_prices.item_id
AND side = '$side'
AND packs.zone_from = pack_prices.zone_id
AND packs.pack_t_id in ($typesStr)
INNER JOIN items ON packs.item_id = items.item_id AND items.on_off
INNER JOIN user_crafts uc on pack_prices.item_id = uc.item_id AND uc.user_id = '$User->id' and isbest
INNER JOIN pack_types pt on packs.pack_t_id = pt.pack_t_id
INNER JOIN fresh_types ft on packs.fresh_id = ft.id
ORDER BY packs.item_id");
if(!$qwe or !$qwe->rowCount()){
    die('no data');
}

$packData = [];
foreach($qwe as $v)
{
    $v = (object) $v;
    $Pack = new Pack();
    $Pack->byQ($v);
    $Pack->initPrice(
        per: $per ?? 130,
        siol:$siol ?? 0,
        quality: $condition
    );
    $packData[] = [
        'item_id' => $Pack->item_id,
        'salary' => $Pack->PackProfit->finalSalary,
        'goldsalary' => $Pack->PackProfit->finalGoldSalary,
        'profit' => $Pack->PackProfit->profit,
        'profitor' => $Pack->PackProfit->profitOr,
        'Pack' => $Pack,
    ];
    //printr($Pack);
}
echo json_encode($packData);