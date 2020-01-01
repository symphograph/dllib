<footer id="footer">
	<span>&copy; <a  href="https://vk.com/roman_chubich" target="_blank">Граф</a> * Dead Legion * Шаеда</span>
<?php
$email = $email ?? '';
if($email == '')
{   
?>
	<div id="xlgames">
	Гильдия Dead Legion выражает большую признательность компании <a  href="https://xlgames.com" target="_blank">XL-GAMES</a> за шедевральную игру <a  href="https://aa.mail.ru" target="_blank">Archeage</a> и компании <a  href="https://mail.ru" style="color: white" target="_blank">mail.ru</a> за отличную локализацию.
	</div>
<?php
}
?> 
</footer>
<script type='text/javascript'>
$( document ).ready(function() {
  
	setTimeout(function() {$("#xlgames").hide('slow');}, 5000);	
	setTimeout(function() {$("#underhead").hide('slow');}, 5000);
});	   
</script>