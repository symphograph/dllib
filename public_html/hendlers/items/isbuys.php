<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/config.php';

$User = new User();
if(!$User->byIdenty()){
    die('user');
}

$checked = $_POST['checked'] ?? 0;
if(!$checked){
    die('no data');
}

if(is_array($checked)){
    qwe("DELETE FROM user_buys WHERE user_id = :user_id",['user_id'=>$User->id]);
    $checked = array_map('intval',$checked);
    foreach ($checked as $ch){
        $Item = new Item();
        $Item->byId($ch);
        $Item->setAsBuy();
    }
    $User->clearUCraftCache();
}

/*
if(is_array($prices)){

    foreach ($prices as $p){
        $p = (object) $p;
        $Price = new Price($p->item_id);
        $price = intval($p->price,0);
        if(!$price){
            continue;
        }

        $Price->insert($price,0);
    }
    $User->clearUCraftCache();
}
*/

echo json_encode([]);

