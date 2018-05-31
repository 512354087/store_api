<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table = 'order';

    public $timestamps = false;

    protected  $guarded=[];

     public function orderdetail(){
         return $this->hasMany('App\Model\OrderDetail','order_no');
     }
}
