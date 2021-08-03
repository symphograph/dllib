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
$crafts = ['best'=>[],'other'=>[]];
if($Item->craftable){
    $Item->getCrafts();
    if(!$Item->isCounted()){
        $Item->RecountBestCraft(1,1);
    }

    if($Item->getBestCraft()){
        $Item->bestCraft->setCountedData();
    }

    if(!isset($lost) or !count($lost)){
        foreach ($Item->crafts as $craft_id){
            $Craft = new Craft($craft_id);
            $CraftInfo = new CraftInfo(
                Craft: $Craft,
                Item: $Item
            );

            $best = 'other';
            if(in_array($Craft->isbest,[1,2])){
                $crafts['best'] = $CraftInfo;
            }else
                $crafts['other'][] = $CraftInfo;
        }
        $Item->crafts = $crafts;
    }else{
        $Item->crafts = [];
    }

}
$Item->initPrice();
echo json_encode(['item'=>$Item],JSON_FORCE_OBJECT);