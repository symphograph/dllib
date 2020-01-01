<?php
	///Операции с юзерскими настройками
$new = false;
if(isset($_GET['new']) and $_GET['new'] = 1) $new = true;

	$lvl = 0;
  if(isset($_POST['prof_lvl']) and ctype_digit($_POST['prof_lvl']))
	{
		//var_dump($_POST);
		$lvl = intval($_POST['prof_lvl']);
		$prof_id = intval($_POST['prof_id']);
		//$prof_name = intval($_POST['name']);
		$sqldel="DELETE FROM `user_crafts` WHERE `user_id` = '$user_id' and `isbest` < 2";
		$sql="REPLACE INTO `user_profs` 
		(`prof_id` , `prof`, `user_id`, `lvl`, `time`) 
		VALUES 
		('".$prof_id."', (SELECT `profession` FROM `profs` WHERE `prof_id` = '$prof_id'), '$user_id',  '$lvl', now())";
		qwe($sql);

			qwe($sqldel) or die(mysqli_error ($dbLink));
		$new = true;
	}
	
if (isset($_POST['chenge_best'])) 
{
	$new_best = mysqli_real_escape_string($dbLink,$_POST['chenge_best']);
	if(ctype_digit($new_best))
		{
		qwe("UPDATE `user_crafts` SET `isbest` = 0, `auc_price` = 0 
		WHERE `user_id` = '$user_id' and `item_id` in 
		(SELECT `result_item_id` FROM `crafts` 
		WHERE `on_off` > 0 and `craft_id` = '$new_best')");
		qwe("UPDATE `user_crafts` SET `isbest` = 2 
		WHERE `user_id` = '$user_id' AND `craft_id` = '$new_best'");
		$sqldel="DELETE FROM `user_crafts` WHERE `user_id` = '$user_id' AND `isbest` < 2";
		qwe($sqldel) or die(mysqli_error ($dbLink)); 
		$new = true;
		}
		
	if($new_best == 'bye' and ctype_digit($_POST['bye_price']) and $_POST['bye_price'] > 0)
		{
		$bye_price = $_POST['bye_price'];
		if(ctype_digit($_POST['bye_itemid']))$bye_itemid = $_POST['bye_itemid'];
		qwe("UPDATE `user_crafts` SET `auc_price` = '$bye_price', `isbest` = 3 
		WHERE `user_id` = '$user_id' and `item_id` = '$bye_itemid'");
		$sqldel="DELETE FROM `user_crafts` WHERE `user_id` = '$user_id' and `isbest` < 2";
		qwe($sqldel) or die(mysqli_error ($dbLink));
		$new = true;
	    }
}
	
if(isset($_POST['unset_u_craft']))
{
$sqldel="DELETE FROM `user_crafts` WHERE `user_id` = '$user_id' and `item_id` = '$item_id'";
qwe($sqldel) or die(mysqli_error ($dbLink));
$new = true;
}
if(isset($_POST['unset_all_craft']))
{
$sqldel="DELETE FROM `user_crafts` WHERE `user_id` = '$user_id'";
qwe($sqldel) or die(mysqli_error ($dbLink));
$new = true;
}
	  
?>