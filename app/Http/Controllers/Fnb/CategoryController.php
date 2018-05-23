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
        select * FROM
        (SELECT aa.id,COUNT(t_category.id) as count FROM
                (SELECT b.* FROM t_category AS a LEFT JOIN t_category AS b ON a.id = b.parent_id WHERE a.parent_id =0) as aa
                INNER JOIN t_category on t_category.parent_id= aa.id GROUP BY aa.id) as bb where count=0
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
