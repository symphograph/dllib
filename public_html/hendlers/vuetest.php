<?php
//var_dump($_POST);
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$limit = $_POST['limit'];
$limit = intval($limit);
$qwe = qwe("SELECT * FROM items WHERE on_off AND item_id > 1000 LIMIT $limit");
if(!$qwe or !$qwe->num_rows)
    die();
foreach ($qwe as $q){
    $q = (object) $q;
    $arr[] = $q;
}

echo json_encode($arr);