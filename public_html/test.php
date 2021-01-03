<meta charset="utf-8">
<?php
$tstart = microtime(true);

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if (!$cfg->myip)
    die('rrr');

require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/funct-obhod2.php';
$User = new User();
$User->byIdenty();

$Item = new Item();
$Item->getFromDB(16325);
$arr = $Item->craftTree();
/*
usort($arr,function($a,$b){
    return $a['deep'] - $b['deep'];
});
*/
//printr($arr);
foreach ($arr as $k=>$v)
{
    //printr($v);
    echo '<br>'.str_repeat(' - ',$v['deep']-1).$v[2];
}
die();
$qwe = qwe("SELECT item_id FROM items WHERE on_off AND item_id >= 8319 AND craftable LIMIT 1");

foreach ($qwe as $q)
{
    $item_id = $q['item_id'];
    $Item = new Item();
    $Item->getFromDB($item_id);
    //printr($Item);

    $Item->RecountBestCraft();
    ?><details><summary><?php echo $Item->name?></summary><?php
    echo ''. (microtime(true) - $tstart);
    $Item->getAllMats();
    printr($Item->allMats);

    //$Item->allMatsShow(1,10);
    echo '<br>'. (microtime(true) - $tstart);
    ?></details><?php
    // echo count($arr).'<br>';
}









die();
$Pack = new Pack();
$Pack->getFromDB(31901);
printr($Pack);


$qwe = qwe("SELECT item_id FROM items WHERE on_off AND item_id >= 32103 AND ismat LIMIT 100");
foreach ($qwe as $q)
{
    $item_id = $q['item_id'];
    $Item = new Item();
    $Item->getFromDB($item_id);
    //$arr = $Item->AllPotentialResults($item_id);
    //$str = implode(', ',$arr);
    ?><details><summary><?php echo $Item->name?></summary><?php
    printr($Item);
    ?></details><?php
   // echo count($arr).'<br>';
}



/*
function ListResult(array $arr)
{
    if(!count($arr))
        return false;
    $str = implode(', ',$arr);

    $qwe = qwe("
    SELECT * from `items`
    WHERE item_id
    IN ( $str )
    ORDER BY item_name
    ");
    foreach ($qwe as $q)
    {
        extract($q);
        echo $item_name.'<br>';
    }
}
*/
echo '<br><br>'. (microtime(true) - $tstart);
?>

