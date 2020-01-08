<?php /*?><meta charset="utf-8"><?php */
//var_dump($_POST);
$start = microtime(true);
require_once 'includs/ip.php';
if(!$myip) exit();
if(empty($_POST))
{
	?>
	<form method="post" action="">
	<input type="submit" name="go" value="go">	
	</form>
	<?php	
}

if(empty($_POST['go'])) exit();
include 'includs/config.php';
include 'functions/pars_functs.php';
include 'functions/filefuncts.php';
include 'functions/functs.php';
include 'functions/functions.php';
$compliteds = $wrongs = [];
$query = qwe("
SELECT `item_id`, `icon` 
FROM `items` 
WHERE /*`on_off` = 1 and*/ `md5_icon` = 'f0dc39c58c77e2212f965e918c1c2023'
AND item_id > (SELECT item_id FROM `parsed_last`)
");
if($query)
foreach($query as $mxitm)
{
	
	extract($mxitm);
	qwe("UPDATE `parsed_last` SET item_id = '$item_id'");
	
	if(in_array($icon,$compliteds) or in_array($icon,$wrongs))
		continue;

	$img = ParsIcons($item_id);
	
	
	if(!$img)
	{
		$wrongs[$item_id] = $icon;
		continue;
	}
		
	$img = mysqli_real_escape_string($dbLink,$img);
	$compliteds[$item_id] = $icon;
	qwe("UPDATE `items` SET `icon` = '$img' WHERE `item_id` = '$item_id'");
	?><img src="img/icons/50/<?php echo $img?>"><?php
	//echo $item_id.'<br>';
}
//delFolderRecurs('imgtmp');
printr($wrongs);
echo 'Время выполнения скрипта: '.(microtime(true) - $start).' сек.';


?>
