<?php 
if(!preg_match('/catalog.php/', $_SERVER['HTTP_REFERER']))
echo '<meta http-equiv="refresh" content="0; url=../index.php">';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta charset="utf-8">
<title>Крафт</title>
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
if(!empty($_GET['item_id']) or !empty($_POST['item_id'])){
	include "../includs/config.php"; // Подключение к БД.
		if(!empty($_GET['item_id']))
		{$item_id=trim($_GET['item_id']);
	  $setgold=$_GET['setgold'];
	  $setname=$_GET['setname'];
	  $setsilver = $_GET['setsilver'];
	  $setbronze = $_GET['setbronze'];
	  $user = $_GET['user'];};
         if(!empty($_POST['item_id']))
		{$item_id=$_POST['item_id'];
		  $setname=$_POST['setname'];
		 $setgold='';
	     $setsilver = '';
	     $setbronze = '';
		 $user = $_GET['user'];
		};
		
$gold = '<img src="../img/gold.png" width="15" height="15" alt="gold"/>';
 $silver = '<img src="/..img/silver.png" width="15" height="15" alt="gold"/>';
 $bronze = '<img src="../img/bronze.png" width="15" height="15" alt="gold"/>';
//$setgold=2;
//$setsilver = 94;
//$setbronze = 56;
$setprise = $setgold*10000+$setsilver*100+$setbronze;
$query = mysqli_escape_string($setprise);
		//$request  = qwe("UPDATE items SET price = '$query' where item_name =  '$item_id'");
?>		
	<div class="top"></div>
	<div id="rent">
		<form action="setprise-control.php" method="POST">
			
      <div class="top"></div>
    
       <div class="search_area">
            <input readonly type="text" name="setname" id="search_box" value="<?php	 
	echo $setname;
	 ?>" autocomplete="off">
    <!--<input type="submit" value="" id="search_bt">-->
			<!--<br><br>-->
		<!--<div id="search_advice_wrapper"></div>-->
        
	</div>
      <div class="line"></div>
      <div class="money_area">
            <div class="money-line">
            <input type="number" name="setgold"
    value= "<?php echo trim($_GET['setgold']); ?>" id="gold" autocomplete="off" placeholder="Цена на ауке" max="1000000"
    oninvalid="this.setCustomValidity('Фигасе!')" 
    oninput="setCustomValidity('')">
            </div>
            <div class="money-line">
            <input type="number" name="setsilver" 
    value= "<?php echo trim($_GET['setsilver']); ?>" id="silbro" autocomplete="off" max="99">
            </div>
    
            <div class="money-line">
           <input type="number" name="setbronze" 
    value= "<?php echo trim($_GET['setbronze']); ?>" id="silbro" autocomplete="off" max="99">
    <input type="hidden" name="item_id" id="search_box" 
value="<?php echo $item_id; ?>" autocomplete="off" dislay="none">
           </div> 
          <?php echo '<div class="top_itimset" style="background-image: url(../img/icons/'.str_replace(' ','_',$item_id).'.png)"></div>';
	?>
            
     </div>
     <br><br>
    <center> <hr width="320">
   <div class="confirm">
   
   <?php echo $user;?>, точно?<br>Проверь, действительно ли эта цена сейчас на аукционе?
   </div><br>
   <hr width="320"><br>
   <div id="ok">
   <input type="submit" value="" id="ok"></div>
  
   <input type="submit" id="cancel" value="" name="cancel">
   
   </center> 
</form>

</div>
<?php
/*if($_GET['item_id'])
{$item_id = $_GET['setgold'];
$setprice = $_GET['setgold']*10000 +$_GET['setsilver']*100+$_GET['setbronze'];
qwe("UPDATE items SET price = '$setprice' where item_name =  '$item_id'");}*/

include_once '../pageb/footer.html';
	exit();}
echo $_GET['item_id'].'<br>';		
echo $_GET['setgold'].'<br>';
echo $_GET['setsilver'].'<br>';
echo $_GET['setbronze'].'<br>';	
	exit();
	?>
</body>
</html>