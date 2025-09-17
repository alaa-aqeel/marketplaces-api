<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $sources = array_keys(config("marketplace"));

        return [
            "fullname" => "required|string|max:255",
            "phone_number" => "required|string|regex:/^07\d{8}$/",
            "products" => "required|array|min:1",
            "products.*.source" => ["required","string", Rule::in($sources)],
            "products.*.external_id" => ["required", "string"],
            "products.*.price" => ["required", "numeric", "min:0"],
            "products.*.currency" => ["required", "string", "in:iq,usd"],
            "products.*.title" => ["required", "string", "max:255"]
        ];
    }
}
