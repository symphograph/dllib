<?php

$ps = [];
if(!empty($_COOKIE['pack_settings']))
	$ps = unserialize($_COOKIE['pack_settings']);

function PackTypeButton(int $type, string $navname, string $img, array $ps)
{
    $chk = '';
    if(isset($ps['type'][$type])){
        $chk = ' checked ';
    }

    ?>
    <div class="nicon_out">
        <input type="checkbox" id="type_<?php echo $type ?>" name="type[<?php echo $type ?>]" <?php echo $chk ?> value="<?php echo $type ?>">
        <label class="navicon" for="type_<?php echo $type ?>" style="background-image: url(<?php echo $img ?>);"></label>
        <div class="navname"><?php echo $navname ?></div>
    </div>
    <?php
}


?>

<div class="select_row">

	<div class="navcustoms">
		<div class="nicon_out">
			<input type="radio" id="side_1" name="side" value="1">
			<label class="navicon" for="side_1" style="background-image: url(img/westhouse.png);"></label>
			<div class="navname">Западные</div>
		</div>

        <?php
            $buttons = [
                1 => ['Обычные', 'img/icons/50/icon_item_0863.png'],
                8 => ['За ДЗ', 'img/icons/50/icon_item_0476.png'],
                2 => ['Компост', 'img/icons/50/icon_item_2504.png'],
                3 => ['С навеса', 'img/icons/50/icon_item_1336.png'],
                4 => ['Растворы', 'img/icons/50/icon_item_3869.png'],
                6 => ['Общинные', 'img/icons/50/icon_item_0864.png'],
                7 => ['Трофейные', 'img/icons/50/icon_item_4295.png']
            ];
            foreach ($buttons as $type => $v) {
                PackTypeButton($type, $v[0], $v[1], $ps);
            }
        ?>

		<div class="nicon_out">
			<input type="radio" id="side_2" name="side" value="2">
			<label for="side_2" class="navicon" style="background-image: url(img/icons/50/icon_house_029.png);"></label>
			<div class="navname">Восточные</div>
		</div>

        <div class="nicon_out">
            <input type="radio" id="side_3" name="side" value="3">
            <label for="side_3" class="navicon" style="background-image: url(img/icons/50/icon_item_0013.png);"></label>
            <div class="navname">Северные</div>
        </div>
		
	</div>		
	<hr>		
</div>

<div class="sortrow">
	
	<div class="freguency" title="Возраст пака">
		<select name="pack_age" class="select_input" autocomplete="off" onchange="">
		    <?php FreshTimeSelect() ?>
		</select>
	</div>

	<div class="sortmenu">	
		<div class="nicon_out">
			<input type="radio" id="sort_1" name="sort" value="1">
			<label class="navicon" for="sort_1" style="background-image: url(img/packmaker.png?ver=2);"></label>
			<div class="navname">Откуда</div>
		</div>
		<div class="nicon_out">
			<input type="radio" id="sort_2" name="sort" value="2">
			<label class="navicon" for="sort_2" style="background-image: url(img/perdaru2.png);"></label>
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
					<label class="navicon" for="sort_0" style="background-image: url(img/icons/50/icon_item_3229.png);"></label>
				</div>
				<div class="nicon_out" data-tooltip="По прибыли на 1 ор.<br>С учетом всех ОР на все этапы крафта.">
					<input type="radio" id="sort_4" name="sort" value="4" checked>
					<label class="navicon" for="sort_4" style="background-image: url(img/icons/50/2.png);"></label>
				</div>
			</div>
		</div>
	</div>
</div>