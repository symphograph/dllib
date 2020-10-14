<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/filefuncts.php';
$identy = OnlyText($_COOKIE['identy']);
if(iconv_strlen($identy) != 12)
exit('Missed Cookies');
///jhjgfhjhgfhjhgfhjhfhjhgfghjhgf
class Utils {
    public static function redirect($uri = '') {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".$uri, TRUE, 302);
        exit;
    }
}

class PathHelper {
	// путь по умолчанию
	const DEFAULT_PATH = '../catalog.php';

	// массив разрешенных значений, чтобы избежать неожиданных значений в куки
	const PATH_WHITELIST = array(
    	'packtable' => '../packtable.php',
		'catalog' => '../catalog.php',
		'user_customs' => '../user_customs.php',
		'users' => '../users.php',
		'user_prices' => '../user_prices.php'
    );

	// название параметра в URL
    const COOKIE_NAME = 'path';
    // название куки
    const QUERY_NAME = 'path';

	public static function savePath() {
		// если параметр не передан, либо пуст
		if (!isset($_GET[self::QUERY_NAME]) || empty($_GET[self::QUERY_NAME])) {
			return;
		}
    	$path = $_GET[self::QUERY_NAME];

	    // если переданный path находится в белом списке - сохранить в куки
	    if (in_array($path, array_keys(self::PATH_WHITELIST))) {
	    	setcookie(self::COOKIE_NAME, $path, time() + 3600 * 24 * 7, "/");
	    }
    }

    public static function restorePath() 
	{
    	// если параметр не сохранен, либо пуст, вернем URL по умолчанию
		if (!isset($_COOKIE[self::COOKIE_NAME]) || empty($_COOKIE[self::COOKIE_NAME])) {
			return self::DEFAULT_PATH;
		}

    	$path = $_COOKIE[self::COOKIE_NAME];
    	// если сохраненный параметр найден и содержится в списке разрешенных параметров..
    	if (in_array($path, array_keys(self::PATH_WHITELIST))) {
    		// .. вернем сопоставленный с ним адрес
			return self::PATH_WHITELIST[$path];	    	
	    } else {
	    	// иначе - адрес по умолчанию
	    	return self::DEFAULT_PATH;
	    }
			}
}

$domen = $_SERVER['SERVER_NAME'];
$uri_calback = 'https://'.$domen.'/oauth/mailru.php';

$secrets['dllib.ru'] = [
			'app_id'=>000000,
			'app_private' => 'xxxxxxxxxxxxxxxxxx',
			'app_secret' => 'xxxxxxxxxxxxxxxxxxxxx',
			'uri_calback' => $uri_calback
		   ];
$secrets['test.dllib.ru'] = [
			'app_id'=>000000,
			'app_private' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx',
			'app_secret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx',
			'uri_calback' => $uri_calback
		   ];
$secret = $secrets[$domen];
class OAuthMailRu {

    const APP_ID = 000000; //ID приложения
    const APP_PRIVATE = 'xxxxxxxxxxxxxxx'; //Приватный ключ
    const APP_SECRET = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx'; //Защищенный ключ
    const URL_CALLBACK = 'https://dllib.ru/oauth/mailru.php'; //URL, на который произойдет перенаправление после авторизации
    const URL_AUTHORIZE = 'https://connect.mail.ru/oauth/authorize';
    const URL_GET_TOKEN = 'https://connect.mail.ru/oauth/token';
    const URL_API = 'https://www.appsmail.ru/platform/api';

    private static $token;
    public static $userId;
    public static $userData;

    public static function goToAuth($secret)
    {
    	$url = self::URL_AUTHORIZE .
            '?client_id=' . $secret['app_id'] .
            '&response_type=code' .
            '&redirect_uri=' . urlencode($secret['uri_calback']);
/*
        if (!empty($state)) {
        	$url .= '&state=' . urlencode($state);
        }
*/
        Utils::redirect($url);
    }

    public static function getToken($code,$secret) {
        $data = array(
            'client_id' => $secret['app_id'],
            'client_secret' => $secret['app_secret'],
            'grant_type' => 'authorization_code',
            'code' => trim($code),
            'redirect_uri' => $secret['uri_calback']
        );

        // формируем post-запрос
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' =>"Content-Type: application/x-www-form-urlencodedrn".
                    "Accept: */*rn",
                'content' => http_build_query($data)
            )
        );
        if (!($response = @file_get_contents(self::URL_GET_TOKEN, false, stream_context_create($opts)))) {
            return false;
        }

        $result = @json_decode($response);
        if (empty($result)) {
            return false;
        }

        self::$token = $result->access_token;
        self::$userId = $result->x_mailru_vid;
        return true;
    }

    public static function getUser($secret) {
        $request_params = array(
            'app_id' => $secret['app_id'],
            'method' => 'users.getInfo',
            'secure' => 1,
            'session_key' => self::$token,
            'uids' => self::$userId
        );

        $params = '';
        foreach ($request_params as $key => $value) {
            $params .= "$key=$value";
        }

        $url = self::URL_API .
            '?' . http_build_query($request_params).
            '&sig=' . md5($params . $secret['app_secret']);

        if (!($user = @file_get_contents($url))) {
            return false;
        }

        $user = json_decode($user);
        return self::$userData = $user;
    }
}

// Пример использования класса:
if (!empty($_GET['error'])) {
    // Пришёл ответ с ошибкой. Например, юзер отменил авторизацию.
    die($_GET['error']);
} elseif (empty($_GET['code'])) {
    PathHelper::savePath();
    // Самый первый запрос
    OAuthMailRu::goToAuth($secret);
} else 
{
    // Пришёл ответ без ошибок после запроса авторизации
    if (!OAuthMailRu::getToken($_GET['code'],$secret)) {
        die('Error - no token by code');
    }
    /*
     * На данном этапе можно проверить зарегистрирован ли у вас MailRu-юзер с id = OAuthMailRu::$userId
     * Если да, то можно просто авторизовать его и не запрашивать его данные.
     */
    $user = OAuthMailRu::getUser($secret);
    //var_dump($user);
	//echo '<br><br>';
	foreach($user as $v)
	{
	//$uid = $v->uid;
	//echo '<br><br>'.$uid;
	$fname = $v ->first_name;
	$lname = $v ->last_name;
	$email = $v ->email;
	$ava = $v ->pic_50;
	$mailnick = $v ->nick;
	}
	
	$unix_time = time();
	$datetime = date('Y-m-d H:i:s',$unix_time);
	$cooktime = $unix_time+60*60*24*365*5;
	
	$checkmail="SELECT `mail_id`, `email`, `identy` from `mailusers` where `email`='$email'";
	$query= qwe($checkmail);
	if(mysqli_num_rows($query)>0)//Это значит, что у него уже есть identy
	{
		foreach($query as $qid)
		{
			$native_identy = $qid['identy'];
		}
		$qoldidenty = qwe("SELECT * FROM `mailusers` WHERE BINARY `identy` = '$identy'");
		foreach($qoldidenty as $key)
		{
			$uid = $key['mail_id'];
		}
		//Очищаем нунужный mail_id, который дали ему при установке куки.
		qwe("DELETE FROM `mailusers` WHERE `mail_id` = '$uid'");
		

		$upd_sql="UPDATE `mailusers` SET 
		`first_name` = '$fname', 
		`last_name` = '$lname', 
		`avatar` = '$ava', 
		`mailnick` = '$mailnick', 
		`last_time` = '$datetime', 
		`last_ip` = '$ip'
		WHERE `email` = '$email'";
		qwe($upd_sql);
		
		//Возвращаем родную куку. (в плане безопасности надо еще подумать)
		setcookie('identy',$native_identy,$cooktime,'/','',true,true);
		
	}else 
	{
		
		//Такого мыла нет. Дописываем мыло временному юзеру и делаем его постоянным.
		$upd_sql="UPDATE `mailusers` SET 
		`first_name` = '$fname', 
		`last_name` = '$lname', 
		`avatar` = '$ava', 
		`mailnick` = '$mailnick', 
		`last_time` = '$datetime', 
		`last_ip` = '$ip',
		`email` = '$email'
		WHERE BINARY  `identy` = '$identy'";
		qwe($upd_sql);
	}
	
	AvaGetAndPut($ava,$identy);
	//qwe("UPDATE `identy` SET `mail_id` = '$uid' WHERE BINARY `identy` = '$identy'");
	//setcookie('fname', $fname, time()+3600*24*7, "/");
	//setcookie('mailid', $uid, time()+3600*24*7, "/");
	//setcookie('avatar', $ava, time()+3600*24*7, "/");
	
}

//Заодно почистим бд от мусора.
//dbCleaner();

$url = PathHelper::restorePath();
//exit();
echo '<meta http-equiv="refresh" content="0; url='.$url.'">';
//exit();