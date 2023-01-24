<?php

namespace api\craft;

use api\item\Item;

class Mat extends Item
{
    public int|null $resiltId;
    public int|null $grade;
    public int|float|null $need;

    public static function byIds(int $itemId, int $craftId) : self|bool
    {

    }
}