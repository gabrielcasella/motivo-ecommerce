<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_link extends Model
{
    //  List the attributes that can be filled using Laravel's Eloquent ORM
    protected $fillable = ['product_id', 'linked_product_id'];
}
