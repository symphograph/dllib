<?php
//Функции для работы с файлами и папками
function FolderList($dir)
{
	//Получает массив с именами папок в директории
	$files = scandir($dir);
	$skip = ['.', '..'];
	$folders = [];
	foreach($files as $file)
	{
		if(!in_array($file, $skip) and is_dir($dir.'/'.$file))
		   $folders[] = $file;
	}
	return($folders);
}

function FileList($dir)
{
	$files = scandir($dir);
	$skip = ['.', '..'];
	$files2 = [];
	foreach($files as $file)
	{
		if(in_array($file, $skip) or is_dir($dir.'/'.$file))
		{}else
		$files2[] = $file;
		  // $folders[] = $file;
	}
	return $files2;
}

function delFolderRecurs($path) 
{
	//Удаляет файл или папку со всем содержимым
  if (is_file($path)) return unlink($path);
  if (is_dir($path)) {
    foreach(scandir($path) as $p) if (($p!='.') && ($p!='..'))
      delFolderRecurs($path.DIRECTORY_SEPARATOR.$p);
    return rmdir($path); 
    }
  return false;
}

function file_force_contents($dir, $contents)
{
	//Сохраняет файл. Если нет дириктории, создаёт её.
	$parts = explode('/', $dir);
	//printr($parts);
	$file = array_pop($parts);
	$dir = '';
	$i=0;
	foreach($parts as $part)
	{$i++;
		if($i==1)
			$dir = $part;
	 	else
			$dir .= "/$part";
		if(!is_dir($dir)) mkdir($dir, 0700, true);
		//echo $dir.'<br>';
		
	}
	file_put_contents("$dir/$file", $contents);
}

function is_image($filename) {
	$img_types = ['','gif','jpeg','png','swf','psd','bmp','tiff','tiff'];
  $is = @getimagesize($filename);
	//var_dump(filesize($filename));
	
  if ( !$is ) 
	  return false;
  if( !in_array($is[2], array(1,2,3)) ) 
	  return false;
	
  return $img_types[$is[2]];
}
?>