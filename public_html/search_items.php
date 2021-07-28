<?php
if(empty($_GET['query'])) die();
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';


$query =  "%{$_GET['query']}%";
$array = [];

$qwe  = qwe("
SELECT `category`, `item_name`, `personal`, `craftable`, `item_id`, `icon`
FROM `items` 
WHERE (
`item_name` LIKE :query)
AND `item_name` is not Null  
AND `on_off` = 1 
ORDER BY `item_name`, `craftable` DESC, `ismat` DESC, `category`, `personal`, `item_id` 
LIMIT 0, 50
",[$query]);
if(!$qwe or !$qwe->rowCount())
    die();
foreach ($qwe as $q){
    $q = (object) $q;
    $array[] = SearchWrapVariant($q);
}


echo "['".implode("','", $array)."']";

exit();
?>