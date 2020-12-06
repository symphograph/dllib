<?php
$sgr = intval($_POST['sgroup']);
if(!$sgr) die;
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}


$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die('<span style="color: red">Oh!<span>');
extract($userinfo_arr);
$user_id = $muser;

$cat_id = $_POST['categ_id'] ?? 0;
$cat_id = intval($cat_id);


$view = $_POST['view'] ?? 0;
$view = intval($view);

$qwe = qwe("
SELECT
item_categories.id as cat_id,
item_groups.id as gr_id,
item_categories.`name` as cat_name,
item_categories.item_group,
item_groups.`name` as gr_name,
item_groups.description
FROM
item_categories
INNER JOIN item_groups ON item_categories.item_group = item_groups.id
AND item_groups.sgr_id = '$sgr'
ORDER BY item_group, cat_id
");


$qwe2 = qwe("
SELECT * FROM `item_groups` 
WHERE `sgr_id` = '$sgr'
AND `visible_ui` > 0
");
?>

<?php
foreach($qwe2 as $s)
{
	extract($s);
	?><br><details open="open"><?php
	echo '<summary>'.$name.'</summary>';
	Categs($qwe,$id,$cat_id);
	?></details><?php
}
	
$chks = ['','checked'];

function Categs($qwe,$ngr_id,$ccat_id)
{
	
	?><ul><?php
	foreach($qwe as $s)
	{
		extract($s);
		if($ngr_id != $gr_id) 
			continue;
		$chk = $ctn = '';
		if($cat_id == $ccat_id)
		{
			$chk = 'checked';
			$ctn = 'class="catname"';
		}
			
		?><li><label for="<?php echo $cat_id?>" <?php echo $ctn?>><?php echo $cat_name?></label>
			<input <?php echo $chk?> type="radio" name="cat_id" id="<?php echo $cat_id?>" value="<?php echo $cat_id?>"/>
			</li><?php
	}
	?></ul><?php
}
	
?>