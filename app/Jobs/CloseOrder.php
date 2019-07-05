<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $order;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order,$delay)
    {
        $this->order = $order;
        //延迟时间设定,单位秒。
        $this->delay($delay);
    }

    /**
     * Execute the job.
     * 具体执行逻辑
     * 当队列处理器从队列中取出任务，调用handle()方法
     * @return void
     */
    public function handle()
    {
        //已支付订单，直接返回退出
        if($this->order->paid_at){
            return;
        }
        //开始事物
        \DB::transaction(function (){
           //将订单标记为已关闭closed:true
            $this->order->update(['closed' => true]);
            //遍历订单中商品sku，将订单中的数量加回 sku 库存
            foreach ($this->order->items as $item){
                $item->productSku->addStock($item->amount);
            }
        });
    }
}
