<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';

$ver = random_str(8);
//$ver = 'nbc6754';
?>


<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name = "description" content = "Таблица цен на паки в Archeage"/>
  <meta name = "keywords" content = "товары фактории ,паки, archeage, архейдж, региональные товары, таблица паков, сколько стоят паки, цена паков" />
<title>Популярные вопросы</title>
<link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">
<link href="css/items.css?ver=<?php echo md5_file('css/items.css')?>" rel="stylesheet">
<link href="css/faq.css?ver=<?php echo md5_file('css/faq.css')?>" rel="stylesheet">
</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php'?>

<main>
<div id="rent">
	<div class="navcustoms">
	<h1>Популярные вопросы</h1>
	</div>
	
	<div id="rent_in" class="rent_in">
		<div class="all_info" id="all_info">
			<div id="qarea">
				<div class="comments">
					<div class="comments_row">
						<div class="qa">
						<b>Прибыль с Паков превышает выручку. Всё сломалось?</b>
							<div class="comment">
								Скорее всего в цепочке крафта присутствуют отходы производства. Например, шкура, навоз, солома итд.
								В совокупности с дешевыми ОР это может дать отрицательную себестоимость некоторых ингридиентов.


							</div>
						</div>
					</div><br>
					<div class="comments_row">
						<div class="qa">
						<b>Как считается стоимость предметов за Ремесленную репутацию, Честь, Дельфийские звёзды итд.?</b>
							<div class="comment">
								Если указана цена самого предмета, то используется она. Если нет, то исходя из указанной цены валюты.


							</div>
						</div>
					</div><br>
					<div class="comments_row">
						<div class="qa">
						<b>В игре десятки тысяч предметов. Не слишком ли наивно полагать, что можно добиться актуальной ценовой картины?</b>
							<div class="comment">
								Цены на все предметы не нужны. Калькулятор ориентируется на себестоимость, полученную при расчете рецептов. 
								Критичны только цены на исходное сырьё. Аукционная цена крафтабельных предметов используется только если Вы отмечаете предмет, как покупаемый, или хотите видеть прибыль с продажи.
							</div>
						</div>
					</div><br>
					<div class="comments_row">
						<div class="qa">
						<b>В каком браузере сайт отображается наиболее корректно?</b>
							<div class="comment">
								Google Chrome.
							</div>
						</div>
					</div><br>
					<div class="comments_row">
						<div class="qa">
						<b>Я не отображаюсь в списке сообщества. Друзья не могут добавить мои цены в "список доверия".</b>
							<div class="comment">
								В списке отображаются пользователи, выполнившие вход в аккаунт и сделавшие записи о ценах.
								Цены на "интимные" предметы (РР, ОР, Честь итд) не влияют на попадание в список.
							</div>
						</div>
					</div><br>
					<div class="comments_row">
						<div class="qa">
						<b>Вцелом, я доверяю ценам своего друга, но у нас разные взгляды на стоимость Очков работы и Чести.</b>
							<div class="comment">
								Настройки доверия не влияют на эту группу цен. Даже если запись об ОР у вашего друга новее, калькулятор предпочтёт Вашу.
							</div>
						</div>
					</div><br>
					<div class="comments_row">
						<div class="qa">
						<b>Крафт обошелся в 19 голд. Я продал за 20. Почему я на 1 голду в минусе?</b>
							<div class="comment">
								10% забрал аукцион.
							</div>
						</div>
					</div><br>
					<div class="comments_row">
						<div class="qa">
						<b>Учтена ли стоимость отходов в расчете рецепта?</b>
							<div class="comment">
								Да.
							</div>
						</div>
					</div><br>
					<div class="comments_row">
						<div class="qa">
						<b>Некоторые отходы крафтабельны. Почему требуется их аукционная цена, ведь можно взять их себестоимость?</b>
							<div class="comment">
								Да.
							</div>
						</div>
					</div><br>
					<div class="comments_row">
						<div class="qa">
						<b>У меня завались ОР. Зачем их вообще считать?</b>
							<div class="comment">
								Есть разные мнения о стоимости ОР. На мой взгляд, ОР это не ресурс, а эквивалент единицы времени.
								Допустим, у Пети и Васи тьма свободного времени и они весь день сиськи мяли. Вася помял 3 сиськи, а Петя только одну.
								То есть, Васин день стоит дороже, а время, как выяснилось, можно измерять и в мятых сиськах, даже если его завались.
							</div>
						</div>
					</div><br>
				</div>
			</div>
		</div>
	</div>
	
</div>
</main>
<?php include_once 'pageb/footer.php';?>

</body>
</html>