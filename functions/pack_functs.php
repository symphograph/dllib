<?php
function rent($price, $craft_price)
{
	$rent_str = $prib_str = '';
	if($craft_price > 0 and $price >0)
	{
		//$rent = ' '.round($price/($price - $craft_price)*100,0).'%';
		$prib = round($price - $craft_price,0);
		$prib_str = esyprice($prib);
		$prib_str = '<br><div class="itemprompt" data-title="Чистая прибыль"><div>'.$prib_str.'</div></div>';
		//$rent = round($prib/$price*100,0);
		//$rent_str = '<br><div class="itemprompt" data-title="Рентабельность"><div>'.$rent.' %</div></div>';
	}
	
	return($prib_str.$rent_str);
}




?>