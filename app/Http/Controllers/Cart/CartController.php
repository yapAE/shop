<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\ApiController;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends ApiController
{


    /**
     * @param AddCartRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(AddCartRequest $request)
    {
        $user   = $request->user();
        $skuId  = $request->sku_id;
        $amount = $request->amount;

        //查询商品是否在购物车中

        if ($cart = $user->cartItem()->where('product_sku_id',$skuId)->first()){
            //存在就更新
            $cart->update([
                'amount' => $cart->amount + $amount,
            ]);
        }else{

            //否则新开购物车
            $cart  = new CartItem(['amount' => $amount]);

            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }
        return  $this->success($cart);
    }
}
