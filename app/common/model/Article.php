<?php

namespace app\common\model;

use think\model;

class Article extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function desc()
    {
        return $this->hasOne(ArticleDesc::class);
    }
}
