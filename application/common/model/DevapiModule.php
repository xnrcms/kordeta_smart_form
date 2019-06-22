<?php
/**
 * Model层-接口模块模型
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;
use think\Db;

class DevapiModule extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    public function formatWhereDefault($model,$parame){

    	if (isset($parame['search']) && !empty($parame['search'])) {

          $search     = json_decode($parame['search'],true);

          if (!empty($search)) {

            foreach ($search as $key => $value) {

              if (!empty($value) && (is_string($value) || is_numeric($value)) ) {

                $model->where('main.'.$key,'eq',$value);
              }
            }
          }
        }

        return $model;
    }

    public function titleCheck($title='',$id=0){

      return $this->where('title','=',(!empty($title) ? $title : 'xnrcms'))->where('id','neq',$id)->count();
    }
}
