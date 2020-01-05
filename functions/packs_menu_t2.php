<?php
$side_to_id = 1;
$zone_to_id = '';
$zone_from_id = 100;
if(isset($_POST['side_to']) and ctype_digit($_POST['side_to']))
	$side_to_id = $_POST['side_to'];

if(isset($_POST['zone_to']) and ctype_digit($_POST['zone_to']))
	$zone_to_id = $_POST['zone_to'];
if(isset($_POST['zone_from']) and ctype_digit($_POST['zone_from']))
	$zone_from_id = $_POST['zone_from'];
if(isset($_GET['new_side']) and ctype_digit($_GET['new_side']))
	$side_to_id = $_GET['new_side'];
if(isset($_GET['new_zone']) and ctype_digit($_GET['new_zone']))
	$zone_to_id = $_GET['new_zone'];
$old_side = '';

/*
if(isset($_POST['old_side']) and ctype_digit($_POST['old_side'])) $old_side = $_POST['old_side'];
echo '<input type="hidden" name="old_side" value="'.$side_to_id.'" autocomplete="off">';

if(($old_side != $side_to_id or $zone_to_id == $zone_from_id) 
   and !isset($_GET['new_side']))
			 
			 {
				echo '<meta http-equiv="refresh" content="0; url=packtable.php?new_side='.$side_to_id.'&new_zone='.$zone_to_id.'">';
		          exit();
			 }
	*/	 
?>
<div class="select_row">
<!--<div class="select_name">Материк</div>-->
	<div class="select"><select name="side_to" class="select_input" autocomplete="off" onchange="this.form.submit()" title="На какой материк">
		<!--<option value="<?php //echo $side_to_id;?>" selected>На какой материк</option>-->
		<?php
		//include 'includs/config.php';
		   $query = qwe("SELECT * FROM `sides` WHERE side_id != 9");
		      /* if(!isset($_POST['side_to']))
				echo '<option value="" selected>Материк</option>';  
			  */ 
			   foreach($query as $v)
			   {
				   $selected = '';
				   $side_id = $v['side_id'];
				   //$zone_id = $v['zone_id'];
				   if($side_id == $side_to_id) $selected = 'selected';
				   $side_name = $v['side_name'];
				   echo '<option value="'.$side_id.'" '.$selected.'>'.$side_name.'</option>';
			   }
		?>
	</select>
	
</div>
<?php 
/*

		if($side_to_id > 0 and $side_to_id != 3)
		{	echo
'
<div class="select" title="Откуда">
	<select name="zone_from" class="select_input" autocomplete="off" onchange="this.form.submit()">';
		//$hgfd = '<div class="select_name">Откуда</div>';
		 $query = qwe("SELECT * FROM `zones` WHERE `side` = '$side_to_id' ORDER BY `zone_name`");
		  
		 
			foreach($query as $v)
			{     
				  $selected = '';
				  $zone_id = $v['zone_id'];
				  if($zone_id == $zone_from_id) $selected = 'selected';
				  $zone_name = $v['zone_name'];
				echo '<option value="'.$zone_id.'" '.$selected.'>'.$zone_name.'</option>';
			}
		 $selected = '';
		 if($zone_from_id == 100)$selected = 'selected';
		 echo '<option value="100" '.$selected.'>Показать все</option>';
		echo
	'</select>
</div>';
		 
		}
*/	
	
	$siol = 0; $x_siol = '<b>X</b>'; $siol_on_off = 0; $siol_title = 'Включить Сиоль';
	//if(isset($_POST['siol']) and ctype_digit($_POST['siol'])) $siol = $_POST['siol'];
	if((isset($_POST['siol']) and $_POST['siol'] == 5) or (isset($_POST['siol_on_off']) and $_POST['siol_on_off'] == 0))
		{
			$siol = 5;
			$x_siol = ' ';
			$siol_on_off = 1;
			$siol_title = 'Отключить Сиоль';
		}
	if(isset($_POST['siol_on_off']) and $_POST['siol_on_off'] == 1) 
		{$siol = 0;
		$siol_on_off = 0;
		$x_siol = '<b>X</b>';
		$siol_title = 'Включить Сиоль';
		}
	    
	echo '<div class="siol_div"><button type="submit" class="siol" title="'.$siol_title.'" name="siol_on_off" onClick="document.forms.form.submit() (.form name="locations")" value="'.$siol_on_off.'">'.$x_siol.'</button>
	<input type="hidden" name="siol" value="'.$siol.'"></input></div>';
			?>

</div>