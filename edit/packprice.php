<?php
if(!isset($_POST)) die();
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
if(!$myip) die('ff');
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/usercheck.php';
$p = [];
foreach ($_POST as $k => $v)
{
    $p[$k] = intval($v);
}
extract($p);
printr($p);
$newprice = $newprice/$per*130;
$newprice = round($newprice,0,2);
echo $newprice;
qwe("
UPDATE pack_prices
SET pack_price = '$newprice'
WHERE item_id = '$item_id'
AND zone_id = '$from_id'
AND zone_to = '$to_id'
");