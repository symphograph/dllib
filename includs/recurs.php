<?php

$craftsq = qwe("
SELECT * 
FROM `craft_materials` 
WHERE `result_item_id` = '$item_id'
");
$count_mats = mysqli_num_rows($craftsq);

//echo '<p>Надо посчитать:'.$count_mats.' итемов</p>';

$x = 1;
$icrft = 0;
$crafta = 0;
$crafts = [];
$crdeep = [];
if(!isset($lost))
	$lost = [];
$deep = 0;
$forlost = array(); 
$crftorder[] = $item_id;
	///Выясняем всё что нужно для дерева рецептов.
	res($item_id, $craftsq, $x, $crafta, $icrft, $crdeep, $crftorder);

   ///Массив необходимого получен. Запрашиваем рецепты, которые ещё не считали:
	$selcrafts = "
	SELECT * from `crafts` 
	WHERE `on_off` = 1  
	AND 
		`result_item_id` IN (".(implode(', ', $crdeep)).") 
	ORDER BY 
		`deep` DESC, `result_item_id`";
	$querycrafts = qwe($selcrafts);
	//echo implode(', ', $crdeep); exit();
	$userbest =array();
    $b = mysqli_num_rows($querycrafts);
/*
	$selucrafts = "SELECT * FROM `user_crafts` WHERE `user_id` = '$user_id' and `isbest` > 1";
	$quucrafts = qwe($selucrafts);

	foreach($quucrafts as $ucr)
	{
		$userbest[$ucr['craft_id']] = $ucr['isbest'] ;
		//$userbestaucprice[$ucr['craft_id']] =  $ucr['auc_price'];
		//echo $ucr['craft_id'];
	}
	//print_r($userbest); exit();
*/
//Если есть непосчитанные рецепты, считаем
if($b > 0)
{
	if(!isset($orcost))
	$orcost = PriceMode(2,$user_id)['auc_price'] ?? false;
	
	foreach($querycrafts as $key){	
	$craftkeys1[$key['result_item_id']][] = $key['craft_id'];
	}
	//printr($craftkeys1);
	
	$craftarr = CraftsBuffering($craftkeys1);
	


    if(!in_array($_SERVER['SCRIPT_NAME'],['/hendlers/packs_list.php','/hendlers/isbuysets.php','/packres.php']))
    {

        if(count($lost)>0)
        {
            MissedList($lost);
            exit();
        }
    }

		
	$i = 0;
}
//if(isset($craftarr))
    AllOrRecurs($craftarr,$user_id);
?>