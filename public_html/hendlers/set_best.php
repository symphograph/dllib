<?php
$craft_id = $_POST['craft_id'] ?? $_GET['craft_id'] ?? 0;
$craft_id = intval($craft_id);
if(!$craft_id) exit();

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}

//var_dump($_POST);

$User = new User;
if(!$User->byIdenty())
	die('<span style="color: red">Oh!<span>');

$user_id = $User->id;
$item_id = ResultItemId($craft_id);
$isbest = isBest($craft_id);
if($isbest)
{
	qwe("
	DELETE FROM `user_crafts` 
	WHERE `user_id` = '$user_id' AND
	`craft_id` ='$craft_id'
	");

}else
{
	qwe("UPDATE `user_crafts`
	SET `isbest` = 2
	WHERE `craft_id` = '$craft_id'
	AND `user_id` = '$user_id'
	");	
}



qwe("
    DELETE FROM `user_crafts` 
    WHERE `user_id` = '$user_id' 
      AND
        (`isbest` <2
            OR 
            (`item_id` = '$item_id' AND `craft_id` !='$craft_id')
        )");

qwe("UPDATE `user_crafts` SET `craft_price` = NULL WHERE `user_id` = '$user_id'");


function isBest($craft_id)
{
	global $user_id;
	$qwe = qwe("
        SELECT `isbest` FROM `user_crafts`
        WHERE `craft_id` = '$craft_id'
        AND `user_id` = '$user_id'
        ");
	if(!$qwe or !$qwe->num_rows)
	    return false;

	$q = mysqli_fetch_object($qwe);
	return $q->isbest;
}
?>