<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Product;
Use App\Product_metadata;
use App\Product_link;
use App\Attribute;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*
         * Fetch all products
         * TODO: retrive only the product id
         */
        $products_result = Product::all();

        // Fill a products array based on the ids and return it
        $products = [];
        foreach($products_result as $prod) {
            $products[] = $this->show($prod->id);
        }
        return response()->json($products, 201);
    }

    /**
     * Store a newly created product in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'bail|required',
            'sku' => 'bail|required',
            'type' => 'required|in:simple,configurable'
        ]);

        /*
         * There can be a few required attributes for all products, so let's make
         * sure we have values for those
         */
        $required_attributes = Product_metadata::where('is_required', true)->get();
        $extra_validations = [];
        $attribute_types = [];

        foreach ($required_attributes as $required) {
            $attribute_name = $required->attribute->name;
            $extra_validations['attributes.' . $attribute_name] = 'bail|required';
            $attribute_types[$attribute_name] = $required->data_type;
        }

        // If we are missing required attributes invalidate the request
        $this->validate($request, $extra_validations);

        $product = Product::create($request->all());

        // The product has been saved, now let's save the attributes
        $this->storeAttributes($product->id, $request['attributes']);

        return $product;
    }

    /**
     * Store attributes for a newly created product in the database.
     *
     * @param $product_id
     * @param $attributes
     */
    private function storeAttributes($product_id, $attributes)
    {
        // Get the attribute list to be stored
        $attribute_list = $this->getAttributeList($attributes);

        /*
         * Now prepare the attributes to be added to the correct model
         */
        foreach($attribute_list as $att_name => $att_data) {
            $attribute_data = [
                'attribute_id' => $att_data['id'],
                'product_id' => $product_id,
                'value' => $att_data['value']
            ];
            $att_data['model']::create($attribute_data);
        }
    }

    /**
     * Display a product based on the product id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Validator::make(
            [
                'id' => $id
            ],
            [
                'id' => 'required|numeric'
            ])
            ->validate();

        $product = $this->getProduct($id);

        switch($product->type) {
            case 'simple':
                break;
            case 'configurable':
                $linked_products = Product_link::where('product_id', $id)->get();
                $products = [];
                foreach($linked_products as $linked) {
                    $products[] = $this->getProduct($linked->linked_product_id);
                }
                $product['linked_products'] = $products;
                break;
        }

        return $product;
    }

    /**
     * Fetch a product and it's attributes.
     *
     * @param $product_id
     * @return mixed
     */
    private function getProduct($product_id)
    {
        $product = Product::findOrFail($product_id);
        $product['attributes'] = $this->getAttributes($product_id);
        return $product;
    }

    /**
     * Fetch the attributes for a product.
     *
     * @param $product_id
     * @return array
     */
    private function getAttributes($product_id)
    {
        $models = Product_metadata::getModels();
        $attributes = [];
        foreach ($models as $model) {
            $model_response = $model::where('product_id', $product_id)->get();
            if(!empty($model_response)) {
                foreach($model_response as $attributes_model) {
                    $attributes[] = [
                        'name' => $attributes_model->attribute()->first()->name,
                        'value' => $attributes_model->value,
                    ];
                }
            }
        }
        return $attributes;
    }

    /**
     * Link a simple product with a configurable product.
     *
     * @param  int  $product_id
     * @param  int  $linked_product_id
     * @return \Illuminate\Http\Response
     */
    public function link($product_id, $linked_product_id)
    {
        Validator::make(
            [
                'product_id' => $product_id,
                'linked_product_id' => $linked_product_id,
            ],
            [
                'product_id' => 'required|numeric',
                'linked_product_id' => 'required|numeric',
            ])
            ->validate();

        // TODO Validate the types, configurable for product_id, simple for linked_product_id

        return Product_link::create([
            'product_id' => $product_id,
            'linked_product_id' => $linked_product_id,
        ]);
    }

    /**
     * Update the specified product in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Validator::make(
            [
                'id' => $id
            ],
            [
                'id' => 'required|numeric'
            ])
            ->validate();

        $product = Product::findOrFail($id);

        $prod_request = $request->all();
        $attributes = $request['attributes'];
        unset($prod_request['attributes']);

        // First let's update the product
        $product->update($prod_request);

        // Now let's update it's attributes
        // TODO Should we create or delete attributes in this step if needed?
        $this->updateAttributes($attributes, $id);

        // Return the product with it's attributes with the new values
        return $this->show($id);
    }

    /**
     * Uptate the attribues for a product.
     *
     * @param $attributes
     * @param $product_id
     */
    private function updateAttributes($attributes, $product_id)
    {
        // Get the attribute list to be updated
        $attribute_list = $this->getAttributeList($attributes);

        foreach($attribute_list as $att_name => $att_data) {
            $attribute_data = [
                'attribute_id' => $att_data['id'],
                'product_id' => $product_id,
                'value' => $att_data['value']
            ];

            $attribute =  $att_data['model']::where('product_id', $product_id)->where('attribute_id', $att_data['id'])->first();
            $attribute->update($attribute_data);
        }
    }

    /**
     * Retrieve a list of attribute data based on the attribute name. The most important part is to get the correct
     * model that should be used to store the attribute.
     *
     * @param $attributes
     * @return array
     */
    private function getAttributeList($attributes)
    {
        /*
         * As a EAV model was used, let's retrieve the appropriate data type for each attributes so the
         * correct table is used to store it using the Product_metadata model
         */

        $attribute_list = [];
        foreach ($attributes as $att_name => $att_value) {
            $attribute_id = Attribute::where('name', $att_name)->first()->id;
            $attribute_list[$att_name]['model'] = Product_metadata::getDataTypeModel($attribute_id);
            $attribute_list[$att_name]['value'] = $att_value;
            $attribute_list[$att_name]['id'] = $attribute_id;
        }
        return $attribute_list;
    }

    /**
     * Remove the specified product from the database and it's attributes.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Validator::make(
            [
                'id' => $id
            ],
            [
                'id' => 'required|numeric'
            ])
            ->validate();

        $product = Product::findOrFail($id);

        // Remove the attributes
        $this->destroyAttributes($id);

        // TODO Should a soft delete be used instead?
        $product->delete();

        return response()->json(null, 204);
    }

    /**
     * Remove the attributes for a given product.
     *
     * @param $product_id
     */
    private function destroyAttributes($product_id)
    {
        $models = Product_metadata::getModels();
        foreach ($models as $model) {
            $model_response = $model::where('product_id', $product_id)->get();
            if(!empty($model_response)) {
                foreach($model_response as $attributes_model) {
                    // TODO Should a soft delete be used instead?
                    $attributes_model->delete();
                }
            }
        }
    }
}
