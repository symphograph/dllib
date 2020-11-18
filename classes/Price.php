<?php


abstract class Price
{

    public int $price;
    public int $time;
    public int $puser_id;
    public int $type;

    public function __construct()
    {
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getType(): int
    {
        return $this->type;
    }
}