<?php
	 $addmemb = $_POST['addmemb'];
	 $addmembs = explode(' ',$addmemb);
	$i=0;
     include("../../functions/mb_ucfirst2.php");
     
	 foreach($addmembs as $mems)
	{    
		 $mem = trim($mems).'_no_ts';
		 $mem = mb_ucfirst($mem);
		 $add[] = "'".$mem."'"; 
		$uni_test = qwe("SELECT * FROM `ev_membs_add` WHERE `sh_nick` = '$mem'");
		if(!mysqli_num_rows($uni_test)> 0)
	//записываем новеньких
	 qwe("INSERT INTO `ev_membs_add` (`sh_nick`, `time`) VALUES ('$mem', now())");
	};
	$adds = implode(', ',$add);
    //получаем присоенные id
	$mq = qwe("SELECT * FROM `ev_membs_add` WHERE `sh_nick` in ($adds)");
	foreach($mq as $akey)
	{
	   $addgids[] = $akey['id'];
	};
    //Дописываем в тс
$query = qwe("SELECT `id`, `sh_nick` FROM `ev_membs_add`");
foreach ($query as $v)
{
	$add_cldbid = $v['id'];
	$add_nick = $v['sh_nick'];
	//echo '<p>'.$add_cldbid.' '.$add_nick.'</p>';
	qwe("REPLACE INTO `ts_users` (`cldbid`, `sh_nick`, `group_id`)
	VALUES ('$add_cldbid', '$add_nick', '8')");
}
?>