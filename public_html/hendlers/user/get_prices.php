<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/config.php';

$User = new User();
if(!$User->byIdenty()){
    die('user');
}

$puser = $_POST['puser'] ?? 0;
$puser = intval($puser);
if(!$puser)
    die('puser');

$Puser = new User();
$Puser->byId($puser);
$prices = $Puser->getPrices($User->server_group);
if(!count($prices)){
    die('no data');
}
$cells = [];
$checked = [];
foreach ($prices as $p){
    $p = (object) $p;
    $Item = new Item();
    $Item->byId($p->item_id);
    if($User->id != $puser && $Item->isPrivate()){
        continue;
    }

    $PriceCell = new PriceCell(
        item_id:       $Item->item_id,
        grade:         $Item->basic_grade ?? 1,
        icon:          $Item->icon,
        price:         $p->auc_price,
        time:          $p->time,
        checked:       $Item->isBuyCraft,
        havingChekbox: $Item->craftable,
        isPrivate:     $Item->isPrivate(),
        tooltip:       $Item->item_name,
        item_name:     $Item->item_name
    );
    if($PriceCell->checked){
        $checked[] = $Item->item_id;
    }
    $cells[] = $PriceCell;
}


echo json_encode(['prices'=>$cells, 'checked'=>$checked]);

