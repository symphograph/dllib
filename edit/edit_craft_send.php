<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Документ без названия</title>
</head>

<body>
<?php
	require_once '../includs/ip.php';
	include_once '../functions/functs.php';
if(!$myip) exit();

	if(isset($_POST['cancel'])) {echo 'Отменено<br>'; exit();};
	
if(!empty($_POST['craft_id']))	
{
	$needs = $_POST['mater_need'];
	print_r($needs);}
	else exit();
	$result_item_name = $_POST['result_item_name'];
    $craft_id = $_POST['craft_id'];
	include "../includs/config.php";
	$result_amount = $_POST['result_amount'];
	$prof = $_POST['prof'];
	$dood_name = $_POST['dood_name'];
	$rec_name = $_POST['rec_name'];
	$labor_need = $_POST['labor_need'];
	$mins = intval($_POST['mins']);
	
	qwe("
	UPDATE `crafts` SET 
	`result_item_name` = '$result_item_name',
	`result_amount` = '$result_amount',
	`profession` = '$prof',
	`dood_name` = '$dood_name',
	`rec_name` = '$rec_name',
	`labor_need` = '$labor_need',
	`mins` = '$mins',
	`on_off` = 1 
	WHERE `craft_id` = '$craft_id'
	");

	$SPM_array = SPM_array();
	//$spm = $SPM_array[$craft_id];
	
	foreach($SPM_array as $spmcraft_id => $spm)
	{
		qwe("
		UPDATE crafts
		SET spm = '$spm'
		WHERE craft_id = '$spmcraft_id'
		");
	}

	
	foreach($needs as $mat_id => $need)
	{
		
		echo $mat_id.' '.$need.'<br>';
		qwe("
		UPDATE `craft_materials` 
		SET `mater_need`='$need'
		WHERE `item_id`='$mat_id' 
		AND `craft_id`='$craft_id'
		");
		
	}
	
	if(!empty($_POST['del'])) 
	{
	echo 'Вижу, что-то на удаление.<br>';
	$del = $_POST['del'];
		
	$dels = implode(', ',$del);
		
	qwe("
	DELETE FROM `craft_materials` 
	WHERE `craft_id`='$craft_id' 
	AND `item_id` in ($dels)
	");
		
	$q_maters = qwe("
	SELECT * FROM `craft_materials` 
	WHERE `item_id` IN ($dels)");
		
	qwe("
	UPDATE `items` 
	SET `ismat` = 0 
	WHERE `item_id` in ($dels) 
	AND `item_id` 
	NOT in (SELECT `item_id` FROM `craft_materials` where `item_id` in ($dels))
	");						  
	};
	
	if(!empty($_POST['newmat']) and !empty($_POST['newmatneed'])) 
	{   $newmats = $_POST['newmat'];
	    $newmatneed = $_POST['newmatneed'];
	    $result_item_id = $_POST['result_item_id'];
		echo 'Вижу итемы для добавления.<br>';
	    
		$nmarr = implode(', ',$newmats).'<br>';
	    echo implode(', ',$newmatneed);
	    $c = array_combine($newmats, $newmatneed);
	    print_r($c);
	    foreach($c as $id=>$newneed)
		{   
			if($id>0 and $newneed !=0){
			echo $id.' x '.$newneed.'<br>';
			qwe("INSERT INTO `craft_materials` (`craft_id`, `item_id`, `result_item_id`, `mater_need`)
			VALUES ('$craft_id','$id', '$result_item_id', '$newneed')");
			qwe("UPDATE `items` SET `ismat`= 1
		 WHERE `item_id` = '$id'");};
		};	
	 
	};
	
	if(!empty($_POST['prof']) and intval($craft_id)>0)
	{
		$prof_id = intval($_POST['prof']);
		qwe("
		UPDATE `crafts` 
		SET `prof_id` = '$prof_id', 
		`profession` = 
		(
			SELECT `profession` 
			FROM `profs` WHERE `prof_id` = '$prof_id'
		) 
		WHERE `craft_id` = '$craft_id'");
	}
	include '../functions/craftable.php';
	echo '<meta http-equiv="refresh" content="0; url=recedit.php?query='.$craft_id.'"">';
	?>
</body>
</html>