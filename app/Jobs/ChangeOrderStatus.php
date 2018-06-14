<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


/**
 * Class ChangeOrderStatus
 * @package App\Jobs
 * 任务调度  队列
 * 构造函数可以用来传递需要的参数，handle方法支持依赖注入
 */
class ChangeOrderStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $order_id;
    protected $status;
    protected $product_arr;


    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct($order_id,$status,$product_arr)
    {
        $this->order_id=$order_id;
        $this->status=$status;
        $this->product_arr=$product_arr;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       //Log::info('已发送邮件->'.$this->email);

        $order=DB::table('t_order')->where('id',$this->order_id)->get();
        try{
            //未付款
            DB::beginTransaction();
            if ($order[0]->status == 101){   //待支付  当订单状态为待支付
                DB::table('t_order')->where('id',$this->order_id)->update(['status'=>$this->status]);
                foreach($this->product_arr as $k=>$v){
                    $num=DB::table('product_stock')->where('id',$v['stock']->id)->value('num');
                    DB::table('product_stock')->where('id',$v['stock']->id)->update(['num'=>$num+$v['num']]);
                }
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('id为'.$this->order_id.'队列执行失败');

        }


    }


}
