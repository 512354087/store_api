<?php

namespace App\Http\Controllers\Fnb;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    // 查找出为空的 分类id   fnb
    public function index()
    {
        $res=DB::connection('fnb')->table('t_category')->where('parent_id',0)->get();
        $list=[];
        foreach ($res as $key => $value){
          $category=DB::connection('fnb')->table('t_category')->where('parent_id',$value->id)->get();
          foreach ($category as $k => $v){
              $categoryCount =DB::connection('fnb')->table('t_category')->where('parent_id',$v->id)->count();
              if ($categoryCount==0){
                  array_push($list,$v);
              }
          }
        }
        return $list;

    }
}
