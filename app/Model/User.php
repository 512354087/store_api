<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'user';

    public $timestamps = false;


    protected $hidden = ['password'];

    
}
