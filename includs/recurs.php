<?php

$MainItem = new Item;
$MainItem->getFromDB($item_id);
$craftkeys1 = $MainItem->CraftsByDeep();

if(!isset($lost))
	$lost = [];

$forlost = [];


if(count($craftkeys1))
{
	if(!isset($orcost))
	    $orcost = PriceMode(2,$user_id)['auc_price'] ?? false;

	$craftarr = CraftsBuffering($craftkeys1);

    if(!in_array($_SERVER['SCRIPT_NAME'],[
        '/hendlers/packs_list.php',
        '/hendlers/isbuysets.php',
        '/packres.php',
        '/hendlers/packpost/packpostmats.php',
        '/hendlers/packpost/packobj.php',
    ]))
    {

        if(count($lost)>0)
        {
            MissedList($lost);
            exit();
        }
    }
}
if(isset($craftarr))
    AllOrRecurs($craftarr,$user_id);
?>