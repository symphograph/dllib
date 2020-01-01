<?php
/*
$zone_to_id = '';
$zone_from_id = 100;

$side_to_id = $_POST['side_to'] ?? 1;
$side_to_id = intval($side_to_id);
if(isset($_POST['zone_to']) and ctype_digit($_POST['zone_to']))
	$zone_to_id = $_POST['zone_to'];
if(isset($_POST['zone_from']) and ctype_digit($_POST['zone_from']))
	$zone_from_id = $_POST['zone_from'];
if(isset($_GET['new_side']) and ctype_digit($_GET['new_side']))
	$side_to_id = $_GET['new_side'];
if(isset($_GET['new_zone']) and ctype_digit($_GET['new_zone']))
	$zone_to_id = $_GET['new_zone'];
$old_side = '';
*/
$ps = [];
if(!empty($_COOKIE['pack_settings']))
{
	$ps = unserialize($_COOKIE['pack_settings']);
	//printr($pack_settings);
	
}
?>
<!--<div class="timerow">

	<div class="slidecontainer"><input type="range" min="50" max="130" value="100" class="slider" id="myRange"></div>
	
</div>-->
<div class="select_row">

	<div class="navcustoms">
		<div class="nicon_out">
			<input type="radio" id="side_1" name="side" value="1">
			<label class="navicon" for="side_1" style="background-image: url(../img/westhouse.png);"></label>
			<div class="navname">Западные</div>
		</div>

		<div class="nicon_out">
			<input type="checkbox" id="type_1" name="type[1]" <?php if(isset($ps['type'][1])) echo 'checked' ?> value="1">
			<label class="navicon" for="type_1" style="background-image: url(../img/icons/50/icon_item_0863.png);"></label>
			<div class="navname">Обычные</div>
		</div>
		<div class="nicon_out">
			<input type="checkbox" id="type_2" name="type[2]" <?php if(isset($ps['type'][2])) echo 'checked' ?> value="2">
			<label class="navicon" for="type_2" style="background-image: url(../img/icons/50/icon_item_2504.png);"></label>
			<div class="navname">Компост</div>
		</div>
		<div class="nicon_out">
			<input type="checkbox" id="type_3" name="type[3]" <?php if(isset($ps['type'][3])) echo 'checked' ?> value="3">
			<label class="navicon" for="type_3" style="background-image: url(../img/icons/50/icon_item_1336.png);"></label>
			<div class="navname">С навеса</div>
		</div>
		<div class="nicon_out">
			<input type="checkbox" id="type_4" name="type[4]" <?php if(isset($ps['type'][4])) echo 'checked' ?> value="4">
			<label class="navicon" for="type_4" style="background-image: url(../img/icons/50/icon_item_3869.png);"></label>
			<div class="navname">Растворы</div>
		</div>
		<div class="nicon_out">
			<input type="checkbox" id="type_6" name="type[6]" <?php if(isset($ps['type'][6])) echo 'checked' ?> value="6">
			<label class="navicon" for="type_6" style="background-image: url(../img/icons/50/icon_item_0864.png);"></label>
			<div class="navname">Общинные</div>
		</div>
		<div class="nicon_out">
			<input type="checkbox" id="type_7" name="type[7]" <?php if(isset($ps['type'][7])) echo 'checked' ?> value="7">
			<label class="navicon" for="type_7" style="background-image: url(../img/icons/50/icon_item_4295.png);"></label>
			<div class="navname">Трофейные</div>
		</div>
		<div class="nicon_out">
			<input type="radio" id="side_2" name="side" value="2">
			<label for="side_2" class="navicon" style="background-image: url(../img/icons/50/icon_house_029.png);"></label>
			<div class="navname">Восточные</div>
		</div>
		
	</div>		
	<hr>		
</div>
<!--<div class="timerow">
<div id="package" class="package"></div>
<div class="slidecontainer"><input type="range" min="0" max="2525" step="15" value="0" class="slider2" id="myRange2"></div>
</div>-->


<!--<div class="itemprompt" data-title="Параметры для корректного расчета чистой прибыли">
<input type="submit" class="crft_button" formaction="user_customs.php" name="customs" value="Настройки">
</div>-->


<div class="sortrow">
	
	<div class="freguency" title="Возраст пака">
		<select name="pack_age" class="select_input" autocomplete="off" onchange="">
		<?php
			
			//if($pack_age == 0)
			echo '<option value="0" selected >Свежесть - 0:00</option>';
			for($i=15;$i<2525;$i+=15)
			{	
				if($i>=105) $i = $i+15;
				if($i>=150) $i = $i+30;
				if($i>=180) $i = $i+3*60;
				//if($i==$pack_age and $i >0)
				//$selected = 'selected';
				$pack_time = date("H:i",$i*60-3600*3);
				if($i > 1439)
					$pack_time = $pack_time.'+1д';
				echo '<option value="'.$i.'">'.$pack_time.'</option>';
				$selected = '';
			}	
		?>
		</select>
	</div>

	<div class="sortmenu">	
		<div class="nicon_out">
			<input type="radio" id="sort_1" name="sort" value="1">
			<label class="navicon" for="sort_1" style="background-image: url(../img/packmaker.png?ver2);"></label>
			<div class="navname">Откуда</div>
		</div>
		<div class="nicon_out">
			<input type="radio" id="sort_2" name="sort" value="2">
			<label class="navicon" for="sort_2" style="background-image: url(../img/perdaru2.png);"></label>
			<div class="navname">Куда</div>
		</div>
		<div class="monsort">
			<div class="nicon_out">
				<input type="radio" id="sort_3" name="sort" value="3">
				<label class="navicon" for="sort_3" style="background-image: url(img/icons/50/icon_item_0191.png);"></label>
				<div class="navname">Выручка</div>
			</div>
			<div class="profit_m">
				<div class="nicon_out" data-tooltip="По прибыли">
					<input type="radio" id="sort_0" name="sort" value="0" checked>
					<label class="navicon" for="sort_0" style="background-image: url(/img/icons/50/icon_item_3229.png);"></label>
					<!--<div class="navname">Прибыль</div>-->
				</div>
				<div class="nicon_out" data-tooltip="По прибыли на 1 ор.<br>С учетом всех ОР на все этапы крафта.">
					<input type="radio" id="sort_4" name="sort" value="4" checked>
					<label class="navicon" for="sort_4" style="background-image: url(/img/icons/50/2.png);"></label>
					<!--<div class="navname">Прибыль с 1ор</div>-->
				</div>
			</div>
		</div>
	</div>
</div>