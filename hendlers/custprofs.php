<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functs.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';

$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die('<span style="color: red">Oh!<span>');
extract($userinfo_arr);
$user_id = $muser;
?>
<div class="prof_area">

<form action="edit/set_prof.php" method="post" name="profs">
<div class="prof_cells">
<h3>Ремесло</h3>
<p>Уровни прокачки профессий</p>
<?php
$query = qwe("SELECT 
`profs`.`prof_id`, 
`profession`, 
`lvl` 
FROM `profs`
LEFT JOIN `user_profs` 
ON `profs`.`prof_id` = `user_profs`.`prof_id`
AND `user_id` = '$user_id'
WHERE `used` = 1");
foreach($query as $p)
{
	$prof_id = $p['prof_id'];
	$prof_name = $p['profession'];
	$prof_name2 = str_replace(' ','_',$prof_name);
	$lvl = $p['lvl'];
	?>
	<div class="prof_cell">
	<div class="prof_cellin">
		<div class="prof_img" style="background-image: url(img/profs/<?php echo $prof_name2;?>.png)">
			<div class="PrOk" id="PrOk_<?php echo $prof_id;?>"></div>
		</div>
		<div class="prof_pharams">
		<label for="prof_<?php echo $prof_id;?>">
		<span class="item_name"><?php echo $prof_name;?></span>
		</label><br>
		<select name="prof[<?php echo $prof_id;?>]" id="prof_<?php echo $prof_id;?>" class="server" onchange="SetProf(<?php echo $prof_id;?>)">
		<?php
		$query_pr = qwe("SELECT `lvl`, CONCAT(round(`min` /1000,0),'к - ',round(`max`/1000,0),'к') as `lvl_descr` FROM `prof_lvls`;");
		SelectOpts($query_pr, 'lvl', 'lvl_descr', $lvl,false);
		?>
		</select>
		</div>
		<div class="clear"></div>
	</div>
	</div>
<?php
}
?>
<div class="clear"></div>
</div>
</form>
<div class="clear"></div>
</div>
