<?php

namespace app\common\model;

use think\model;

class ArticleDesc extends Model
{
    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
