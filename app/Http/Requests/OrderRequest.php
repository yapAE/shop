<?php

namespace App\Http\Requests;

use App\Models\ProductSku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //判断用户提交信息是否存在及其归属
            'address_id' => [
                'required',
               // Rule::exists('user_addresses','id')->where('user_id',$this->user()->id),
            ],
            'items' => ['required','array'],
            'items.*.sku_id' => [   //检查items 下子数组的 sku_id 参数
                'required',
                function ($attribute,$value,$fail){

                    if(!$sku = ProductSku::find($value)){
                        return $fail('该商品不存在');
                    }

                    if (!$sku->product->on_sale){
                        return $fail('该商品未上架');
                    }

                    if (!$sku->stock === 0){
                        return $fail('该商品已售完');
                    }
                    //获取当前索引
                    preg_match('/items\.(\d+)\.sku_id/',$attribute,$match);
                    $index = $match[1];
                    //根据索引获取购买数量

                    $amount = $this->input('item')[$index]['amount'];

                    if($amount >0 && $amount > $sku->stock){
                        return $fail('该商品库存不足');
                    }
                }
            ],

            'items.*.amount' => ['required','integer','min:1'],
        ];
    }
}
