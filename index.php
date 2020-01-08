<?php 
require_once 'includs/ip.php';
//if(!isset($_COOKIE['cldbid']))
 include_once 'tscheck.php';
//if($ip == '37.194.65.246')exit();
//echo $ip;
 if($verif < 1){
 echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="yandex-verification" content="4878c37eb34cedcf" />';	 
 echo '<meta http-equiv="refresh" content="0; url=../invite.php">';
 exit();};
 ?>
<!doctype html>
<html>
 <head>
 <meta name="yandex-verification" content="4878c37eb34cedcf" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=0.35">
  <title>Dead Legion</title>
  <link href="css/style.css" rel="stylesheet">
  <script type="text/javascript" src="//vk.com/js/api/openapi.js?130"></script>
<script type="text/javascript">
VK.init({apiId: 5673308});
</script>
 </head>
 <body>
<?php 
include_once 'pageb/header.html'; 

 ?>
<div class="topw"></div>
<div class="dis1">
<div class="vk-wall">
 
 <!-- VK Widget -->
<div id="vk_groups"></div>
<script type="text/javascript">
VK.Widgets.Group("vk_groups", {mode: 4, width: "600px", height: "700px",wide: "2", color1: "e8ddc5", color2: "6c3f00", color3:"825112"}, 74540255);
</script></div>
<div class="dis2">
<?php $height= 530;
echo '<div class="hello"><div class="helloin"><br>Привет, '.$nick.'!<br>Похоже, DL тебе доверяет.<br>Хорошо, что ты с нами!</div></div>';
?>
<iframe src="https://discordapp.com/widget?id=229501860195729408&theme=dark" width="100%" height="<?php echo  $height;?>" allowtransparency="true" frameborder="0"></iframe></div></div>


<?php    require_once 'pageb/footer.html'; 
   
?>
 </body>
</html>