<meta charset="utf-8">
<?php
$tstart = microtime(true);

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if (!$cfg->myip)
    die();
$User = new User();
$Price = new Price;
$Price->withFrends(1);







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

