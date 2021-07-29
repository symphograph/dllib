<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST')
    die();
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';


$search = $_POST['search'] ?? '';
if(empty($search))
    die('empty');

$search = "%{$search}%";
$arr = [];
$qwe = qwe("
SELECT * FROM items
WHERE item_name LIKE :search
AND on_off
LIMIT 0, 50
",['search' => $search]);
if (!$qwe or !$qwe->rowCount()){
    die('no results');
}
foreach ($qwe as $k => $q)
{
    $q = (object) $q;
    $arr[] = $q;
}
echo json_encode($arr);