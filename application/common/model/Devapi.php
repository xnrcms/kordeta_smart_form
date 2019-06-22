<?php
/**
 * Model层-接口模型
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;
use think\Db;

class Devapi extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    public function formatWhereDefault($model,$parame){

    	if (isset($parame['search']) && !empty($parame['search'])) {

          $search     = json_decode($parame['search'],true);

          if (!empty($search)) {

            foreach ($search as $key => $value) {

              if (!empty($value) && (is_string($value) || is_numeric($value)) ) {

                if($key == 'api_module_type' && $value == '2'){
                    $model->where('api_module_type','in',[1,2]);
                }elseif($key == 'api_module_type' && $value == '1'){
                    $model->where('api_module_type','in',[0,1,2,3]);
                }else{
                    $model->where('main.'.$key,'eq',$value);
                }
              }
            }
          }
        }

        return $model;
    }

    public function formatWhereDevapiCount($model,$parame){

      return $model->where('module_id','eq',$parame['id']);
    }

    public function apiurlCheck($urlmd5='',$id=0){

      $urlmd5  = !empty($urlmd5) ? $urlmd5 : 'xnrcms';
      $count    = $this->where('urlmd5','=',$urlmd5)->where('id','neq',$id)->count();

      return $count;
    }

    public function getDevapiListByModelid($modelid=0,$apiid=0)
    {
      $map              = [];
      $map[]            = ['module_id','=',$modelid];
      if ($apiid > 0) {
        $map[]          = ['id','=',$apiid];
      }
      $list             = $this->where($map)->select();
      return !empty($list) ? $list->toArray() : [];
    }

    public function saveBaseapi($updata = [])
    {
      if (!empty($updata))
      {
          return $this->insertGetId($updata);
      }

      return 0;
    }
}
