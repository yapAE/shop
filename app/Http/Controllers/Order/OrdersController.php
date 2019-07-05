<?php

namespace App\Http\Controllers\Order;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\OrderRequest;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrdersController extends ApiController
{
    //
    public function store(OrderRequest $request)
    {
        $user = Auth::user();
        //此处用到事物
        $order = \DB::transaction(function ()use ($user,$request){
            $address = UserAddress::find($request->address_id);
            //更新用户地址最后使用时间
            $address->update(['last_used_at' => now()]);
            //创建一个订单

            $order = new Order([
                //快照收货地址
               'address'     => [
                   'address'       =>  $address->full_address,
                   'zip'           =>  $address->zip,
                   'contact_name'  =>  $address->contact_name,
                   'contact_phone' =>  $address->contact_phone,
               ],
                'remark'       =>  $request->remark,
                'total_amount' =>  0,//暂时订单金额为0
            ]);

            //关联订单到当前用户
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            $items       = $request->items;

            foreach ($items as $data) {

                $sku = ProductSku::find($data['sku_id']);
                //创建一个OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                   'amount'  => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];

                if ($sku->decreaseStock($data['amount']) <= 0){
                    throw  new InvalidRequestException('该商品库存不足');
                }
            }
            //更新订单总金额
            $order->update(['total_amount' => $totalAmount]);
            //下单成功的商品从购物车移除
            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItem()->whereIn('product_sku_id',$skuIds)->delete();

            //触发超时未支付订单的自动取消并还库存任务(暂设测试用30s)
            $this->dispatch(new CloseOrder($order,30));

            return $order;
        });
        return $this->success($order);
    }
}
