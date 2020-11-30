<?php

$craftsq = qwe("
SELECT * 
FROM `craft_materials` 
WHERE `result_item_id` = '$item_id'
");
$count_mats = mysqli_num_rows($craftsq);

//echo '<p>Надо посчитать:'.$count_mats.' итемов</p>';
$MainItem = new Item;
$MainItem->getFromDB($item_id);
$crdeep = $MainItem->AllPotentialCrafts();
//printr($crdeep);
$x = 1;
$icrft = 0;
$crafta = 0;
//$crdeep = [];
if(!isset($lost))
	$lost = [];
$deep = 0;
$forlost = array(); 

///Выясняем всё что нужно для дерева рецептов.
//res($item_id, $craftsq, $x, $crafta, $icrft, $crdeep);

///Получаем рецепты в правильном порядке

$crft_str = implode(',', $crdeep);
$selcrafts = "
SELECT * from `crafts` 
WHERE `on_off` 
AND 
    `craft_id` IN ( $crft_str ) 
ORDER BY 
    `deep` DESC, `result_item_id`";
$querycrafts = qwe($selcrafts);


if($querycrafts and $querycrafts->num_rows)
{
	if(!isset($orcost))
	$orcost = PriceMode(2,$user_id)['auc_price'] ?? false;
	
	foreach($querycrafts as $key)
	{
	    $craftkeys1[$key['result_item_id']][] = $key['craft_id'];
	}
	//printr($craftkeys1);
	
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

		
	$i = 0;
}
if(isset($craftarr))
    AllOrRecurs($craftarr,$user_id);
?>