<?php
$item_id = $_POST['item_id'] ?? 0;
$item_id = intval($item_id);
if(!$item_id) die();

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';


$User = new User;
if(!$User->byIdenty())
	die('<span style="color: red">Oh!<span>');

$user_id = $User->id;
PackMatsDisplay($item_id,$User->id);

function PackMatsDisplay($item_id,$user_id)
{
	$query = qwe("
	SELECT
    craft_materials.craft_id,
    craft_materials.item_id,
    craft_materials.mater_need,
    items.item_name,
    items.icon,
    user_crafts.isbest,
    grades.color
    FROM
    craft_materials
    INNER JOIN user_crafts ON craft_materials.craft_id = user_crafts.craft_id 
    AND user_crafts.user_id = '$user_id'
    AND isbest > 0
    INNER JOIN items ON craft_materials.item_id = items.item_id AND craft_materials.item_id != 500
    AND items.on_off = 1
    INNER JOIN crafts ON craft_materials.craft_id = crafts.craft_id AND crafts.on_off = 1
    AND craft_materials.result_item_id = '$item_id'
    LEFT JOIN grades ON craft_materials.mat_grade = grades.id
	");

	?>
	<div class="pack_mats">

	<?php
	$pack_id = $item_id; 
	foreach($query as $q)
	{
		$q = (object) $q;
		?>
		
		<div class="maticon" style="background-image: url(img/icons/50/<?php echo $q->icon?>.png)">
			<div class="itdigit"><?php echo $q->mater_need?></div>
		</div>
		
		<?php
		
	}
	
	?>
	<a href="/catalog.php?item_id=<?php echo $pack_id?>">
	<div class="maticon" style="background-image: url(img/icons/50/icon_item_4069.png)"></div>
	</a>
	</div>			
	<?php
}