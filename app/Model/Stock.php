<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'product_stock';

    public $timestamps = false;

    public function product()
    {
      $this->belongsToMany('App\Model\Product','product_id');
    }

}
