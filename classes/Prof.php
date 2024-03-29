<?php


class Prof
{
    public int    $id        = 25;
    public string $name      = 'Прочее';
    public int    $lvl       = 0;
    public int    $save_or   = 0;
    public int    $save_time = 0;
    public int    $min       = 0;
    public int    $max       = 10000;

    public function InitForUser(int $prof_id) : bool
    {
        global $User;
        $qwe = qwe("
        SELECT * FROM profs 
        LEFT JOIN user_profs up on profs.prof_id = up.prof_id
        AND profs.prof_id = '$prof_id'
        AND up.user_id = '$User->id'
        INNER JOIN prof_lvls pl on up.lvl = pl.lvl
        ");
        if(!$qwe or !$qwe->rowCount())
            return false;

        $q= $qwe->fetchObject();
        $this->id = $prof_id;
        $this->name = $q->profession;
        $this->lvl = $q->lvl;
        $this->save_or = $q->save_or;
        $this->save_time = $q->save_time;
        $this->min = $q->min;
        $this->max = $q->max;
        return true;
    }

    public function savedLabor(int $labor) : int
    {
        //`pass_labor` * (100 - IFNULL(`save_or`,0)) / 100,0)
        $labor = $labor * (100 - $this->save_or)/100;
        return round($labor);
    }
}