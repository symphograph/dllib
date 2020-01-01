<?PHP
	if (!function_exists('mb_ucfirst') && extension_loaded('mbstring'))
{
    function mb_ucfirst($mem, $encoding='UTF-8')
    {
        $mem = mb_ereg_replace('^[\ ]+', '', $mem);
        $mem = mb_strtoupper(mb_substr($mem, 0, 1, $encoding), $encoding).
               mb_substr($mem, 1, mb_strlen($mem), $encoding);
        return $mem;
    }
}
?>