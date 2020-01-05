<?php
function price_row($cost, $what_save)
{$gold = $silver = $bronze = $what = '';
 $required = '';
 
	if($cost >0)
	{$cost2 = str_pad($cost, 10, "0", STR_PAD_LEFT);
		$gold = ltrim(substr($cost2,0,6),"0");
		$silver = substr($cost2,6,2);
		$bronze = substr($cost2,8);
	}
 if($what_save == 'click') {$what = 'клика:<br>'; $required = 'required';}
 if($what_save == 'catal') $what = 'катализатора:<br>';
 if($what_save == 'item') $what = 'предмета:<br>';
 if($what_save == 'roll') $what = 'свитка:<br>';
	echo '<div class="price_row">';
	echo 'Цена '.$what;
    echo
	'<input name="'.$what_save.'_gold" '.$required.' class="gold" type="number" autocomplete="off" min="0" max="999999" value="'.$gold.'"/>
	<img src="img/gold.png" alt="gold" border="0">
	<input name="'.$what_save.'_silver" '.$required.' class="silver" type="number" autocomplete="off" min="0" max="99" value="'.$silver.'"/>
	<img src="img/silver.png" alt="silver" border="0">
	<input name="'.$what_save.'_bronze" '.$required.' class="brnz" type="number" autocomplete="off" min="0" max="99" value="'.$bronze.'"/>
	<img src="img/bronze.png" alt="bronze" border="0">
	<input type="hidden" name="'.$what_save.'_cost" value="'.$cost.'"/>
	</div>';	
}
?>
