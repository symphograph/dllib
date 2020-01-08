<?php 
require_once 'includs/ip.php';
if(!$myip) exit();
exit();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>ищем обсидиан</title>
</head>

<body>
<details>
<?php
require_once 'includs/config.php';

$query = qwe("
SELECT * FROM `items`
WHERE `roll_group` = 2
AND `lvl` = 6
AND `on_off` = 1
AND categ_id != 119
AND `personal` != 1
ORDER BY categ_id, slot
");
foreach($query as $q)
{
	$result_name = $q['item_name'];
	$result_id = $q['item_id'];
	echo '<b>'.$result_name.'</b><br>';
	
	$query2 = qwe("SELECT * FROM `items` 
	WHERE slot = ".$q['slot']." 
	AND `roll_group` = 2
	AND `personal` = 1
	AND categ_id = ".$q['categ_id']."
	AND `on_off` = 1
	AND lvl = ".($q['lvl'])."
	ORDER BY lvl
	");
	
	foreach($query2 as $q2)
	{
		echo 'должен получаться из<br>';
		echo $q2['item_name'].'<br>';
		$mater_id = $q2['item_id'];
		echo 'по рецепту получается из<br>';
		$query3 = qwe("
		SELECT * FROM crafts WHERE result_item_id = '$result_id'
		");
		if(mysqli_num_rows($query3) >0)
		{
			echo '<p>Найдено рецептов: '.mysqli_num_rows($query3).'</p>';
			foreach($query3 as $q3)
			{
				$query4 = qwe("
				SELECT * FROM craft_materials 
				WHERE craft_id = ".$q3['craft_id']."
				");
				if(mysqli_num_rows($query4) == 2)
				foreach($query4 as $q4)
				{
					if($q4['item_id'] == $mater_id)
						echo '<p>'.$q3['craft_id'].': правильный рецепт</p>';
				}
				else
				{
				echo '<p>'.$q3['craft_id'].': плохой рецепт</p>';
				qwe("
				UPDATE `crafts` SET `on_off` = 0 WHERE `craft_id` = ".$q3['craft_id']."
				");
				}
			}
		}
/*
		$query3 = qwe("
		SELECT * FROM crafts WHERE result_item_id = '$result_id' AND craft_id not in 
		(SELECT craft_id FROM craft_materials WHERE result_item_id = '$result_id')
		");
		foreach($query3 as $q3)
		{
			echo $q3['craft_id'].'<br>';
		}
*/			
		$cridnext = qwe("SELECT max(craft_id)+1 as next_id FROM crafts WHERE craft_id > 1000000 AND craft_id < 2000000");
		foreach($cridnext as $next)
		{
			$next_id = $next['next_id'];
			echo 'Новый рецепт '.$next_id.'<br>';
		}
	
		
		$insertq = qwe("INSERT INTO `crafts` 
		(`craft_id`, 
		`rec_name`, 
		`dood_id`, 
		`dood_name`, 
		`result_item_id`, 
		`result_item_name`, 
		`labor_need`, 
		`prof_need`, 
		`result_amount`, 
		`my_craft`, 
		`on_off`,
		`craft_time`)
		VALUES 
		('$next_id', 
		'$result_name', 
		'', 
		'', 
		'$result_id', 
		'$result_name', 
		'500', 
		'', 
		'1', 
		'1', 
		'1',
		'')
		");
		qwe("INSERT INTO `craft_materials` 
			(`craft_id`,`item_id`,`result_item_id`,`mater_need`,`item_name`,`result_item_name`)
			VALUES
			('$next_id','$mater_id','$result_id','1',
			(SELECT `item_name` FROM `items` WHERE `item_id` = '$mater_id'),
			(SELECT `item_name` FROM `items` WHERE `item_id` = '$result_id')
			)
			");
		
		qwe("INSERT INTO `craft_materials` 
			(`craft_id`,`item_id`,`result_item_id`,`mater_need`,`item_name`,`result_item_name`)
			VALUES
			('$next_id','44814','$result_id','1',
			(SELECT `item_name` FROM `items` WHERE `item_id` = '44814'),
			(SELECT `item_name` FROM `items` WHERE `item_id` = '$result_id')
			)
			");
		
	}
}
?></details><?php
exit();
function IsHaveCraft($item_id)
{
	$query = qwe("Select * FROM `crafts` WHERE `result_item_id` = '$item_id'");
	$have = (mysqli_num_rows($query)> 0);
	if($have)
	{
		echo 'Есть рецепт ('.mysqli_num_rows($query).')';
		
		foreach($query as $q)
		{
			echo '<details>';
			$mess = false;
			$qcraft = qwe("SELECT
			`craft_materials`.`mater_need`,
			`items`.`item_name`
			FROM `craft_materials`
			INNER JOIN items ON craft_materials.item_id = items.item_id
			WHERE craft_id = ".$q['craft_id']."");
			if(mysqli_num_rows($qcraft) > 0)
			foreach($qcraft as $qc)
			{
				echo $qc['item_name'].' x '.$qc['mater_need'].'<br>';
			}
			else
				$mess = 'Потерял материалы';
			
			echo $mess.'</details>';
		}
		
	}
	else
		echo 'Нет рецепта';
	return $mess;
}
$query = qwe("
SELECT * FROM `items` 
WHERE `roll_group` = 2  
AND on_off = 1 
AND categ_id !=119
AND lvl = 7
");
foreach($query as $q)
{
	echo '<b>'.$q['item_name'].'</b><br>';
	$mess = IsHaveCraft($q['item_id']);
	$query2 = qwe("
	SELECT * FROM `items` 
	WHERE lvl=7 AND `roll_group` = 2 
	AND on_off = 1 
	AND categ_id =119
	AND slot = ".$q['slot']."
	AND categ_pid = ".$q['categ_id']."
	");
	foreach($query2 as $q2)
	{
		echo '<ul>2<br>'.$q2['item_name'].'<br>';
		IsHaveCraft($q2['item_id']);
		$query3 = qwe("
		SELECT * FROM `items` 
		WHERE lvl=6 AND `roll_group` = 2 
		AND on_off = 1 
		AND categ_id !=119
		AND slot = ".$q2['slot']."
		AND categ_id = ".$q2['categ_pid']."
		AND personal != 1
		");
		foreach($query3 as $q3)
		{
			echo '<ul>3<br>';
			echo $q3['item_name'].'<br>';
			IsHaveCraft($q3['item_id']);
			$query4 = qwe("
			SELECT * FROM `items` 
			WHERE lvl=6 AND `roll_group` = 2 
			AND on_off = 1 
			/*AND categ_id =119*/
			AND slot = ".$q3['slot']."
			AND categ_id = ".$q3['categ_id']."
			AND personal = 1
			");
			foreach($query4 as $q4)
			{
				echo '<ul>4<br>';
				echo $q4['item_name'].'<br>';
				IsHaveCraft($q4['item_id']);
				$query5 = qwe("
				SELECT * FROM `items` 
				WHERE lvl=6 AND `roll_group` = 2 
				AND on_off = 1 
				AND categ_id =119
				AND slot = ".$q4['slot']."
				AND categ_pid = ".$q4['categ_id']."
				AND personal = 1
				");
				foreach($query5 as $q5)
				{
					echo '<ul>5<br>';
					echo $q5['item_name'].'<br>';
					IsHaveCraft($q5['item_id']);
					$query6 = qwe("
					SELECT * FROM `items` 
					WHERE lvl=5 AND `roll_group` = 2 
					AND on_off = 1 
					AND categ_id !=119
					AND slot = ".$q5['slot']."
					AND categ_id = ".$q5['categ_pid']."
					AND personal != 1
					");
					//echo '$query6';
					foreach($query6 as $q6)
					{
						echo '<ul>6<br>';
						echo $q6['item_name'].'<br>';
						IsHaveCraft($q6['item_id']);
						$query7 = qwe("
						SELECT * FROM `items` 
						WHERE lvl=5 AND `roll_group` = 2 
						AND on_off = 1 
						AND categ_id !=119
						AND slot = ".$q6['slot']."
						AND categ_id = ".$q6['categ_id']."
						AND personal = 1
						");
						foreach($query7 as $q7)
						{
							echo '<ul>7<br>';
							echo $q7['item_name'].'<br>';
							IsHaveCraft($q7['item_id']);
							$query8 = qwe("
							SELECT * FROM `items` 
							WHERE lvl=4 AND `roll_group` = 2 
							AND on_off = 1 
							AND categ_id !=119
							AND slot = ".$q7['slot']."
							AND categ_id = ".$q7['categ_id']."
							/*AND personal = 1*/
							");
							foreach($query8 as $q8)
							{
								echo '<ul>8<br>';
								echo $q8['item_name'].'<br>';
								IsHaveCraft($q8['item_id']);
								echo '</ul>';
							}
							echo '</ul>';
						}
						echo '</ul>';
					}
					echo '</ul>';
				}
				echo '</ul>';
			}
			echo '</ul>';

		}
		echo '</ul>';
	}
}
?>

</body>
</html>