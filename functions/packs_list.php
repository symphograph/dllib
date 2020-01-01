
	<?php
		if($side_to_id == 3) {$zone_to_id = 40; $zone_from_id = 40;};
if($zone_from_id > 0)
{
	if($zone_from_id != 100)
	$query = qwe("SELECT * FROM `packs` WHERE `zone_id` = '$zone_from_id' ORDER BY `pack_t_id`");
	if($zone_from_id == 100)
	$query = qwe("SELECT * FROM `packs` WHERE `side` = '$side_to_id' ORDER BY `pack_t_id`");
	foreach($query as $v)
	{
		$item_id = $v['item_id'];
		$pack_name = $v['pack_name'];
		if(preg_match('/Груз компоста/',$pack_name))
		$pack_name = 'Груз компоста';
		if(preg_match('/Груз зрелого сыра/',$pack_name))
		$pack_name = 'Груз сыра';
		if(preg_match('/Груз домашней наливки/',$pack_name))
		$pack_name = 'Груз наливки';
		if(preg_match('/Груз меда/',$pack_name))
		$pack_name = 'Груз меда';
		echo '<div class="pack_row"><div class="pack_icon"><img src="img/icons/'.$item_id.'.png"/></div><div class="pack_name">'.$pack_name.'</div></div>';
	}
}
	
	?>