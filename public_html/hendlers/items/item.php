<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST')
    die();
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$User = new User();
if(!$User->byIdenty()){
    die('user');
}


$itemId = $_POST['itemId'] ?? 0;
$itemId = intval($itemId);
if(!$itemId)
    die('itemId');


setcookie("item_id",$itemId,time()+60*60*24*360,'/');

$Item = new Item();
$Item->byId($itemId);
$crArr = $Item->getCraftResults();
$Item->craftResults = [];
foreach ($crArr as $resultId){
    $Result = new Item();
    $Result->byId($resultId);
    $Item->craftResults[] = $Result;
}

if($Item->craftable){
    $Item->getCrafts();
    if(!$Item->isCounted()){
        $Item->RecountBestCraft(1,1);
    }



    if(!isset($lost) or !count($lost)){

        if($Item->getBestCraft()){
            $Item->bestCraft->setCountedData();
        }

        $Item->crafts = initCrafts($Item->crafts, $Item);

        $Item->getAllMats();
        $Item->allMats = initAllMats($Item->allMats);

        $Item->getAllTrash();
        $Item->allTrash = initAllTrash($Item->allTrash);

    }else{
        $lost = Item::initLost($lost);
        $User->clearUCraftCache();
        $Item->lost = $lost;
        $Item->crafts = [];
    }

}
$Item->initPrice();
$Item->initValutInfo();

echo json_encode(['item'=>$Item]);

function initAllMats(array $mats) : array
{
    $allMats = [];
    foreach ($mats as $id => $need){
        $Mat = new Mat();
        $Mat->byId($id);
        $Mat->initPrice();
        $Mat->ToolTip($need,0);
        $Cubik = new Cubik(
            id: $id,
            icon: $Mat->icon,
            grade: $Mat->basic_grade,
            tooltip: $Mat->tooltip,
            value: $need
        );
        $allMats[] = $Cubik;

    }
    return $allMats;
}

function initAllTrash(array $mats) : array
{
    $allTrash = [];
    foreach ($mats as $id => $val){
        $Mat = new Mat();
        $Mat->byId($id);
        $Mat->initPrice();
        $Mat->ToolTip($val,0);
        $Cubik = new Cubik(
            id:      $id,
            icon:    $Mat->icon,
            grade:   $Mat->basic_grade,
            tooltip: $Mat->tooltip,
            value:   $val
        );
        $allTrash[] = $Cubik;
    }
    return $allTrash;
}

function initCrafts(array $arr, Item $Item) : array
{
    $crafts = ['best'=>[],'other'=>[]];
    foreach ($arr as $craft_id){
        $Craft = new Craft($craft_id);
        $CraftInfo = new CraftInfo(
            Craft: $Craft,
            Item: $Item
        );

        if(in_array($Craft->isbest,[1,2])){
            $crafts['best'] = $CraftInfo;
        }else
            $crafts['other'][] = $CraftInfo;
    }
    return $crafts;
}