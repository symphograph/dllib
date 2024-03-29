<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/filefuncts.php';
$User = new User();
if(!$User->check()) {
    die('Error Authentication');
}

$secret = (object) $cfg->mailru_secrets[$_SERVER['SERVER_NAME']];
class Utils {
    public static function redirect($uri = '') {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".$uri, TRUE, 302);
        exit;
    }
}

class PathHelper {
	// путь по умолчанию
	const DEFAULT_PATH = '../packtable.php';

	// массив разрешенных значений, чтобы избежать неожиданных значений в куки
	const PATH_WHITELIST = array(
    	'packtable' => '../packtable.php',
		'catalog' => '../catalog.php',
		'user_customs' => '../user_customs.php',
		'users' => '../users.php',
		'user_prices' => '../user_prices.php',
        'routestime' => '../routestime.php',
        'packpost' => '../packpost.php'
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

class OAuthMailRu {
    const URL_AUTHORIZE = 'https://connect.mail.ru/oauth/authorize';
    const URL_GET_TOKEN = 'https://connect.mail.ru/oauth/token';
    const URL_API = 'https://www.appsmail.ru/platform/api';

    private static $token;
    public static $userId;
    public static $userData;

    public static function goToAuth($secret)
    {
        $uri_calback = 'https://'.$_SERVER['SERVER_NAME'].'/oauth/mailru.php';
    	$url = self::URL_AUTHORIZE .
            '?client_id=' . $secret->app_id .
            '&response_type=code' .
            '&redirect_uri=' . urlencode($uri_calback);

        Utils::redirect($url);
    }

    public static function getToken($code,$secret) {
        $uri_calback = 'https://'.$_SERVER['SERVER_NAME'].'/oauth/mailru.php';
        $data = array(
            'client_id' => $secret->app_id,
            'client_secret' => $secret->app_secret,
            'grant_type' => 'authorization_code',
            'code' => trim($code),
            'redirect_uri' => $uri_calback
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
            'app_id' => $secret->app_id,
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
            '&sig=' . md5($params . $secret->app_secret);

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

	foreach($user as $v)
	{
        //$uid = $v->uid;
        //echo '<br><br>'.$uid;
        $User->fname     = $v->first_name;
        $User->last_name = $v->last_name;
        $User->email     = $v->email;
        $User->avatar    = $v->pic_50;
        $User->mailnick  = $v->nick;
	}

    if(!$User->authByEmail())
        die('Myauth Error');
}

$url = PathHelper::restorePath();
header("Location: ".$url);
exit();