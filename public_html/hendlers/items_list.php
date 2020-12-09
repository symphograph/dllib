<?php
//var_dump($_POST);
$cat_id = intval($_POST['cat_id']) ?? 0;
if(!$cat_id)
{
	$squery = $_POST['squery'] ?? '';
	if(empty($squery)) die;
}
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}

$User = new User();
if(!$User->byIdenty())
	die('<span style="color: red">Oh!<span>');


$view = $_POST['view'] ?? 0;
$view = intval($view);
$and = "AND `items`.`categ_id` = '$cat_id'";

if(!$cat_id)
{
    if(!isset($dbLink))
        dbconnect();
	$squery = mysqli_real_escape_string($dbLink,$squery);
	$and = "AND `items`.`item_name` LIKE '%".$squery."%'";
	$view = 'list';
}
$sql = "
SELECT
items.item_id,
items.item_name,
items.craftable,
items.ismat,
items.basic_grade,
items.icon,
items.categ_id,
items.personal,
items.md5_icon,
item_categories.item_group,
item_categories.`name` as category,
item_groups.`name` as gr_name,
item_subgroups.sgr_id,
item_subgroups.sgr_name
FROM
items
INNER JOIN item_categories ON items.categ_id = `item_categories`.`id`
AND `items`.`on_off` = 1 $and
INNER JOIN `item_groups` ON `item_categories`.`item_group` = item_groups.id
INNER JOIN item_subgroups ON item_groups.sgr_id = item_subgroups.sgr_id
ORDER BY `item_name`
";

$qwe = qwe($sql);
if(!$qwe or !$qwe->num_rows)
 die('Ничего не найдено');
foreach($qwe as $q)
{
	$sgr_id = $q['sgr_id'];
	$cat_id = $q['categ_id'];
	break;
}

?>
<input type="hidden" name="last_sgr_id" value="<?php echo $sgr_id?>">
<input type="hidden" name="last_categ_id" value="<?php echo $cat_id?>">
<?php

if(!$view)
	Kvadratikami($qwe);
else
	Strokami($qwe);

function Kvadratikami($qwe)
{
	?><div class="items_bar"><?php
	foreach($qwe as $q)
	{
		$q = (object) $q;
	
		?>
			<div class="itim"   id="itim_<?php echo $q->item_id?>" style="background-image: url(/img/icons/50/<?php echo $q->icon?>.png?ver=<?php echo $q->md5_icon?>)">
				<div class="grade" 
					 data-tooltip="
					<?php 
						echo $q->item_name;
						if($q->personal)
							echo '<br><span class=comdate>Персональный</span>';
						if($q->craftable)
							echo '<br><span class=comdate>Есть рецепт</span>';
					?>"  
					 style="background-image: url(/img/grade/icon_grade<?php echo $q->basic_grade?>.png)">
				</div>
			</div>
	    <?php
	}
	?></div><?php
}

function Strokami($qwe)
{
	?><div class="items_list"><?php
	foreach($qwe as $q)
	{
        $q = (object) $q;
		?>
		<div class="item_row">
			<label class="nicon" id="<?php echo $q->item_id?>">
				<input type="radio" name="item_id" value="<?php echo $q->item_id?>">
				<div class="itim" id="itim_<?php echo $q->item_id?>" style="background-image: url(/img/icons/50/<?php echo $q->icon?>.png)">
					<div class="grade" style="background-image: url(/img/grade/icon_grade<?php echo $q->basic_grade?>.png)"></div>
				</div>
				<div class="itemname"><?php echo $q->item_name?></div>
			</label>
		</div>
		<?php
	}
	?></div><?php
}
?>