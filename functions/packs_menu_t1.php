<?php

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

<?php
function PackTypeButtons()
{
    //$qwe = qwe("select * from pack_types");
}
?>

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
            <input type="checkbox" id="type_8" name="type[1]" <?php if(isset($ps['type'][8])) echo 'checked' ?> value="8">
            <label class="navicon" for="type_8" style="background-image: url(../img/icons/50/icon_item_0476.png);"></label>
            <div class="navname">За ДЗ</div>
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
<?php

?>


<div class="sortrow">
	
	<div class="freguency" title="Возраст пака">
		<select name="pack_age" class="select_input" autocomplete="off" onchange="">
		    <?php FreshTimeSelect() ?>
		</select>
	</div>

	<div class="sortmenu">	
		<div class="nicon_out">
			<input type="radio" id="sort_1" name="sort" value="1">
			<label class="navicon" for="sort_1" style="background-image: url(../img/packmaker.png?ver=2);"></label>
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
				<label class="navicon" for="sort_3" style="background-image: url(img/icons/50/quest/icon_item_quest023.png);"></label>
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