<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class ChangeOrderStatus
 * @package App\Jobs
 * 任务调度  队列
 * 构造函数可以用来传递需要的参数，handle方法支持依赖注入
 */
class ChangeOrderStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $value;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value="dongli";
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Redis::hset('queue.test','name', $this->value);
    }

    public function failed()
    {
        dump('failed');
    }
}
