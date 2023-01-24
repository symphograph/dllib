<?php

namespace Transfer;

use Api\Api;
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
    public static function getAllUserPrices(int $userId, string $lastDatetime): bool|array
    {
        $qwe = qwe("
            select item_id as itemId,
                   auc_price as price,
                   server_group as serverGroup,
                   time as datetime
            from prices 
            where user_id = :user_id
            and time >= :lastDatetime",
            ['user_id'=>$userId, 'lastDatetime'=> $lastDatetime]
        );
        if(!$qwe || !$qwe->rowCount()){
            return false;
        }
        return $qwe->fetchAll(PDO::FETCH_CLASS, self::class);
    }

}