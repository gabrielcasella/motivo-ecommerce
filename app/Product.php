<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /*
     *  List the attributes that can be filled using Laravel's Eloquent ORM.
     *  The product structure is simple because most of the data will be stored in the attributes tables.
     */

    protected $fillable = ['title', 'sku', 'type'];
}
