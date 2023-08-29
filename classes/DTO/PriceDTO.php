<?php

namespace DTO;

use DTO\DTO;
use PDO;

class PriceDTO extends DTO
{
    public int $userId;
    public int $itemId;
    public int $price;
    public int $serverGroup;
    public string $updatedAt;

    public static function byId(int $user_id, int $item_id)
    {

    }

    /**
     * @return self[]
     */
    public static function getListOfUser(int $user_id): array
    {
        $qwe = qwe("
            select 
                user_id as userId, 
                item_id as itemId, 
                auc_price as price, 
                server_group as serverGroup, 
                `time` as updatedAt 
            from prices 
            where user_id = :user_id",
            ['user_id' => $user_id]
        );
        return $qwe->fetchAll(PDO::FETCH_CLASS, self::class) ?? [];
    }

}