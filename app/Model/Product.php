<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $table = 'product';

    public $timestamps = false;

    /**
     * 获取商品库存
     */
    public  function stock()
    {
        return $this->hasMany('App\Model\Stock','product_id')
            ->select('product_stock.*','b.name as color_name','product_attributes.name as size_name')
            ->leftJoin('product_attributes','product_attributes.id','=','product_stock.size_id')
            ->leftJoin('product_attributes as b','b.id','=','product_stock.color_id');
    }

    public function discount(){
        return $this->hasOne('App\Model\Product_discount','product_id');
    }
}
