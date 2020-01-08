<?php
require_once 'includs/ip.php';
//include("tscheck.php");

?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow"/>
<title>Шаблон</title>
 <link href="css/defolt.css?ver=3" rel="stylesheet">
 <link href="css/big_window.css?ver=3" rel="stylesheet">
 <link href="css/packs_get.css?ver=3" rel="stylesheet">
</head>

<body>

<?php
include 'pageb/header.html';
	

?>
<div class="topw"></div>
<div class="input1"><div class="input2"><div class="inpur">
<div class="winhead"></div>
<div class="select_area">
<form method="post" action="packs_get.php" name="locations">
<?php include 'functions/packs_menu_t1.php'; 
	
	?>
</form></div>

<?php
	include 'functions/packs_list_t1.php';
	?>
	</div>
</div></div></div>
<?php
	include_once 'pageb/footer.php';
	
	?>
	
</body>
</html>