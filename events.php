<?php
include 'includs/ip.php';
include("tscheck.php");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow"/>
<title>Мероприятия</title>
 <link href="css/style.css" rel="stylesheet">
 <link href="css/packstable.css?ver=123" rel="stylesheet">
</head>

<body>

<?php
include 'pageb/header.html';
require_once 'includs/config.php';
?>
<div class="topw"></div>
<div class="input1"><div class="inpur">
<?php echo
'<p>Привет, '.$nick.'!</p>';
	if($cldbid >0)
{
	$query = qwe("
SELECT `event_name`, COUNT(`events`.`event_t_id`) as `ev_cnt`, `event_types`.`event_t_id`   
FROM `ev_members`, `event_types`,`events`
WHERE `member_id` = '$cldbid' 
AND `ev_members`.`event_id` = `events`.`id`
AND `events`.`date` BETWEEN '2017-04-16' AND NOW()
AND `event_types`.`event_t_id` = `events`.`event_t_id` 
GROUP BY `events`.`event_t_id`
ORDER BY `event_types`.`ev_categ`, `event_name`");
	
		if(mysqli_num_rows($query) > 0)
	{	$query_banks = qwe("SELECT * FROM `ev_banks_log`");
		echo 'В текущем периоде ты участвовал в:';
		foreach ($query as $v)
		{
		 $event_name = $v['event_name'];
		 $ev_cnt = 	$v['ev_cnt'];
		 $event_t_id = $v['event_t_id'];
		 echo '<p>'.$event_name.' x'.$ev_cnt.'</p>';
		}
		echo 'Твоя доля:';

	}
}

  
  
 ?> 
   </div></div>
   <?php
	include_once 'pageb/footer.html';
	?>
   

</body>
</html>