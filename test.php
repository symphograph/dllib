<meta charset="utf-8">
<?php
require_once 'includs/ip.php';
if(!$myip) exit();
include_once 'functions/functions.php';
include_once 'includs/config.php';

$arr = [];
$item_id = 8318;
$arr = DependentItems($item_id, $arr);

function DependentItems($item_id, $arr,$i=0)
{
    if(!$i) $i = 0;
    $i++;
    //global $arr;
    $qwe = qwe("
    Select result_item_id, ismat 
    from craft_materials 
    inner join items on craft_materials.result_item_id = items.item_id
    and craft_materials.item_id = '$item_id' 
    and items.on_off
    group by result_item_id
    ");
    if(!$qwe or $qwe->num_rows == 0)
        return false;

    foreach ($qwe as $q)
    {
        $id = $q['result_item_id'];
        $ismat = $q['ismat'];
        $arr[] = $id;
        //$item_name = ItemAny($id,'item_name')[$id] ?? 'oo!'.$id;
       // echo $id.' '.$item_name.'<br>';
        if($ismat)
            $arr = DependentItems($id, $arr,$i);
    }
    return $arr;
}
//sort($arr);
printr($arr);


?>

