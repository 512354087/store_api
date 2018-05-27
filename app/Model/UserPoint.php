<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    //
    protected $table = 'user_point';

    /**
     * 获取当前时间
     *
     * @return int
     */
    public function freshTimestamp() {
        return time();
    }
    /**
     * 避免转换时间戳为时间字符串
     *
     * @param DateTime|int $value
     * @return DateTime|int
     */
    public function fromDateTime($value) {
        return $value;
    }

}
