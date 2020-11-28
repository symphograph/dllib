<meta charset="utf-8">
<?php
$tstart = microtime(true);
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
if(!$myip) exit();
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functs.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';

$Craft = new Craft(8000266);
$mats = $Craft->mats;
foreach ($mats as $mat)
{
    $mat->Cubik();
    //printr($mat);
}





die();
$qwe = qwe("SELECT item_id FROM items WHERE on_off AND item_id >= 32103 AND ismat LIMIT 100");
foreach ($qwe as $q)
{
    $item_id = $q['item_id'];
    $Item = new Item($item_id);
    $arr = $Item->AllPotentialResults($item_id);
    $str = implode(', ',$arr);
    ?><details><summary><?php echo $Item->item_name?></summary><?php
    ListResult($arr);
    ?></details><?php
    echo count($arr).'<br>';
}




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

echo '<br><br>'. (microtime(true) - $tstart);
?>

