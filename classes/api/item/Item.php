<?php

namespace api\item;

use PDO;

class Item
{
    public int|null $id;
    public string|null $name;
    public bool $craftable = false;
    public bool $personal = false;
    public string|null $icon;
    public int|null $grade;
    public int|null $categ_id;
    public Info|null $Info;

    public function __set(string $name, $value): void{}

    /**
     * @return bool|array<self>
     */
    public static function searchList(array $ItemIds = []): bool|array
    {

        if(empty($ItemIds)){
            $qwe = qwe("select *, 
            item_id as id, 
            item_name as name, 
            if(basic_grade,basic_grade,1) as grade 
            from items where on_off
            order by name, craftable desc, personal, grade"
            );
        }else {
            if(!\MyHelpers::isArrayIntList($ItemIds)){
                return false;
            }

            $ItemIds = '('.implode(',',$ItemIds).')';
            $qwe = qwe("select *, 
            item_id as id, 
            item_name as name, 
            if(basic_grade,basic_grade,1) as grade 
            from items where on_off
                       and item_id in $ItemIds
            order by name, craftable desc, personal, grade"
            );
        }
        if(!$qwe || !$qwe->rowCount()) {
            return false;
        }

        return $qwe->fetchAll(PDO::FETCH_CLASS, get_class());
    }

    public static function byId(int $id) : self|bool
    {
        $qwe = qwe("select *, 
            item_id as id, 
            item_name as name, 
            if(basic_grade,basic_grade,1) as grade 
            from items where on_off
                       and item_id = :id",
        ['id' => $id]
        );
        if(!$qwe || !$qwe->rowCount()) {
            return false;
        }
        return $qwe->fetchObject(get_class());
    }

    public function initInfo(): void
    {
        $this->Info = Info::byId($this->id);
    }

}