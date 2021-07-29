<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST')
    die();
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$Post = file_get_contents("php://input");
$Post = json_decode($Post);

$sGrId = $Post->sGrId ?? 0;
$sGrId = intval($sGrId);
if(!$sGrId)
    die('sGrId');

$arr = [];
$qwe = qwe("
SELECT
item_categories.id as cat_id,
item_groups.id as gr_id,
item_categories.`name` as cat_name,
item_categories.item_group,
item_groups.`name` as gr_name,
item_groups.description
FROM
item_categories
INNER JOIN item_groups ON item_categories.item_group = item_groups.id
AND item_groups.sgr_id = :sGrId
ORDER BY item_group, cat_id
",['sGrId' => $sGrId]);
$arr = $qwe->fetchAll();
$groups = getGroups($sGrId);
$arr2 = [];
foreach ($groups as $k => $g){

    $g['categs'] = getCategs($arr,$g['id']);
    $arr2[$g['id']] = $g;
}

echo json_encode($arr2);

function getGroups(int $sGrId)
{
    $qwe = qwe("SELECT * FROM `item_groups` 
    WHERE sgr_id = '$sGrId'
    AND visible_ui");
    if(!$qwe or !$qwe->rowCount()){
        return [];
    }
    return $qwe->fetchAll();
}

function getCategs($arr,int $grId)
{
    $arr2 = [];
    foreach ($arr as $k=>$v){
        if($v['gr_id'] == $grId){
            $arr2[$v['cat_id']] = $v;
        }
    }
    return $arr2;
}