<?php
/**
 * Model层-接口参数模型
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;
use think\Db;

class DevapiParame extends Base
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

    public function getDataIdByTag($api_id,$tag)
    {
      $map                = [];
      $map['api_id']      = $api_id;
      $map['tag']         = $tag;

      $id                 = $this->where($map)->value('id');
      
      return !empty($id) ? $id : 0;
    }

    public function getDevapiParameByApiids($apiids=[])
    {
      if (empty($apiids)) return [];

      $list   = $this->where('api_id','in',$apiids)->select();
      return !empty($list) ? $list->toArray() : [];
    }

    public function delParameByApiId($api_id = 0)
    {
      if ((int)$api_id > 0) {
        $this->where('api_id','=',(int)$api_id)->delete();
      }
    }
}
