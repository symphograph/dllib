<meta charset="utf-8">
<?php
require_once 'includs/ip.php';
if(!$myip) exit();
include_once 'functions/functions.php';
include_once 'includs/config.php';



$item_id = 8319;
$arr = AllPotentialMats($item_id);

function AllPotentialMats(int $item_id, array $arr=[], int $i=0)
{
    $i = intval($i);
    $i++;

    $qwe = qwe("
    SELECT 
    crafts.craft_id,
    items.item_id,
    items.item_name,
    items.craftable
    FROM craft_materials
    inner join items on craft_materials.item_id = items.item_id
    AND craft_materials.result_item_id = '$item_id'
    AND items.on_off
    AND craft_materials.mater_need > 0
    inner join crafts on crafts.craft_id = craft_materials.craft_id
	AND crafts.on_off
    ");
    if(!$qwe or $qwe->num_rows == 0)
        return [];

    foreach ($qwe as $q)
    {

        $id = $q['item_id'];
        $craftable = $q['craftable'];
        //if(!$craftable) continue;
        //echo $id.'<br>';
        $arr[] = $id;
        if($craftable)
            $arr = AllPotentialMats($id, $arr,$i);
    }
    $arr = array_unique($arr);
    sort($arr);
    return $arr;
}
//printr($arr);

$qwe = qwe("
SELECT * from items
WHERE item_id
IN (".implode(', ',$arr).")
");
foreach ($qwe as $q)
{
    extract($q);
    echo $item_name.'<br>';
}
?>

