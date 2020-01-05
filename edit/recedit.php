<?php 
include '../includs/ip.php';
if(!$myip) exit(); 
include_once('../functions/functs.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta charset="utf-8">
<title>Редактор рецепта</title>
 <link href="../css/style-recedit.css" rel="stylesheet">
<link href="../css/recedit.css" rel="stylesheet">

</head>

<body>
<?php
include_once '../pageb/header.html';
	if(!empty($_GET['off']))
	{   include "../includs/config.php";
		$craft_id = intval($_GET['off']);
	    $url_from = $_SERVER['HTTP_REFERER'];
		qwe("UPDATE `crafts` SET `on_off` = 0 WHERE `craft_id` = '$craft_id'");
		include '../functions/craftable.php';
		echo '<meta http-equiv="refresh" content="0; url='.$url_from.'">';
	 exit();
		
	}
		
if(empty($_GET['query']) and empty($_GET['addrec']))
exit();
	include "../includs/config.php"; // Подключение к БД.
 if(!empty($_GET['addrec']))
 {
	 $item_id = intval($_GET['addrec']);
	 $query = qwe("SELECT * FROM `crafts` where `my_craft` = 1 ORDER BY `craft_id` DESC LIMIT 1");
	 foreach($query as $v)
	 {
		 $new_craft_id = $v['craft_id']+1;
	 }
	 qwe("
	 INSERT INTO `crafts` 
	 (`craft_id`,
	 `result_item_id`, 
	 `result_item_name`, 
	 `my_craft`) 
	 VALUES 
	 ('$new_craft_id',
	 '$item_id', 
	 (SELECT DISTINCT `item_name` AS `col1` FROM `items` WHERE `item_id`='$item_id'), '1')
	 ");
	 
	 qwe("UPDATE `items` set `craftable`=1 where `item_id`='$item_id'");
	 $q_craft = qwe("SELECT `craft_id` FROM `crafts` where `result_item_id` = '$item_id' order by `craft_id` DESC LIMIT 1");
	 $arrcraft = mysqli_fetch_assoc($q_craft);
	 $craft_id = $arrcraft['craft_id'];
	 echo '<meta http-equiv="refresh" content="0; url=recedit.php?query='.$craft_id.'"">';
	 exit();
}
	
if(!empty($_GET['query']))
{
	$craft_id = $_GET['query'];
	$q_craft = qwe("SELECT * FROM `crafts` where `craft_id` = '$craft_id'");
	$arrcraft = mysqli_fetch_assoc($q_craft);
	extract($arrcraft);
	$item_id = $result_item_id;
};

	

?>		
	<div class="top"></div>
	<div id="rent">
		
			
      <div class="top"></div>
    
       
   
        
	<form action="edit_craft_send.php" method="POST">
      <div class="line">
      
    
      
          <?php echo '<div class="top_itimset" style="background-image: url(../img/icons/'.$item_id.'.png)"></div>';
		  echo $result_item_name;
	?>
            
     </div>
     <br><br>
    <hr width="320">
   <div class="confirm">
   <p>Результат:<Br>
   <input type="text" name="result_item_name" value="<?php	 
	echo $result_item_name;
	 ?>" autocomplete="off"></p>
	 <p>Количество:<Br>
   <input type="number" name="result_amount" value="<?php	 
	echo $result_amount;
	 ?>" autocomplete="off"></p>
	 
<br><label for="price_type">Профессия</label><br>
<select name="prof" id="prof" autocomplete="off">
<?php
	$query = qwe("SELECT * FROM `profs` ORDER BY `profession`");
	SelectOpts($query, 'prof_id', 'profession', $prof_id, 'Не выбрана');
?>
</select><br>
   
    <p>Приспособление:<Br>
   <input type="text" name="dood_name" value="<?php	 
	echo $dood_name;
	 ?>" autocomplete="off"></p>
     <p>Имя рецепта:<Br>
   <input type="text" name="rec_name" value="<?php	 
	echo $rec_name;
	 ?>" autocomplete="off"></p>
	 <p>Очков работы:<Br>
   <input type="number" name="labor_need" value="<?php	 
	echo $labor_need;
	 ?>" autocomplete="off"></p>
	<p>Длительность (мин):<Br>
   <input type="number" name="mins" value="<?php	 
	echo $mins;
	 ?>" autocomplete="off"></p>
   </div><br>
   <input type="hidden" name="craft_id" value="<?php	 
	echo $craft_id;
	 ?>" autocomplete="off">
	 <input type="hidden" name="result_item_id" value="<?php	 
	echo $item_id;
	 ?>" autocomplete="off">
   <hr width="320"><br>
   
   

<div class="rent_count"><div class="rent_count_in">
<?php
$i=0;
$q_maters = qwe("SELECT `craft_materials`.`item_id`, `craft_materials`.`mater_need`, `items`.`item_name` 
FROM `craft_materials`, `items` 
where `craft_materials`.`item_id`= `items`.`item_id`
AND `craft_materials`.`craft_id` = '$craft_id'");
foreach($q_maters as $v){
	
	$mat_id = $v['item_id'];
	$mat_need = $v['mater_need'];
	$mat_name = $v['item_name'];
   echo '<div class="itemline"><div class="itemprompt" data-title="'.$mat_name.' x '.$mat_need.'"><div class="itim" style="background-image: url(../img/icons/'.$mat_id.'.png)"><div class="itdigit"><input style="width: 35px; background-color: transparent; border-color: transparent; color: white; text-shadow: -1px -1px 5px #010101;
	text-align: right;" type="text" name="mater_need['.$mat_id.']" value= "'.$mat_need.'"><input type="checkbox" name="del[]" value="'.$mat_id.'"
	style="background-color: transparent; border-color: transparent;"
	"></div></div></div></div>';
$i++;
} 
	
for($c=0;$c<12-$i;$c++)
{
?>
<div class="itemline">
	<div class="itemprompt" data-title="Добавить">
		<div class="itim">
			<div class="itdigit">
			<input style="width: 35px; background-color: transparent; border-color: transparent; color: white; text-shadow: -1px -1px 5px #010101;
			text-align: right;" name="newmat[]" value= "">
			<input style="width: 35px; background-color: transparent; border-color: transparent; color: white; text-shadow: -1px -1px 5px #010101;
			text-align: right;" name="newmatneed[]" value= "">
			</div>
		</div>
	</div>
</div>
<?php
};
?>

<br></div><br></div>
<br><br>
<div class="ok-cancel">
   <input type="submit" value="" id="ok">
   <input type="submit" id="cancel" value="" name="cancel"><br><br>
</div>
   </div>
   </form>
<?php
include_once '../pageb/footer.html';
	?>
    
</body>
</html>