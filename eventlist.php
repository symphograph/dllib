<?php
require_once 'includs/ip.php';
include("tscheck.php");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow"/>
<title>Список рейдов</title>
 <link href="css/style.css" rel="stylesheet">
 <!--<link href="css/packstable.css?ver=123" rel="stylesheet">-->
 <link href="css/eventlist6.css?ver=6" rel="stylesheet">
</head>

<body>

<?php
include 'pageb/header.html';
	$gold = '<div class="gold"><img src="img/gold.png" line-height="1px" width="15" height="15" alt="gold"/></div>';
	$reed = 'readonly'; $admin = false;
	if($group_lvl>4 or $myip) {$reed = ''; $admin = true;} 
	$query = qwe("SELECT * FROM `ev_start`");
	foreach($query as $v)
	{
		$ev_start = date("Y-m-d",strtotime($v['start_date']));
		$ev_end = date("Y-m-d",strtotime($v['end_date']));
	}
?>
<div class="topw"></div>
<div class="input1"><div class="input2"><div class="inpur">

<?php echo
'<p>Привет, '.$nick.'!</p>';
	
	$query = qwe("SELECT `bank_id`, `bank_name`, bill 
	FROM `event_types`, `event_banks` 
	WHERE `ev_categ` = `bank_id`
    AND `bank_id` > 1
    GROUP BY `bank_id` DESC");
	
?>
<div class="info"><div class="in_info">
    
<?php
	if($group_lvl>4 or $myip) echo '<form name="bills" method="post" action="edit/events/put_to_bank2.php">';
	echo '<p>С <input name="start" type="date" class="from_date" value="'.$ev_start.'" placeholder="'.$ev_start.'" '.$reed.'  autocomplete="off"> по 
	<input name="end" type="date" class="from_date" value="'.$ev_end.'" placeholder="'.$ev_end.'" '.$reed.'  autocomplete="off">
	мы заработали:</p>';
	
	
	   $banks = array(); 
	foreach($query as $v)
	{
	 $bank_id = $v['bank_id'];
	 $bank_name = $v['bank_name'];
	 $bill = $v['bill'];
	 $banks[$bank_id] = $bill;
		echo '<div class="banks"><div class="bank">'.$bank_name.'</div>
		<input type="number" class="bill" name="bill['.$bank_id.']" value="'.$bill.'" '.$reed.'>'.$gold.'</div>';
	}
	echo '<div style="clear: both; width: 300px; height: 1px;"></div>';
	if($group_lvl>4 or $myip) echo '
	<input type="hidden" name="nick" value="'.$nick.'">
	
	<input title="Для отображния этой кнопки нужно быть в ТС-группе [Глава]" value="Сохранить" class="button" type="submit"></form>';
	
	
?>	
</div></div>
<!--<details style ="width: 500px"><summary>Зряплаты</summary>-->
<div class="member_list">
<?php
	//Запрашиваем сколько на нос за 1 рейд во всех категориях
	$part_cost_q = qwe("
	SELECT `event_banks`.`bank_name`, 
	COUNT(`events`.`event_t_id`) as `ev_cnt`, 
	`event_types`.`ev_categ`, 
	round(`event_banks`.`bill`/COUNT(`events`.`event_t_id`),2) as `part_cost`, 
	`event_banks`.`bill`
	FROM `ev_members`, `event_types`,`events`, `event_banks`
	WHERE `ev_members`.`event_id` = `events`.`id`
	AND (`events`.`date` BETWEEN '$ev_start' AND '$ev_end')
	AND `event_types`.`event_t_id` = `events`.`event_t_id` 
	AND `event_banks`.`bank_id` = `ev_categ`
	GROUP BY `event_types`.`ev_categ` 
	ORDER BY `event_types`.`ev_categ` DESC, `event_id` DESC");
	if(isset($_GET['recount']) and $admin)
	qwe("TRUNCATE TABLE `ev_salary`");
	foreach($part_cost_q as $v)
	{
		$bank_id = $v['ev_categ'];
		$part_cost = $v['part_cost'];
		$bank_name = $v['bank_name'];
		echo '<details style ="width: 500px"><summary>'.$bank_name.'</summary>';
		//echo '<p><b>'.$bank_name.'</b></p>';
		$parts_cnt_q = qwe(
				"SELECT `ev_categ`, `member_id`, `ts_users`.`sh_nick` , COUNT(`events`.`id`) as `parts`
				FROM `events`, `event_types`, `ev_members`, `ts_users`
				WHERE (`events`.`date` 
				BETWEEN '$ev_start' AND '$ev_end')
				AND `ev_categ` = '$bank_id' 
				AND `events`.`event_t_id` = `event_types`.`event_t_id`
				AND `events`.`id` = `ev_members`.`event_id`
				AND `member_id` = `cldbid`
				GROUP BY `cldbid`
				ORDER BY `sh_nick`");
	       
		foreach($parts_cnt_q as $p)
		{
			$member_id = $p['member_id'];
			$sh_nick = $p['sh_nick'];
			$parts = $p['parts'];
			$salary = round($parts*$part_cost*0.9,0);
			//$sum[$sh_nick][] = $salary;
			if(isset($_GET['recount']) and $admin)
			qwe("INSERT INTO `ev_salary` (`member_id`, `bank_id`, `salary`) 
			VALUES ('$member_id', '$bank_id' ,'$salary')");
			echo '<div class="salary"><div class="salary_cell"><div class="nick">'.$sh_nick.'</div></div><div class="parts">'.$salary.'</div>'.$gold.'</div>';
		}
		
	  echo '</details>';
	}
	$query = qwe(
	"SELECT `ts_users`.`sh_nick`, sum(`salary`) as `sumsal` 
FROM `ev_salary`, `ts_users`
WHERE `member_id` = `cldbid`
GROUP BY `member_id`
ORDER BY `sh_nick`");
	
	echo '<details style ="width: 500px"><summary>Итого за всё</summary>';
	foreach($query as $v)
	{
	   $sh_nick = $v['sh_nick'];
	   $sumsalary = $v['sumsal'];
	   echo '<div class="salary"><div class="salary_cell"><div class="nick">'.$sh_nick.'</div></div><div class="parts">'.$sumsalary.'</div>'.$gold.'</div>';
	}
	
?>
</details>

</div>

<?php 
	
	
	//Пошел список рейдов
	echo '<div style="clear: both; width: 300px; height: 1px;"></div>';
	$query = qwe("
SELECT `events`.`date`, `events`.`id`, `event_types`.`event_t_id`, `ts_users`.`sh_nick`, `event_types`.`event_name` 
FROM `events`, `ts_users`, `event_types`
WHERE `ts_users`.`cldbid` = `events`.`leader`
AND `events`.`event_t_id` = `event_types`.`event_t_id`
AND (`date` BETWEEN '$ev_start' AND now())
ORDER BY `date` DESC, `event_name`");
if(mysqli_num_rows($query) > 0)		
	{	$rlimg = '<img src="img/rl.png" width="11" height="11" alt="РЛ:"/>';
		setlocale(LC_ALL, 'ru_RU.UTF-8');
//echo strftime('%S', time());
		echo '<table>';
		$delimg = '<img src="/img/del.png" width="24" height="32" title="Удалить рейд"/>';
	    $ev_edit_img = '<img src="/img/repair.png" width="27" height="27" title="Править рейд"/>';
		foreach ($query as $v)
		{
		 $ev_date = date('d.m.y',(strtotime($v['date'])));	
		 $event_name = $v['event_name'];
		 $rl = $v['sh_nick'];
		 $event_id = $v['id'];
		 if($admin)
			 {
				 $delev = '<div class="edit"><a href="edit/events/evdel.php?event_id='.$event_id.'">'.$delimg.'</a></div>';
				 $ev_edit = '<div class="edit"><a href="edit/events/ev_editor.php?event_id='.$event_id.'">'.$ev_edit_img.'</a></div>';
			 }
		 else {$delev = ''; $ev_edit = '';}
			$edit_line = '<div class="edits">'.$delev.$ev_edit.'</div>';
		 echo '<tr><td class="raid_tr" 
		 ><div class="date"><p>'.$ev_date.'</p></div><div class="event_name"><p>'.$event_name.'</p></div><p><b>'.$rlimg.' '.$rl.'</b>'
		 .$edit_line.'</p></td>
		 <td id="members">';
			$memb_q = qwe("
			SELECT `cldbid`, `sh_nick` FROM `ts_users` 
            WHERE `cldbid` in 
            (SELECT `member_id` FROM `ev_members` WHERE `event_id` = '$event_id')
			ORDER BY `sh_nick`");
			echo '<details style ="width: 500px"><summary>Участники</summary><div class ="raid">';
			$i=1;
				foreach($memb_q as $n)
			{	
			   $member = $n['sh_nick'];
				if(preg_match('/_no_ts/',$member))
		 $member = str_replace('_no_ts','',$member);
				if($i == 1)
				{echo '<div class="raid_col">';}
			echo '<div class="raid_cell"><div class="nick">'.$member.'</div></div>';
			   if($i == 5)
				{echo '</div>';$i=1; continue;}
				$i++;
			}
		 
		 echo '</div></details></td></tr>';
		}
		echo '</table>';

	}


  
  
 ?> 
   </div></div></div>
   <?php
	include_once 'pageb/footer.php';
	?>
   

</body>
</html>