<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_num'=>'require|integer',
            'status' => 'require|integer',
            'product_id'=> 'require|integer',
            'payable_total'=> 'require|numeric',
            'fact_total'=> 'require|numeric',
            'discount_total'=>'require|numeric',
            'user_id'=>'require|integer',
            'user_address_id'=>'require|integer'
        ];
    }
}
