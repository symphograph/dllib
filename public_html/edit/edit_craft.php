<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
if(!$myip) die;
?>

<html lang="ru">
<head>
<meta charset="utf-8">
<title>Редактор рецепта</title>
 <link href="../css/style.css" rel="stylesheet">
 <script src="//yandex.st/jquery/1.7.2/jquery.min.js"></script>
 <script type="text/javascript" src="../TextChange2.js"></script>
<link href="../css/Search.css" rel="stylesheet">

</head>

<body>
<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
include_once '../pageb/header.html';
if(empty($_POST['craft_id']))
exit();

	require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php'; // Подключение к БД.
 $craft_id = $_POST['craft_id'];
$q_craft = qwe("SELECT * FROM crafts where craft_id = '$craft_id'");
$q_maters = qwe("SELECT * FROM craft_materials where craft_id = '$craft_id'");
$arrcraft = mysqli_fetch_assoc($q_craft);
$profession = $arrcraft['profession'];
$profneed = $arrcraft['prof_need'];
$dood_name = $arrcraft['dood_name'];
//$dood_id = $arrcraft['dood_id'];
$rec_name = $arrcraft['rec_name'];
$result_item_name = $arrcraft['result_item_name'];
$result_amount = $arrcraft['result_amount'];
$item_id = $arrcraft['result_item_id'];



?>		
	<div class="top"></div>
	<div id="rent">
		
			
      <div class="top"></div>
    
       
       <form action="" method="GET">
       <div class="search_area">
            <input type="text" name="query" id="search_box" onchange="submit" value="<?php	 if (isset($_GET['query']))
	{$itemq = mysqli_real_escape_string($dbLink,trim($_GET['query']));}?>" autocomplete="off">
     <div id="search_advice_wrapper"></div>
     </div>
     </form>
   
        
	<form action="edit_craft_send.php" method="POST">
      <div class="line"></div>
      <div class="money_area">
    
            <div class="money-line">
     
    <input type="hidden" name="item_id" id="search_box" 
value="<?php echo $item_id; ?>" autocomplete="off" dislay="none">
           </div> 
          <?php echo '<div class="top_itimset" style="background-image: url(../img/icons/'.$item_id.'.png)"></div>';
	?>
            
     </div>
     <br><br>
    <center> <hr width="320">
   <div class="confirm">
   <p>Профессия:<Br>
   <input type="text" name="prof" value="<?php	 
	echo $profession;
	 ?>" autocomplete="off"></p>
    <p>Приспособление:<Br>
   <input type="text" name="dood" value="<?php	 
	echo $dood_name;
	 ?>" autocomplete="off"></p>
     <p>Имя рецепта:<Br>
   <input type="text" name="dood" value="<?php	 
	echo $rec_name;
	 ?>" autocomplete="off"></p>
   </div><br>
   <hr width="320"><br>
   <div id="ok">
   <input type="submit" value="" id="ok"></div>
  
   <input type="submit" id="cancel" value="" name="cancel">
   
   </center> 
</form>

<div class="rent_count"><div class="rent_count_in">
<?php

$countmats = mysqli_num_rows($q_maters);



   foreach($q_maters as $arrmats) 
   {
   ?>
	<p>
	<div>
		<input type="hidden" name="item_id" value="<?php echo $arrmats['item_id']?>" autocomplete="off" dislay="none">
		<input type="text" name="item_name" size="30" value="<?php echo $arrmats['item_name']?>" autocomplete="off" dislay="none">
		<input type="number" name="mater_need" size="5" value= "<?php echo $arrmats['mater_need']?>">
	</div>
	</p>
   <?php
   }
?>
</div></div></div>
<?php
include_once '../pageb/footer.html';
	?>
    
</body>
</html>