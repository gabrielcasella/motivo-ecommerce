<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_metadata extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_metadata';

    /**
     * LIst of attribute types with their correspondent model.
     *
     * @var string
     */
    private static $models = [
        'text'      => 'App\Attribute_text',
        'decimal'   => 'App\Attribute_decimal',
        'int'       => 'App\Attribute_int',
        'string'    => 'App\Attribute_varchar',
        'date_time' => 'App\Attribute_datetime',
    ];

    /**
     * Get the attribute name associated with the metadata.
     */
    public function attribute()
    {
        return $this->hasOne('App\Attribute', 'id', 'attribute_id');
    }

    /**
     * Fetch the attribute data_type and return the model that should be used to store it.
     *
     * @param $attribute_id
     * @return mixed
     */
    public static function getDataTypeModel($attribute_id)
    {
        $metadata = Product_metadata::where('attribute_id', $attribute_id)->first();
        return Product_metadata::$models[$metadata->data_type];
    }

    /**
     * Return a list of attributes data_types with their correspondent model.
     *
     * @return string
     */
    public static function getModels()
    {
        return Product_metadata::$models;
    }
}
