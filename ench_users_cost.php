<?php
include 'includs/ip.php';
include 'includs/config.php';
$query = qwe("
SELECT
`items`.`item_id`,
`items`.`item_name`,
`items`.`category`,
`enchant_cost`.`grade`,
`enchant_cost`.`cost`,
`grades`.`gr_name`,
`grades`.`color`,
`mailusers`.`first_name`
FROM
`enchant_cost`
INNER JOIN `items` ON `enchant_cost`.`item_id` = `items`.`item_id`
INNER JOIN `grades` ON `enchant_cost`.`grade` = `grades`.`id`
INNER JOIN `mailusers` ON `enchant_cost`.`mail_id` = `mailusers`.`mail_id`
WHERE `enchant_cost`.`cost` > '20000' 
AND `enchant_cost`.`mail_id` > 1
ORDER BY `roll_group`, `item_id`, `grade`
");
	
?>
<!doctype html>
<html>
<head>
<style>
	.td{
		padding: 10px;
	}
	.top{
		position: fixed;
		background-color: antiquewhite;
	}
	.cost{
		text-align: right;
	}
</style>
<meta charset="utf-8">
<title>Цены клика</title>
</head>
<body>
<table align="center"><tbody>
<col width=200px> <col width=170px> <col width=130px> <col width=100px>
<tr class="top"><td width=200px><b>Предмет</b></td><td width=170px><b>Грейд</b></td><td width=130px><b>Цена клика</b></td><td width=100px><b>Сохранил</b></td></tr>
<tr><td>.</td><td>.</td><td>.</td><td>.</td></tr>
<?php
foreach($query as $v)
{
	$cost = $v['cost'];
	$cnt = strlen(count_chars($cost,3));
	if($cnt < 2) continue;
	$item_id = $v['item_id'];
	$item_name = $v['item_name'];
	$categ = $v['category'];
	$gr_name = $v['gr_name'];
	$color = $v['color'];
	$autor = $v['first_name'];
	echo '<tr><td class="td">'.$item_name.'</td><td class="td" style="color:'.$color.'">'.$gr_name.'</td><td class="td cost">'.$cost.'</td><td class="td">'.$autor.'</td></tr>';
}
?>
</tbody></table>
</body>
</html>