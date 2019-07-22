<?php
namespace app\common\model;

use think\Model;
use think\Db;

class Plugin extends Base
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

    public function checkPluginCode($value,$id,$type){

        $res    = $this->where('id','not in',[$id])->where('ptype','eq',$type)->where('code','eq',$value)->value('code');

        return !empty($res) ? true : false;
    }
}
