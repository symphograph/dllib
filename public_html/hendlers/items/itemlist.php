<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST')
    die();
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$Post = file_get_contents("php://input");
$Post = json_decode($Post);

$curCat = $Post->curCat ?? 0;
$curCat = intval($curCat);
if(!$curCat)
    die('curCat');

$arr = [];
$qwe = qwe("
SELECT * FROM items
WHERE categ_id = :curCat
AND on_off
",['curCat' => $curCat]);
$arr = $qwe->fetchAll();
echo json_encode($arr);