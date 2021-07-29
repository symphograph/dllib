<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST')
    die();
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$Post = file_get_contents("php://input");
$arr = [];
$qwe = qwe("
SELECT * FROM item_subgroups
WHERE visible_ui
");
foreach ($qwe as $q){
    $q = (object) $q;
    $arr[$q->sgr_id] = $q;
}
echo json_encode($arr);