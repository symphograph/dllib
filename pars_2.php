<meta charset="utf-8">
<?php
include 'includs/ip.php';
if(!$myip) exit();
echo '<form method="post" action="">
<input type="submit" name="go" value="go">	
</form>';
if(!isset($_POST['go'])) exit();
include 'includs/config.php';
function curl_npc($plink)
{	
	$curl = curl_init();
	 curl_setopt($curl, CURLOPT_HEADER, 0);
	 curl_setopt($curl, CURLOPT_FAILONERROR, 1);
	 curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // allow redirects
	 curl_setopt($curl, CURLOPT_TIMEOUT, 10); // times out after 4s
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // return into a variable 
	 curl_setopt($curl, CURLOPT_URL, $plink);
	 curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5 GTB6");
	 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	 curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	$somepage = curl_exec($curl);
	curl_close($curl);
	return $somepage;
}
$id = 23807; $n = 0; $drgr = 0;
$plink = 'http://archeagedatabase.net/ru/item/'.$id;

$somepage = curl_npc($plink);
preg_match_all('#<table class="itemwhite_table"><tr><td colspan="2">(.+?)Цена продажи#is', $somepage, $arr);
//print_r($arr);
	$table = $arr[0][0];
echo $table;
$sintez = 0;
if(preg_match('/Можно использовать как вспомогательный материал/',$table))
$sintez = 1;

if(!preg_match('/Падает с NPC/',$somepage)) continue;
?>