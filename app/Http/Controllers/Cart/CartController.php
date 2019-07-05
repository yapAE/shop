<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\ApiController;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends ApiController
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        $user = Auth::user();

        $cartItems = $user->cartItem()->with(['productSku.product'])->get();

        $addresses = $user->addresses()->orderBy('last_used_at','desc')->get();

        $data = [
            'cartItem'  => $cartItems,
            'addresses' => $addresses,
        ];
/*        foreach ($cartItems as &$item){
            $item['sum'] = $item['product_sku'] * $item['amount'];

        }*/
        return  $this->success($data);
    }
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

    /**
     * @param ProductSku $productSku
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(ProductSku $productSku,Request $request)
    {

        Auth::user()->cartItem()->where('product_sku_id',$productSku->id)->delete();

        return  $this->message('已移除');
    }
}
