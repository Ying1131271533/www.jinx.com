<?php

namespace app\common\model;

use think\model;

class User extends Model
{
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
