<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    //
    protected  $table='order_detail';

    public $timestamps='false';

    public function log(){
        return $this->hasMany('App\Model\OrderDetailLog','order_detail_id');
    }
}
