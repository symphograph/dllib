<?php

namespace transfer;

use api\Api;
use PDO;

class Price
{
    public int     $itemId      = 0;
    public int     $price       = 0;
    public int     $serverGroup = 0;
    public string  $datetime    = '';


    /**
     * @return bool|array<self>
     */
    public static function getAllUserPrices(int $userId): bool|array
    {
        $qwe = qwe("
            select item_id as itemId,
                   auc_price as price,
                   server_group as serverGroup,
                   time as datetime
            from prices 
            where user_id = :user_id",
            ['user_id'=>$userId]
        );
        if(!$qwe || !$qwe->rowCount()){
            return false;
        }
        return $qwe->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function sendList(int $userId): bool
    {

        $List = self::getAllUserPrices($userId);
        if(empty($List)){
            return false;
        }
        global $cfg;
        $responce = Api::curl(
            'https://' . $cfg->transfers->$_SERVER['SERVER_NAME'] . '/api/hooks/prices.php',
            ['userId'=>$userId, 'prices' => $List]
        );

        if (($responce ?? '') === 'ok'){
            return true;
        }
        return false;
    }


}