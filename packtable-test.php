<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name = "description" content = "Таблица цен на паки в Archeage 3.0." />
  <meta name = "keywords" content = "паки 3.0, archeage, архейдж, региональные товары, таблица паков" />
  <meta name="yandex-verification" content="4878c37eb34cedcf" />
<title>Таблица цен на паки 3.0</title>
 <link href="css/style.css" rel="stylesheet">
 <link href="css/packstable.css?ver=123" rel="stylesheet">
</head>

<body>

<?php
include_once 'pageb/header.html';
$ip = $_SERVER['REMOTE_ADDR'];
if($ip == '37.194.65.246'){
 ini_set('display_errors',1);
error_reporting(E_ALL);};
require_once 'includs/config.php';
$iplong = ip2long($ip);

$sidefrom = 0;
$os = false;
$gold = '';
$coal = '';
$shell = '';
$res = '';
$type = "Все";
$way = "Запад (Внутренняя торговля)";
if(isset($_POST['type']))
$type = mysqli_real_escape_string($dbLink,$_POST['type']);
$vse = ''; $esy = ''; $naves = ''; $rothond = ''; $compost = '';

if($type == "Все")
$vse = 'checked';
if($type == "Обычный")
$esy = 'checked';
if($type == "Ротонда")
$rothond = 'checked';
if($type == "Навес") 
$naves = 'checked';
if($type == "Компост") 
$compost = 'checked';
$valgold = '<img src="img/gold.png" width="15" height="15" alt="gold"/>';
$valcoal = '<img src="img/icons/32103.png" width="15" height="15" alt="coal"/>';
$valshell = '<img src="img/icons/32106.png" width="15" height="15" alt="shell"/>';
$valdz = '<img src="img/icons/23633.png" width="15" height="15" alt="dz"/>';
if(isset($_POST['way']))
$way = mysqli_real_escape_string($dbLink,$_POST['way']);
$westint = ''; $westout = ''; $eastint = ''; $eastout = ''; $iznout = ''; $val = $valgold;						
if($way == "Запад (Внутренняя торговля)") $westint = 'checked';
if($way == "Запад (Внешняя торговля)") {$westout = 'checked'; $val = $valcoal;};
if($way == "Восток (Внутренняя торговля)") $eastint = 'checked';
if($way == "Восток (Внешняя торговля)"){$eastout = 'checked'; $val = $valcoal;}
if($way == "Паки Изначального материка"){$iznout = 'checked';}
$siol = 0; $siolx = 1; $ch_siol = ''; $ch_nosiol = 'checked';
$per = 130;
if(isset($_POST['perc']))
$per = mysqli_real_escape_string($dbLink,$_POST['perc']);
if($per > 141 or $per < 50) exit();
if(isset($_POST['siol']))
$siol = mysqli_real_escape_string($dbLink,$_POST['siol']);
if($siol != 5 and $siol != 0) exit();
if($siol == 5)
{$ch_siol = 'checked'; $ch_nosiol = ''; $siolx = '1.05';};
$fper = '<option selected value="130">130</option>';
$fper2 = '<option  value="135">135</option>';
$fper3 = '<option  value="140">140</option>';
echo '<div class="topw"></div><div class="input1"><div class="inpur">';
echo '<div class="title"><form action="" method="post"><b>Цены на паки в версии 3.0 при</b> <select name="perc" onchange="this.form.submit()"><option selected value="'.$per.'">'.$per.'</option>';
$f = 45;
for ($i = 1; $i <= 19; $i++){
	$f = $f+5;
echo '<option value="'.$f.'">'.$f.'</option>';};
echo '</select>%&nbsp;';

?>
<input name="siol" type="radio" value="5" title = "С Сиоль" onchange="this.form.submit()" <?php echo $ch_siol; ?>><img src="img/icons/siol.png" width="20" height="20" alt="siol"/>
    <input name="siol" type="radio" value="0" title = "Без Сиоль" onchange="this.form.submit()" <?php echo $ch_nosiol; ?>><img src="img/icons/nosiol.png" width="20" height="20" alt="nosiol"/>
<?php
$per = $per/100;
?>


<div class="fixmenu">
<?php 
if($way !== "Паки Изначального материка")
echo 
'<p><input name="type" type="radio" value="Обычный" onchange="this.form.submit()" '.$esy.'> Обычный
    <input name="type" type="radio" value="Ротонда" onchange="this.form.submit()" '.$rothond.'> Ротонда
    <input name="type" type="radio" value="Навес" onchange="this.form.submit()" '.$naves.'> Навес
    <input name="type" type="radio" value="Компост" onchange="this.form.submit()" '.$compost.'> Компост
    <input name="type" type="radio" value="Все" onchange="this.form.submit()" '.$vse.'> Все</p>';
?>
<p><input name="way" type="radio" value="Запад (Внутренняя торговля)" onchange="this.form.submit()" <?php echo $westint; ?>> Запад (Внутренняя торговля)</p><p>
    <input name="way" type="radio" value="Запад (Внешняя торговля)" onchange="this.form.submit()" <?php echo $westout; ?>> Запад (Внешняя торговля)</p><p>
    <input name="way" type="radio" value="Восток (Внутренняя торговля)" onchange="this.form.submit()" <?php echo $eastint; ?>> Восток (Внутренняя торговля)</p><p>
    <input name="way" type="radio" value="Восток (Внешняя торговля)" onchange="this.form.submit()" <?php echo $eastout; ?>> Восток (Внешняя торговля)</p><p>
    <input name="way" type="radio" value="Паки Изначального материка" onchange="this.form.submit()" <?php echo $iznout; ?>> Паки Изначального материка
    </p><br></div>
</form>

<?php

//$query = qwe("SELECT * FROM `packs` WHERE `zone_id` > 0");

echo '<div id="rowtop"><div class="leftop">&nbsp;</div>';
if($way == "Запад (Внутренняя торговля)")
$qto = qwe("SELECT * FROM `zones` WHERE `is_get` = '1' and `side` = 1");
if($way == "Восток (Внутренняя торговля)")
$qto = qwe("SELECT * FROM `zones` WHERE `is_get` = '1' and `side` = 2");
if($way == "Запад (Внешняя торговля)")
$qto = qwe("SELECT * FROM `zones` WHERE `get_east` = '1' and `side` = 2");
if($way == "Восток (Внешняя торговля)")
$qto = qwe("SELECT * FROM `zones` WHERE `get_west` = '1' and `side` = 1");
if($way == "Паки Изначального материка") {$to = 'Остров свободы';
echo '<div class="cent">'.$to.'<br>'.$val.'&nbsp;</div>';};
if($way !== "Паки Изначального материка"){
foreach($qto as $zkey){
	$to = $zkey['zone_name'];
	
	echo 
'<div class="cent">'.$to.'<br>'.$val.'&nbsp;</div>';};};
$vt = '';
if(preg_match('/Внешняя/iu', $way))
$vt =  
'<div class="cent">Остров свободы<br>'.$valgold.'&nbsp;</div>
<div class="cent">Остров свободы<br>'.$valshell.'&nbsp;</div>
<div class="cent">Остров свободы<br>'.$valdz.'&nbsp;</div>';
echo $vt;
echo '</div></div><div class="upcontable">&nbsp;</div><div class="contable">';

//echo '<div class="row"><div class="left">Пак</div>';
//echo '</div><div class="input2">';
if($type !== 'Все')
$and = " and `pack_type` = '$type'";
else $and = '';
if(preg_match('/Запад/iu', $way))
$qpacks = qwe("SELECT * FROM `packs` WHERE `zone_id` > 0 and `side` = 1".$and." ORDER BY `zone_id`");
if(preg_match('/Восток/iu', $way))
$qpacks = qwe("SELECT * FROM `packs` WHERE `zone_id` > 0 and `side` = 2".$and." ORDER BY `zone_id`");
if($way == "Паки Изначального материка")
$qpacks = qwe("SELECT * FROM `packs` WHERE `zone_id` = 37");
$vnesh = '';
foreach($qpacks as $key){
	$pack = $key['pack_name'];
	$zone = $key['zone_name'];
	
	if(preg_match('/Внешняя/iu', $way))
	$vnesh = '<div class="cent">&nbsp;'.round(($key['zone_30']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round($key['zone_30_sh']*$per/10000,0).'&nbsp;</div><div class="right">&nbsp;'.round($key['zone_30_dz']*$per/10000,0).'&nbsp;</div>';
	if($way == "Запад (Внутренняя торговля)")
	$prices = 
	'<div class="cent">&nbsp;'.round(($key['zone_1']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_2']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_3']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_5']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_8']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_20']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_27']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	$vnesh;
	if($way == "Запад (Внешняя торговля)")
	$prices = 
	'<div class="cent">&nbsp;'.round(($key['zone_4']*$per)/10000,0).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_12']*$per)/10000,0).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_17']*$per)/10000,0).'&nbsp;</div>'.
	$vnesh;
    if($way == "Восток (Внутренняя торговля)")
	$prices = 
	'<div class="cent">&nbsp;'.round(($key['zone_4']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_7']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_9']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_11']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_12']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_16']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_17']*$per*$siolx)/10000,2).'&nbsp;</div>'.
	$vnesh;
	if($way == "Восток (Внешняя торговля)")
	$prices = 
	'<div class="cent">&nbsp;'.round(($key['zone_5']*$per)/10000,0).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_8']*$per)/10000,0).'&nbsp;</div>'.
	'<div class="cent">&nbsp;'.round(($key['zone_20']*$per)/10000,0).'&nbsp;</div>'.
	$vnesh;
	if($way == "Паки Изначального материка")
	$prices = 
	'<div class="cent">&nbsp;'.round(($key['zone_30']*$per*$siolx)/10000,2).'&nbsp;</div>';
	$item_link= str_replace(" ","+",$pack);
	$short_pack = $pack;
	if(preg_match('/компоста/iu', $pack)) $short_pack = 'Груз компоста';
	if(preg_match('/Груз меда/iu', $pack)) $short_pack = 'Груз меда';
	if(preg_match('/Груз зрелого сыра/iu', $pack)) $short_pack = 'Груз сыра';
	if(preg_match('/Груз домашней/iu', $pack)) $short_pack = 'Груз наливки';
	echo '<div class="row"><div class="left"><a href="catalog.php?query='.$item_link.'" title="'.$pack.'" style="color: #6C3F00; text-decoration: none;" target="_blank">'.$short_pack.'</div><div class="left">'.$zone.'</div>';
	//echo '<div class="cent">&nbsp;'.$side.'&nbsp;</div>';
    echo $prices.'&nbsp;</div></a>';
};
   echo '</div></div></div>';
	include_once 'pageb/footer.html';
	?>
   

</body>
</html>