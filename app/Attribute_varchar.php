<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute_varchar extends Model
{
    // List the attributes that can be filled using Laravel's Eloquent ORM
    protected $fillable = ['attribute_id', 'product_id', 'value'];

    /**
     * Get the attribute associated with this string model.
     *
     * TODO Methods to manage the attributes should be provided as this is an EAV model
     */
    public function attribute()
    {
        return $this->belongsTo('App\Attribute', 'attribute_id', 'id');
    }
}
