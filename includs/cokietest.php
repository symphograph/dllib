<?php
  // это небольшой проверочный скрипт, выясняющий,
  // включены ли cookies у пользователя  

  if(empty($_GET["cookie"]) and empty($_COOKIE["test"]))
  {
    // посылаем заголовок переадресации на страницу,
    // с которой будет предпринята попытка установить cookie 
	setcookie("test","1"); 
    header("Location: $_SERVER[PHP_SELF]?cookie=1");
    // устанавливаем cookie с именем "test"
	  exit();
  }
  else
  {
    if(empty($_COOKIE["test"]) and (!empty($_GET["cookie"])))
    {
      exit('<meta charset="utf-8">Для корректной работы приложения необходимо включить cookies');
    }
  if(!empty($_GET["cookie"]))
	  {
	  	$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
	  	$uri = 'https://' . $_SERVER['HTTP_HOST'] . $uri_parts[0];
	  	header("Location: $uri");
	  //var_dump($uri);
	  exit();
	 // exit('<meta http-equiv="refresh" content="2; '.$uri.'">');
	  }
		  
  }
?>