<?php

namespace api;

class Api
{
    const Monthes = ['', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
    public static function errorMsg(string $msg='Неизвестная ошибка') : string|bool
    {
        return json_encode(['error'=>$msg],JSON_UNESCAPED_UNICODE);
    }

    public static function resultMsg(string|array $msg = 'Готово'): bool|string
    {
        //global $User;
        //LogAction::putToDb($User->id, $msg);
        return json_encode(['result'=>$msg],JSON_UNESCAPED_UNICODE);
    }

    public static function resultData(array|object $data, string|array $msg = 'Готово'): bool|string
    {
        //global $User;
        //LogAction::putToDb($User->id, $msg);
        return json_encode(['result'=>$msg,'data' => $data],JSON_UNESCAPED_UNICODE);
    }

    public static function monthNumByName(string $month): bool|int|string
    {
        return array_search($month, self::Monthes);
    }

    public static function emptyArr() : array
    {
        return [];
    }

    public static function curl(string $plink, array $post): bool|string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // allow redirects
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); // times out after 4s
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // return into a variable
        curl_setopt($curl, CURLOPT_URL, $plink);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5 GTB6");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $somePage = curl_exec($curl);
        //print_r($somepage);
        curl_close($curl);
        return $somePage;
    }

}