<?php

class Side
{
    const Sides = [
        1=>'Запад',
        2=>'Восток',
        3=>'Север',
        9=>'Остров свободы'
    ];
    public string $sideName = 'Материк';

    public function __construct(int $side)
    {
        if(!in_array($side,self::Sides)){
            return false;
        }
        $this->sideName = self::Sides[$side];
    }

}