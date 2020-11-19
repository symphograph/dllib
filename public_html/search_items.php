<?php
if(empty($_GET['query'])) die();

require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php'; // Подключение к БД.
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functs.php';
$query = mysqli_real_escape_string($dbLink,$_GET['query']);
$array = array();

$qwe  = qwe("
SELECT `category`, `item_name`, `personal`, `craftable`, `item_id`, `icon`
FROM `items` 
WHERE (
`item_name` LIKE '%".$query."%')
AND `item_name` is not Null  
AND `on_off` = 1 
ORDER BY `item_name`, `craftable` DESC, `ismat` DESC, `category`, `personal`, `item_id` 
LIMIT 0, 50
");
if(!$qwe or !$qwe->num_rows)
    die();

while($data = mysqli_fetch_object($qwe))
{
    $array[] = SearchWrapVariant($data);
}

echo "['".implode("','", $array)."']";

exit();
?>