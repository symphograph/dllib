<?PHP
	if (!function_exists('mb_ucfirst') && extension_loaded('mbstring'))
{
    function mb_ucfirst($nick, $encoding='UTF-8')
    {
        $nick = mb_ereg_replace('^[\ ]+', '', $nick);
        $nick = mb_strtoupper(mb_substr($nick, 0, 1, $encoding), $encoding).
               mb_substr($nick, 1, mb_strlen($nick), $encoding);
        return $nick;
    }
}
?>