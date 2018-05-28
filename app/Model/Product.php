<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';

    public $timestamps = false;

    /**
     * 获取博客文章的评论
     */
    public function stock()
    {
        return $this->hasMany('App\Model\Stock','product_id')->whereRaw('color_id = ? and size_id = ? and num >= ?',[$request->input('color_id'),$request->input('size_id'),$request->input('product_num')]);
    }
}
