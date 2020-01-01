<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Документ без названия</title>
</head>

<body>
<?php
$lvlranges = array(0, 0, 5, 10, 15, 20, 20, 20, 20, 25, 30, 40);
$or_need = ceil(50*(100-$lvlranges[2])/100);
echo $or_need;
?>
</body>
</html>