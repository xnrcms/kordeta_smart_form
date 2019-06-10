<?php
/**
 * XNRCMS<562909771@qq.com>
 * ============================================================================
 * 版权所有 2018-2028 小能人科技有限公司，并保留所有权利。
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Model是根据数据库的表名一一对应的文件，文件名和类名必须是表名，采用驼峰命名法，表名如果有下划线(_)需去除，然后
 * 将紧挨下划线的字母大写
 */
namespace app\common\model;

use think\Model;
use think\Db;

class UserGroupAccess extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    //默认查询方法，如果特殊需求，则自行改造
    public function formatWhereDefault($model,$parame)
    {
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

    /**
     * 通过用户ID获取用户组ID
     * @param  number $uid 用户ID
     * @return array       用户组ID
     */
    public function getUserGroupAccessListByUid($uid=0)
    {
      if (empty($uid))  return [];

      $ckey                       = 'getUserGroupAccessListByUid='.$uid;
      $data                       = $this->getCache($ckey);

      if (empty($data)){

          $lists                  = $this->where('uid','=',$uid)->field('uid,group_id')->select()->toArray();
          $data                   = [];

          if (!empty($lists)) {
            
            foreach ($lists as $key => $value) {
              
              $data[$value['group_id']]  = $value['group_id'];
            }

            sort($data);
          }

          $this->setCache($ckey,$data);
      }

      return $data;
    }

    public function setGroupAccess($uid = 0,$gid = [])
    {
      if ($uid <= 0)  return false;

      $this->where('uid','=',$uid)->delete();
      $this->clearCache(['ckey'=>'getUserGroupAccessListByUid='.$uid]);

      if (!empty($gid)) {
        $gdata    = [];
        foreach ($gid as $key => $value) {
          $gdata[]  = ['uid'=>$uid,'group_id'=>$value];
        }

        $this->saveAll($gdata);
      }
    }

    public function saveData($uid = 0,$gid = 0)
    {
      if ($uid > 0 && $gid > 0)
      {
        $info     = $this->where('uid','=',$uid)->value('uid');
        $data     = ['uid'=>$uid,'group_id'=>$gid];

        if ($info) {

          $this->save($data,['uid'=>$uid]);
        }else{

          $this->allowField(true)->save($data);
        }

        $this->clearCache(['ckey'=>'getUserGroupAccessListByUid']);
      }

      return false;
    }

    public function checkGroupByUidAndGid($uid=0,$gid=0)
    {
      $map      = [];
      $map[]    = ['uid','=',$uid];
      $map[]    = ['group_id','=',$gid];

      return $this->where($map)->value('uid');
    }

    //自行扩展更多
    //...
}