<?php
function mats($craftsq){
$x = 0;
$crafta = 0;
foreach($craftsq as $v)
	{$craft = $v['craft_id'];
	 if($craft != $crafta and $x > 0) break;
	 $mat = $v['item_name'];
	 echo $mat.'<br>';
	 $crafta = $craft;
	 $x++;};
}
?>