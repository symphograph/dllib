<?php
 function esyprice($total){
	 $gold = '<img src="img/gold.png" width="15" height="15" alt="gold"/>';
 $silver = '<img src="img/silver.png" width="15" height="15" alt="silver"/>';
 $bronze = '<img src="img/bronze.png" width="15" height="15" alt="bronze"/>';
$gol= strrev(substr(strrev($total),4,10));
	if($gol == 0) $gold = '';
	$sil = strrev(substr(strrev($total),2,2));
	if($sil == 0 and $gold == '') {$silver = ''; $rsil = '';};
	$bro = strrev(substr(strrev($total),0,2));
	echo $gol.$gold.$sil.$silver.$bro.$bronze;
}
?>