<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$_POST = json_decode(file_get_contents('php://input'), true);
$query = "%{$_POST['query']}%";

if(mb_strlen($query) < 3)
    die();
$qwe = qwe("
SELECT item_id, item_name, icon FROM items 
WHERE (item_name like :query)
limit 10
",['query'=> $query]);
if(!$qwe or !$qwe->rowCount()){
    var_dump($query);
    die('nodata');
}


foreach ($qwe as $q){
    $q = (object) $q;
    $arr[] = $q;
}

echo json_encode($arr);
