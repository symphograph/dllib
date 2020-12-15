<?php

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}

$vals = ['false','true'];
$val = $_POST['value'] ?? 0;

if(!in_array($val,$vals)) 
	die('val');
	
$val = array_search($val,$vals);

$item_id = $_POST['item_id'] ?? 0;
$item_id = intval($item_id);
if($item_id == 0) exit('id');




$User = new User();
if(!$User->byIdenty())
    die('<span style="color: red">Oh!<span>');

$user_id = $User->id;
if($val)
{
	$auc_price = PriceMode($item_id)['auc_price'] ?? false;
	if(!$auc_price) die('<span style="color:red">Не нашел цену!</span>');
	

	$craft_id = BestCraftForItem($User->id,$item_id);
	if(!$craft_id)
	{
		require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/funct-obhod2.php';
        require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/cat-funcs.php';
        $Item = new Item();
        $Item->getFromDB($item_id);
        $Item->RecountBestCraft();
		$craft_id = BestCraftForItem($User->id,$item_id);
		if(!$craft_id) die('craft_error');
    }

    qwe("
    UPDATE `user_crafts`
    SET `isbest` = 3,
    auc_price = '$auc_price'		
    WHERE `user_id` = '$User->id'
    AND `craft_id` = '$craft_id'
    ");

    qwe("
    REPLACE INTO user_buys
    (user_id, item_id)
    values 
    ('$User->id', '$item_id')
    ");
	qwe("
	DELETE FROM `user_crafts`
	WHERE `user_id` = '$User->id' 
	AND `isbest` < 2
	");
	
	qwe("
	INSERT IGNORE prices
	(item_id, user_id, auc_price, server_group, time)
	VALUES
	('$item_id','$User->id','$auc_price','$User->server_group',NOW())
	");
	echo 'ok';

}
else
{
	qwe("
	DELETE FROM `user_crafts`
	WHERE `user_id` = '$User->id' 
	AND (`item_id` = '$item_id' OR `isbest` < 2)
	");

    qwe("DELETE FROM user_buys
    WHERE user_id = '$User->id'
    AND item_id = '$item_id'
    ");

	echo 'ok';
}