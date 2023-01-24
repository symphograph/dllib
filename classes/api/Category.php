<?php

namespace api;

use PDO;

class Category
{
    public int|null $id;
    public int|null $index;
    public string|null $name;
    public string|null $description;
    public bool $selectable = true;

    /**
     * @var array<self>|null
     */
    public array|null  $children;
    public int|null    $parent_id;
    public string|null $icon;


    public function __set(string $name, $value): void{}

    public static function byId(int $id) : self|bool
    {
        $qwe = qwe("select * from item_categories where id = :id",['id'=>$id]);
        if(!$qwe || !$qwe->rowCount()){
            return false;
        }
        return $qwe->fetchObject(get_class());
    }

    /**
     * @return array<self>
     */
    public static function getTree(): array
    {
        $categs = self::getAllCategs();
        $index = 0;
        $arr = [];
        foreach ($categs as $categ){
            $index++;
            $categ->index = $index;
            $arr[] = $categ;
        }
        $categs = $arr;

        $groups = self::getGroups();
        $roots = self::getRoots();
        $arr = [];
        foreach ($groups as $group){
            $index++;
            $group->initChilds($categs);
            $group->index = $index;
            $group->selectable = false;
            $arr[] = $group;
        }
        $groups = $arr;

        $arr = [];
        foreach ($roots as $root){
            $index++;
            $root->initChilds($groups);
            $root->icon = 'img:https://' . $_SERVER['SERVER_NAME'] . '/img/icons/50/' . $root->icon;
            $root->index = $index;
            $root->selectable = false;
            $arr[] = $root;
        }
        return $arr;
    }

    /**
     * @return bool|array<self>
     */
    private static function getAllCategs(): bool|array
    {
        $qwe = qwe("select *, item_group as parent_id from item_categories");
        if(!$qwe || !$qwe->rowCount()){
            return false;
        }
        return $qwe->fetchAll(PDO::FETCH_CLASS,get_class());
    }

    /**
     * @return bool|array<self>
     */
    private static function getGroups(): bool|array{
        $qwe = qwe("select *, sgr_id as parent_id from item_groups where visible_ui > 0");
        if(!$qwe || !$qwe->rowCount()){
            return false;
        }
        return $qwe->fetchAll(PDO::FETCH_CLASS,get_class());
    }

    /**
     * @return bool|array<self>
     */
    private static function getRoots(): bool|array
    {
        $qwe = qwe("select *, sgr_id as id, sgr_name as name from item_subgroups where visible_ui > 0");
        if(!$qwe || !$qwe->rowCount()){
            return false;
        }
        return $qwe->fetchAll(PDO::FETCH_CLASS,get_class());
    }

    /**
     * @param array<self> $allChilds
     */
    private function initChilds(array $allChilds): void
    {
        $childs = [];
        foreach ($allChilds as $child){
            if($child->parent_id === $this->id){
                $childs[] = $child;
            }
        }
        $this->children = $childs;
    }
}