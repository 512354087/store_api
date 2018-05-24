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
        // 获得所有的二级分类
//     SELECT  c.id,COUNT(c.id)  FROM (SELECT b.* FROM t_category AS a INNER JOIN t_category AS b ON a.id = b.parent_id WHERE a.parent_id =0 GROUP BY b.id) AS c INNER JOIN t_category AS d ON c.id = d.parent_id GROUP BY c.id
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
