<?php
$side_to_id = '';
$zone_to_id = '';
$zone_from_id = '';
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
if(isset($_POST['old_side']) and ctype_digit($_POST['old_side'])) $old_side = $_POST['old_side'];
echo '<input type="hidden" name="old_side" value="'.$side_to_id.'" autocomplete="off">';
if(($old_side != $side_to_id or $zone_to_id == $zone_from_id) 
   and !isset($_GET['new_side']))
			 
			 {
				echo '<meta http-equiv="refresh" content="0; url=packs_get.php?new_side='.$side_to_id.'&new_zone='.$zone_to_id.'">';
		          exit();
			 }
?>
<div class="select_row"><div class="select_name">На какой материк</div><div class="select">
	<select name="side_to" class="select_input" autocomplete="off" onchange="this.form.submit()" title="На какой материк">
		<!--<option value="<?php //echo $side_to_id;?>" selected>На какой материк</option>-->
		<?php
		include 'includs/config.php';
		   $query = qwe("SELECT * FROM `sides` WHERE side_id != 9");
		       if(!isset($_POST['side_to']))
				echo '<option value="" selected>На какой материк</option>';   
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
	
</div></div>
<?php 

	if($side_to_id > 0 and $side_to_id != 3)
		{	echo
'<div class="select_row"><div class="select_name">Куда</div><div class="select" title="Куда">
	<select name="zone_to" class="select_input" autocomplete="off" onchange="this.form.submit()">';
		 
		 $query = qwe("SELECT * FROM `zones` WHERE `side` = '$side_to_id' and `is_get` = '1' ORDER BY `zone_name`");
		    if($zone_to_id == '')
				echo '<option value="" selected>Куда</option>';
			foreach($query as $v)
			{     
				  $selected = '';
				  $zone_id = $v['zone_id'];
				  //if($zone_id == $zone_from_id) continue;
				  if($zone_id == $zone_to_id) $selected = 'selected';
				  $zone_name = $v['zone_name'];
				echo '<option value="'.$zone_id.'" '.$selected.'>'.$zone_name.'</option>';
			}
		echo
	'</select>
</div></div>';
		 
	   }

		if($zone_to_id and $side_to_id != 3)
		{	echo
'<div class="select_row"><div class="select_name">Откуда</div><div class="select" title="Откуда">
	<select name="zone_from" class="select_input" autocomplete="off" onchange="this.form.submit()">';
		
		 $query = qwe("SELECT * FROM `zones` WHERE `side` = '$side_to_id' and `zone_id` != '$zone_to_id' ORDER BY `zone_name`");
		    if($zone_from_id == '' or $zone_to_id == $zone_from_id)
				echo '<option value="" selected>Откуда</option>';
		        echo '<option value="100">Показать все</option>';
			foreach($query as $v)
			{     
				  $selected = '';
				  $zone_id = $v['zone_id'];
				  if($zone_id == $zone_from_id and $zone_to_id != $zone_from_id) $selected = 'selected';
				  $zone_name = $v['zone_name'];
				echo '<option value="'.$zone_id.'" '.$selected.'>'.$zone_name.'</option>';
			}
		 
		echo
	'</select>
</div></div>';
		 
		}
			?>
