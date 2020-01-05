
	<?php

    echo
	'<div class="top_t1_row">
	<div class="col0"></div>';
	echo	
	'<div class="itemprompt" data-title="Для Т2: Солрид, Две Короны, Полуостров падающих звёзд. Для Т3: Лес Гвинедар, Иммергрунское Нагорье, Заболоченные Низины">
	<div class="colz2">Запад</div></div>
	<div class="itemprompt" data-title="Для Т2: Инистра, Поющая Земля, Полуостров Рассвета. Для Т3: Радужные Пески, Плато Соколиной Охоты, Руины Харихараллы">
	<div class="colz2">Восток</div></div>
	<div class="itemprompt" data-title="Фактория сверкающего побережья. Не забудте, что там не принимают Т3.">
	<div class="colz2">Север</div></div>
	<div class="itemprompt" data-title="Остров свободы. Не забудте, что там не принимают Т3."><div class="colz2">ОС</div>
	</div>';
	
	echo
	'</div>
	<div class="packs_area_t1">';
	
	
	include 'includs/config.php';
	$where = "`pack_t_id` in (4, 5)  ORDER BY `side`, `item_id`";	
	$query = qwe("SELECT * FROM `packs` WHERE ".$where."");
	$zone_name2 = '';
	$numrows = mysqli_num_rows($query);
	$i=0; $n=0; $open = false;
	foreach($query as $v)
	{   $i++;
	 if($n<2) $n++;
		$item_id = $v['item_id'];
	    $pr_col_start = '<div class="price_col2">';
	    $pr_col_end = '</div>';
	    $side = $v['side'];
	    
	    $price_1 = $v['zone_5'];
	    $price_2 = $v['zone_4'];
	    $price_3 = $v['zone_40'];
	    $price_4 = $v['zone_30'];//ОС
			
	
	
	 $price_1 = round($price_1/130*$per,0);
	 $price_2 = round($price_2/130*$per,0);
	 $price_3 = round($price_3/130*$per,0);
	 $price_4 = round($price_4/130*$per,0);
	
	 $price_1 = price_str($price_1,$per);
	 $price_2 = price_str($price_2,$per);
	 $price_3 = price_str($price_3,$per);
	 $price_4 = price_str($price_4,$per);
		$price_row = $pr_col_start.$price_1.$pr_col_end.$pr_col_start.$price_2.$pr_col_end.$pr_col_start.$price_3.$pr_col_end.$pr_col_start.$price_4.$pr_col_end;
		/*
	 else 
	 {
		 $price_1 = round($price_1/130*$per,0);
	     $price_1 = price_str($price_1);
		 $price_row = $pr_col_start.$price_1.$pr_col_end;
	 }
	  */  
	    
	    $pack_type = $v['pack_type'];
		$pack_name = $v['pack_name'];
		$zone_name = $v['zone_name'];
	 if($side == 1)
		 $side = 'Продают в факториях: Солрид, Две Короны, Полуостров падающих звезд';
	 if($side == 2)
		 $side = 'Продают в факториях: Инистра, Поющая земля, Полуостров рассвета';
	 if($side == 3)
		 $side = 'Продают в фактории Сверкающего побережья';
	/*	
	 if($zone_name != $zone_name2 and $i<$numrows)
			
			{ 
				if($open) echo '</div><hr>';
				echo '<div class="pack_row"><div class="zone_row">'.$zone_name.'</div></div><div class="zone_area">';
		    $open = true;
			}
	 */
	     $item_link= str_replace(" ","+",$pack_name);
		echo '<div class="pack_row'.$n.'"><div class="itemprompt" data-title="Смотреть рецепт"><div class="pack_icon">
		<a href="../catalog.php?query='.$item_id.'" target="_blank"><img src="img/icons/'.$item_id.'.png" width="40" height="40"/></a>
		</div></div>
		<div class="itemprompt" data-title="'.$side.'"><div class="pack_name">'.$pack_name.'</div></div>';
		echo $price_row;
	 
	    echo '</div>';
		if($open and $i == $numrows)
			echo '</div>';
		$zone_name2 = $zone_name;
	 if($n==2) $n=0;
	 $price_1=$price_2=$price_3='';
	}
	echo '<div>';
	
	?>