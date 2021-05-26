<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$_POST = json_decode(file_get_contents('php://input'), true);
$query = $_POST['query'];
$query = mysqli_real_escape_string($dbLink,$query);
if(mb_strlen($query) < 3)
    die();
$qwe = qwe("
SELECT item_id, item_name, icon FROM items 
WHERE (item_name like '%".$query."%')
limit 10
");
if(!$qwe or !$qwe->num_rows){
    var_dump($query);
    die('nodata');
}


foreach ($qwe as $q){
    $q = (object) $q;
    $arr[] = $q;
}

echo json_encode($arr);
