<?php
$item_id = intval($_POST['item_id']);
if(!$item_id) die;
$cooktime = time()+60*60*24*360;
setcookie("item_id",$item_id,$cooktime,'/');
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}


$User = new User();
if(!$User->byIdenty())
	die('<span style="color: red">Oh!<span>');

$user_id = $User->id;
$mode = $User->mode;
$Item = new Item();
if(!$Item->getFromDB($item_id)){
    die();
}

$description = $Item->description;
	?>
<div id="catalog_area">
	<div class="item_descr_area">
		<?php if($cfg->myip) echo $Item->id?>
		<div class="nicon">
			<div class="itim" id="itim_<?php echo $Item->id?>" style="background-image: url('/img/icons/50/<?php echo $Item->icon?>.png')">
				<div class="grade" style="background-image: url('/img/grade/icon_grade<?php echo $Item->basic_grade?>.png')"></div>
			</div>
			<div class="itemname">
				<div id="mitemname"><b><?php echo $Item->name?></b></div>
				<div class="comdate"><?php if($Item->personal) echo 'Персональный предмет'?></div>
				<div class="mcateg" id="categ_<?php echo $Item->categ_id?>" sgroup="<?php echo $Item->sgr_id?>"><?php echo $Item->category?></div>
			</div>	
		</div>
		<hr><br>
		<details><summary>Описание</summary>
		<div class="item_descr"><?php echo $description?></div>
		</details><br>
		
		<a href="https://archeagecodex.com/ru/item/<?php echo $Item->id?>/" target="_blank">
		<div class="aacodex_logo" data-tooltip="Смотреть на archeagecodex"></div>
		</a>
		<hr>
		
		<?php
		$Price = new Price($Item->id);
        $Price->byMode();
		if($Item->is_trade_npc)
		{
			
			echo 'Продается у NPC:<br>';
			if($Item->valut_id == 500)
				echo esyprice($Item->price_buy);
			else
			{
				echo $Item->price_buy;
				?>
				<a href="catalog.php?item_id=<?php echo $Item->valut_id?>">
		        <img src="img/icons/50/<?php echo $Item->ValutIcon()?>.png" width="15" height="15" alt="<?php echo $Item->valut_name?>"/>
		        </a>
		        <br><br>
		        <?php
					
				if(!$Item->personal){
				    $Price->MoneyForm();
				    $Price->serverMedianPrint();
				}

			}
			
		}elseif($Item->categ_id != 133)
		{
			$Price->MoneyForm();
			$Price->serverMedianPrint();
		}
		
		if($cfg->myip)
		{
			?><a href="/recedit.php?addrec=<?php echo $Item->id?>" target="_blank"><button class="def_button">Добавить рецепт</button></a><br><?php
			?><a href="/edit/edit_item.php?item_id=<?php echo $Item->id?>"><button class="def_button">Править итем</button></a><br><?php
			?><a href="/edit/item_off.php?item_id=<?php echo $Item->id?>"><button class="def_button">отключить</button></a><br><?php
		}
		?>
        <hr><br>
        <a href="user_customs.php"><button class="def_button">Настройки</button></a><br>
        <a href="user_prices.php"><button class="def_button">Мои цены</button></a>
	</div>
	<div id="catalog_right">
<?php
if($Item->ismat)
{
	?><p><b>Используется в рецептах:</b></p>
		<div class="up_craft_area"><?php
	    UpCraftList($Item->id);
		?></div><?php
}
else 
	echo '<div class="up_craft_area">Не используется в рецептах</div>';
			 
			 
			 

	
if($Item->craftable)
{
	?><hr>
	<div class="dcraft_craft_area" id="dcraft_craft_area">
	
	<?php
	//Надо посчитать оптималный
	$globalitem_id = $Item->id;
	$trash = 1;

	if(!$Item->isCounted())
    {
        qwe("DELETE FROM craft_buffer WHERE `user_id` = '$User->id'");
        qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$User->id'");
        require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/funct-obhod2.php';
        $Item->RecountBestCraft();
        qwe("DELETE FROM craft_buffer WHERE `user_id` = '$User->id'");
        qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$User->id'");
    }

    $Item->getCrafts();
	if(count($Item->crafts))
	    DwnCraftList($Item);

	?></div><?php



	if($Item->ispack)
    {
        ?>
        <br><hr><br>
        <a href="/packpost.php?item_id=<?php echo $Item->id?>">
            <button type="button" class="def_button">Пакулятор</button>
        </a> <?php
    }


}else
{
	$refuse = IsRefuse($Item->id);
	if($refuse)
		RefuseList($refuse);
	else
		echo 'Некрафтабельно';
	if(($Item->is_trade_npc and $Item->valut_id !=500) or  in_array($Item->id,[3,4,5,6,23633]))
		ValutInfo($Item);

	if ($Item->id == 41488){
	    ?>
	    <div>
            <br>
            <p>Дополнительно о векселях можно узнать на <a href="https://gisaa.ru/veksel" ><b>gisaa.ru</b></a></p>
	    </div>
        <?php
	}
}
?>
        </div>
    </div>
</div>
<?php
Comments($User,$item_id);

function ValutInfo($Item)
{
	global $User;
	$mvalut = $Item->valut_id;
	$valut_name = $Item->valut_name;
	//Касательно Чести, Рем Репутации, итд.
	if(in_array($Item->id,[2,3,4,5,6,23633]))
	{
		$valut_name = $Item->name;
		$mvalut = $Item->id;
	}
	$val_link = '<a href="catalog.php?item_id='.$mvalut.'">
		<img src="img/icons/50/'.$mvalut.'.png" width="15" height="15" alt="'.$valut_name.'"/></a>';
	
	ItemsFromAlterValutes($mvalut);
	?>
	<br><br>
	<h3><?php echo $Item->name?>: конвертация в золото.</h3>
	*Следует учесть, что калькулятор не принимает в расчет предметы, позволяющие конвертировать валюту оптом.<br>
	Медиана:
	
	<?php
	//$serv_median = RepMedian($mvalut, $User->id);
	$valutData = MonetisationList($val_link, $mvalut, $User->id);
	//printr($valutData);
	if(!empty($valutData[1]))
	{
		echo '<div class="valut_median">'.$val_link.' = '. esyprice(round($valutData[0],0)).'</div>';
		?>
		<div class="clear"></div><br><hr>
		
		<?php
		echo $valutData[1];
		//var_dump($arr_mon);
		
	}	
	else
		echo '<br>Нет данных. Попробуйте указать несколько цен на предметы за эту валюту.<br>';
	
}

function RefuseList($items)
{
	
	$items = implode(',',$items);
	$qwe = qwe("
	SELECT
	items.item_id,
	items.icon,
	items.item_name,
	items.basic_grade
	FROM items 
	WHERE item_id in (".$items.")
	");
	if(!$qwe) return false;
	if($qwe->num_rows == 0) return false;
	?><p><b>Является отходом при крафте:</b></p>
		<div class="up_craft_area"><?php
	Cubiki($qwe);
	?></div><?php
}

function IsRefuse(int $item_id)
{
	$qwe = qwe("
	SELECT result_item_id 
	FROM craft_materials
	WHERE item_id = '$item_id'
	AND mater_need < 0
	");
	if(!$qwe or !$qwe->num_rows)
	    return false;

	$arr = [];
	foreach($qwe as $q)
	{
		$arr[] = $q['result_item_id'];
	}
	return $arr;
}

function DwnCraftList($ItemOb)
{
    $Item = new Item();
    $Item->reConstruct($ItemOb);
	$best_types = ['','Выбран руру','Выбран вами', 'Покупается'];
	$item_id = $Item->id;
	global $User, $mat_deep, $cfg, $trash, $User;

	$qwe = qwe("
	SELECT
	`crafts`.`craft_id`
	FROM
		`crafts`
	INNER JOIN `user_crafts` ON `crafts`.`craft_id` = `user_crafts`.`craft_id`
	AND `user_crafts`.`user_id` = '$User->id'
	AND `user_crafts`.`item_id` = '$item_id'
	AND `crafts`.`on_off` = 1
	ORDER BY `isbest` DESC, `spmu`,`craft_price`
	");
	$i=0;
	$imgor = '<img src="../img/icons/50/2.png" width="15px" height="15px"/>';
	foreach($qwe as $q)
	{$i++;
		//extract($q);
		$Craft = new Craft($q['craft_id']);
		$Craft->InitForUser();
	 	if(!$Craft->setCountedData($User->id))
	 	    continue;

	 	$Prof = new Prof();
	 	$Prof->InitForUser($Craft->prof_id);
		$craft_name = $Craft->rec_name ?? $Item->name;
	 	//$u_amount = $result_amount;
	 	$u_amount = 1;
	 	if($i == 1)
		{
			if(!empty($_POST['u_amount']))
			{
				$u_amount = intval($_POST['u_amount']);
			}	
		}
	 
	 	if($i == 1 and !$Item->personal)
		{
			?>
			<div id="isbuy">
				<div class="isby">
					<input type="radio" <?php if($Craft->isbest != 3) echo 'checked';?> id="ib_<?php echo $item_id?>" name="isbuy" value="1"/>
					<label class="navicon" for="ib_<?php echo $item_id?>" data-tooltip="Использовать для расчетов себестоимость<br>Считать по крафту" style="background-image: url(../img/profs/Обработка_камня.png);"></label>
					<span>Крафтить</span>
				</div>
				<div>x
				<input type="number" name="u_amount" id="u_amount" min="1" value="<?php echo $u_amount?>" autocomplete="off"	/>
				</div>
				<div class="isby">
					<span>Покупать</span>
					<input type="radio" <?php if($Craft->isbest == 3) echo 'checked';?> id="is_<?php echo $item_id?>" name="isbuy" value="3"/>
					<label class="navicon" for="is_<?php echo $item_id?>" data-tooltip="Использовать мою цену для расчетов" style="background-image: url(../img/perdaru2.png);"></label>
				</div>
			</div>
			<?php
		}
	 	if($i == 2)
			echo '<br><details><summary>Другие рецепты</summary>';
			
		$Price = new Price($Item->id);
        $Price->byMode();

	 	if($Price->price)
		{
			$profit = round(($Price->price*0.9 - $Craft->craft_price),0);
			$labor_total = floatval($Craft->labor_total);
			if($labor_total)
			{
				$profitor = round(($profit/$labor_total),0);
				$profitor = esyprice($profitor);
			}	
			else
				$profitor = '';
			$profit = esyprice($profit);	
		} else
		$profitor = $profit = 'Не вижу цену';
	 	$sptime = SPTime($Craft->mins);

		
		?>
		<div class="crresults">
			<div><b><?php echo $best_types[$Craft->isbest];?></b></div>
			<div>
				<!--<div class="itemprompt" data-title="Рецепт будет выбран автоматически">Сбросить выбор</div>-->
			</div>
		</div>
		<div class="craftinfo">
			<div>
				<div class="crresults"><div></div></div>	
			</div>
			<div>
				<div class="crresults"><div><b><?php echo $craft_name?></b></div></div>	
			</div>
		</div>
		<div class="craftinfo">
			<div>
				<div class="crresults">
                    <div>
                        <img src="img/profs/<?php echo str_replace(" ","_",$Prof->name)?>.png" width="20px" height="20px"><?php echo $Prof->name?>
                    </div>
                    <div><?php if($Craft->prof_need > 1000) echo round($Craft->prof_need/1000).'к'?></div>

				</div><hr>
			</div>
			<div>
				<div class="crresults"><div><?php echo $Craft->dood_name?></div></div>
			</div>
		</div>
		<div class="craftinfo">
			<div>
			    <?php printVals('Коэф SPM:',$Craft->spmu,'Коэфициент приводящий друг к другу такие величины, как занимаемая площадь, интервал между сбором, себестоимость, получаемое количество, требуемое количество.');?>
			</div>

			<?php
			if($sptime){
			    ?><div><?php printVals('Интервал:',$sptime);?></div><?php
            }
            ?>

		</div>
		<div class="craftinfo">
			<div>

                <?php
                    printVals('Себестоимость 1 шт:', esyprice($Craft->craft_price));

                    if(in_array($Item->categ_id,[133])){
                        $PackObject = PackObject($item_id);
                    }else
                    {
                        printVals('Прибыль:',$profit);
                        printVals('Прибыль с 1 ОР:',$profitor);
                    }
			    ?>
			</div>
			<div>
			    <?php
			        printVals('На рецепт:',$Craft->labor_need2.$imgor);

                    if(in_array($Item->categ_id,[133]))
                    {
                        $pass_labor = $PackObject['pass_labor2'];
                        //printr($PackObject);
                        printVals('На сдачу:',$pass_labor.$imgor);
                        printVals('На цепочку:',round($Craft->labor_total,2).$imgor);
                        printVals('На всё:',round($Craft->labor_total+$pass_labor,2).$imgor);

                    }else
                    {
                        printVals('На 1 шт:',$Craft->labor_single.$imgor);
                        printVals('На цепочку',round($Craft->labor_total,2).$imgor);
                    }
				?>
			</div>
		</div>

		<div class="crftarea">
            <?php $Craft->matArea($u_amount,$Item);?>
		</div>

		<?php
	 	if($cfg->myip)
		{
			?><a href="../recedit.php?query=<?php echo $Craft->craft_id?>" target="_blank">Править</a><?php
		}
		?>
		<hr><?php
		if($i == 1)
		//Если рецепт основной, показываем полный список ресов	
		{		

			?><div><?php

                    $Item->allMatsShow($u_amount, $Craft->result_amount);
                    $Item->allTrashShow($u_amount, $Craft->result_amount);
			?></div><div class="clear"><?php
		}
	}
	

}

function SPTime($mins)
{
    if(!$mins)
        return '';

    $m = $mins;
    $h = floor($m/60);
    $m = $m-$h*60;

    $d = floor($h/24);
    $h = $h-$d*24;
    if($d>0)
        $d = $d.'д.+';
    else
        $d = '';
    return $d.$h.':'.$m;
}

function UpCraftList(int $item_id)
{

	$qwe = qwe("
	SELECT DISTINCT
	craft_materials.result_item_id as `item_id`,
	items.icon,
	items.item_name,
	items.basic_grade
	FROM
	craft_materials
	INNER JOIN items ON craft_materials.result_item_id = items.item_id
	AND craft_materials.item_id = '$item_id'
	AND items.on_off = 1
	AND craft_materials.mater_need > 0
	INNER JOIN crafts ON craft_materials.craft_id = crafts.craft_id
	AND crafts.on_off = 1
	ORDER BY crafts.deep DESC,
	items.categ_id, craft_materials.result_item_id
	");
	if($qwe->num_rows > 0)
		Cubiki($qwe);
	else
		echo 'Не нашел рецепты';
}

function Cubiki($qwe)
{
	foreach($qwe as $q)
	{
		$q = (object) $q;

		$basic_grade = $q->basic_grade;
		if(!$q->basic_grade)
		    $basic_grade = 1;

		$iconpath = '/img/icons/50/'.$q->icon.'.png';
		$icon_md5 = md5_file($_SERVER['DOCUMENT_ROOT'].$iconpath);
		$icon_url = $iconpath.'?ver='.$icon_md5;
		?>

			<div class="itim"
			id="itim_<?php echo $q->item_id?>"
			style="background-image: url(<?php echo $icon_url?>)">
				<div class="grade" class="grade" data-tooltip="<?php echo $q->item_name?>" style="background-image: url(/img/grade/icon_grade<?php echo $basic_grade?>.png)">
					
				</div>
			</div>
		<?php
	}
}

?>