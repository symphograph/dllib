<?php

namespace DTO;

use DTO\DTO;
use PDO;

class PriceDTO extends DTO
{
    public int $user_id;
    public int $item_id;
    public int $auc_price;
    public int $server_group;
    public string $time;

    public static function byId(int $user_id, int $item_id)
    {

    }

    /**
     * @return self[]
     */
    public static function getListOfUser(int $user_id): array
    {
        $qwe = qwe("select * from prices where user_id = :user_id", ['user_id' => $user_id]);
        return $qwe->fetchAll(PDO::FETCH_CLASS, self::class) ?? [];
    }

}