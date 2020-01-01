
<?php
	function deepfound($query,$lvl, $errarr)
	{	global $errcnt, $errarr;	
		$mats =array();
			$cnt = mysqli_num_rows($query);
			//echo '<p>Вижу '.$cnt.' компонентов</p>';
	   foreach($query as $v)
		{$mat_id = $v['item_id'];
		 $mat_name = $v['item_name'];
		 $cr_name = $v['result_item_name'];
		 $mats[]= $mat_id;
		// echo '<p>Для '.$cr_name.' надо:</p>';
		//echo '<p>'.$lvl.' '.$mat_name.'</p>';
		 qwe("REPLACE INTO craft_lvls (lvl, item_id, item_name) 
		 VALUES ('$lvl', '$mat_id', '$mat_name')");
		}
		if(count($mats)>0)
		{
			//echo '<hr>'; 
			$lvl++;
			if($lvl>30) 
			{ 
			 
			 
			     $er_row = $mat_id.' '.$cr_name;
				 if(!in_array($er_row,$errarr)) 
					{ $errarr[] = $er_row;
					 $errcnt++;
			          echo '<p>Превышен лимит итераций для '.$er_row.'</p>';
					}
			   return;
			};
		$imp = implode(', ',$mats);
		$query = qwe("SELECT DISTINCT `item_id`, `item_name`, `result_item_name` FROM `craft_materials` where `result_item_id` in ($imp)
		and `item_id` in (SELECT `result_item_id` FROM `crafts` where `on_off` >0)");	
		deepfound($query, $lvl, $errarr);	
		}
	}
	
?>		
