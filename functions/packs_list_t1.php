<?php
if(!PriceMode(32103,$user_id))
{
	$coalprice = PriceMode(32103,$user_id)['auc_price'];
	$coalprice = intval($coalprice);
	if($coalprice > 0)
	qwe("REPLACE INTO `prices` (`user_id`, `item_id`, `server_group`, `auc_price`, `time`) 
	VALUES
	('$user_id','32103', '$server_group', '$coalprice', '2018-02-02 00:00:00')
	");
}

if(!PriceMode(32106,$user_id,1))
{
	$shellprice = PriceMode(32106,$user_id)['auc_price'];
	$shellprice = intval($shellprice);
	if($shellprice > 0)
	qwe("REPLACE INTO `prices` (`user_id`, `item_id`, `server_group`, `auc_price`, `time`) 
	VALUES
	('$user_id','32106', '$server_group', '$shellprice', '2018-02-02 00:00:00')
	");
}
?>

	<div class="packs_area_t1">
		
	<?php
	$craft_price = 1;
	$query = qwe("
SELECT
packjoin.item_id,
items.item_name,
items.icon,
packjoin.side,
packjoin.zone_id,
packjoin.pack_t_id,
packjoin.pack_type,
packjoin.zone_name,
packjoin.zone_to,
(SELECT zone_name FROM zones WHERE zone_id = zone_to) as zname_to,
fresh_lvls.fresh_name,
packjoin.db_price,
packjoin.quantity,
IF(packjoin.pack_t_id = 6,0,fresh_data.fresh_per) as fresh_per,
packjoin.valuta_id,
ROUND(packjoin.quantity*(IF(packjoin.valuta_id = 500,1,coal_price.auc_price))*IF(packjoin.valuta_id = 500,1,0.9)) as fresh_price,
user_crafts.craft_price,
ROUND(packjoin.quantity*(IF(packjoin.valuta_id = 500,1,coal_price.auc_price))*IF(packjoin.valuta_id = 500,1,0.9)) - user_crafts.craft_price as profit
FROM
fresh_data
INNER JOIN 
(
	SELECT pack_prices.item_id,  
	pack_prices.zone_to,
	zones.zone_id,
	zones.side,
	zones.zone_name,
	fresh_data.fresh_lvl,
	packs.pack_t_id,
	fresh_data.fresh_type,
	packs.pack_type,
	pack_prices.valuta_id,
	fresh_data.fresh_tstart,
	fresh_data.fresh_tstop,
	pack_prices.pack_price as db_price,
	ROUND(ROUND(pack_prices.pack_price/mul + pack_prices.pack_price/mul * IF(packs.pack_t_id = 6,1,fresh_data.fresh_per)/100)/130*IF(pack_prices.valuta_id = 500,".($per+$siol).",130)) as quantity
	FROM fresh_data
	INNER JOIN zones ON zones.fresh_type = fresh_data.fresh_type AND zones.side = '$side_to_id'
	INNER JOIN packs ON packs.zone_id = zones.zone_id
	INNER JOIN pack_prices ON pack_prices.zone_id = zones.zone_id AND pack_prices.item_id = packs.item_id
	WHERE ('$pack_age' BETWEEN fresh_data.fresh_tstart AND fresh_data.fresh_tstop-1)
) as packjoin
ON fresh_data.fresh_type = packjoin.fresh_type AND fresh_data.fresh_lvl = packjoin.fresh_lvl
INNER JOIN items ON items.item_id = packjoin.item_id AND items.on_off = 1
INNER JOIN fresh_lvls ON fresh_lvls.fresh_lvl = packjoin.fresh_lvl
LEFT JOIN user_crafts ON user_crafts.item_id = items.item_id AND user_crafts.isbest = 1 AND user_id = '$user_id'
LEFT JOIN (SELECT * FROM prices WHERE user_id = '$user_id' AND `server_group` = '$server_group') as coal_price
on coal_price.user_id = '$user_id' AND coal_price.item_id = packjoin.valuta_id 
ORDER BY ".$sort);
	$zone_name2 = '';
	$numrows = mysqli_num_rows($query);
	$i=0; $n=0; $open = false;
	foreach($query as $v)
	{   $i++;
	 $craft_price = 0;
	
	 $item_id = $v['item_id'];
	 $freshency = $v['fresh_name'];
	 $fres_per = $v['fresh_per'];
	 $valuta = $v['valuta_id'];
	 if($valuta != 500)
		 $price = $v['quantity'];
	 else
	 $price = $v['fresh_price'];
	 $profit = $v['profit'];
	 $zname_from = $v['zone_name'];
	 $zname_to = $v['zname_to'];
	 
	 $price_1 = price_str($price,$valuta);
	 $profit_1 = price_str($profit,500);
	
			$viruch = '<div class="itemprompt" data-title="Выручка">';
			$price_row = '<div class="colz">'.$zname_to.'</div>'.'<div class="colz">'.$price_1.'</div>'.'<div class="colz">'.$profit_1.'</div>';
	
	    
	    $pack_type = $v['pack_type'];
		$pack_name = $v['item_name'];
		if(preg_match('/Груз компоста/',$pack_name))
		$pack_name = 'Груз компоста';
		if(preg_match('/Груз зрелого сыра/',$pack_name))
		$pack_name = 'Груз сыра';
		if(preg_match('/Груз домашней наливки/',$pack_name))
		$pack_name = 'Груз наливки';
		if(preg_match('/Груз меда/',$pack_name))
		$pack_name = 'Груз меда';
	 	if($pack_name == 'Вяленые припасы Заболоченных низин')
		$pack_name = 'Вяленые припасы';
	 	$pack_name = $pack_name.'<br><span class="zname">'.$zname_from.'</span>';
		$zone_name = $v['zone_name'];
	 
	 
	 if (false): 
		if($zone_name == $zone_name2 /*and $i<$numrows*/)
			
			{ 
				if($open) echo '</div><hr>';
				?>
				<div class="pack_row">
	    			<div class="zone_row"><?php echo $zone_name;?></div>
	    		</div>
	    		<div class="zone_area">
		    <?php
			    $open = true;
			}
	 endif; 
	    $item_link= str_replace(" ","+",$pack_name);
	 	?>
		<div class="pack_row<?php echo $n?>">
		<div class="itemprompt" data-title="Смотреть рецепт">
		<a href="../catalog.php?query_id=<?php echo $item_id?>" target="_blank">
		<div class="pack_icon" style="background-image: url(img/icons/50/<?php echo $v['icon']?>.png)">
		<div class="itdigit"><?php echo $fres_per;?>%</div>
		</div></a></div>
		<div class="itemprompt" data-title="<?php echo $pack_type?>"><div class="pack_name"><?php echo $pack_name;?></div></div>
		<?php
		echo $price_row;
	 
	    echo '</div>';
		//if($open and $i == $numrows)
		//echo '</div>';
		$zone_name2 = $zone_name;
	 	$n++;
		if($n==2) $n=0;
		//$price_1=$price_2=$price_3='';
	}
	echo '<div>';
	?>