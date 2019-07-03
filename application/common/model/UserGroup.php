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

class UserGroup extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    //默认查询方法，如果特殊需求，则自行改造
    public function formatWhereDefault($model,$parame)
    {
        if (isset($parame['search']) && !empty($parame['search']))
        {
          $search     = json_decode($parame['search'],true);

          if (!empty($search))
          {
            foreach ($search as $key => $value)
            {
              if (!empty($value) && (is_string($value) || is_numeric($value)) )
              {
                $model->where('main.'.$key,'eq',$value);
              }
            }
          }
        }

        if (isset($parame['ownerid'])) {
          $model->where('main.ownerid','eq',$parame['ownerid']);
        }

        return $model;
    }

    public function getRow($id = 0)
    {
      $info       = $this->getOneById($id);
      $info       = !empty($info) ? $info->toArray() : [];

      //自定义扩展
      //.......

      return $info;
    }

    public function getList($parame)
    {
      $ckey       = (isset($parame['cacheKey']) && !empty($parame['cacheKey'])) ? $this->name . json_encode($parame['cacheKey']) : '';

      $ownerid    = isset($parame['apiParame']['ownerid']) ? $parame['apiParame']['ownerid'] : -1;
      $ctag       = 'table_' . $this->name . '_getList_Ownerid=' . $ownerid;
      $data       = $this->getCache($ckey);

      //自定义扩展
      //.......

      if (empty($data) || !isset($data['lists']) || empty($data['lists']))
      {
          $data                 = $this->getPageList($parame);
          $this->setCache($ckey,$data,$ctag);
      }

      return $data;
    }

    public function saveData($id = 0,$parame = [])
    {
        $info      = $id <= 0 ? $this->addData($parame) : $this->updateById($id,$parame);
        $info      = !empty($info) ? $info->toArray() : [];

        //自定义扩展
        //.......
        
        $ownerid  = isset($info['ownerid']) ? $info['ownerid'] : 0;
        $ctag     = 'table_' . $this->name . '_getList_Ownerid=' . $ownerid;

        $this->clearCache(['ctag'=>$ctag]);

        return $info;
    }

    public function deleteData($id = 0)
    {
      $info         = $this->getRow($id);

      if (!empty($info))
      {
        $ownerid      = isset($info['ownerid']) ? $info['ownerid'] : 0;
        $ctag         = 'table_' . $this->name . '_getList_Ownerid='.$ownerid;
        $delCount     = $this->delData($id);

        //删除分组权限
        model('user_group_access')->delGroupAccessByGroupId($id);

        $this->clearCache(['ctag' => $ctag]);
      }else{
        $delCount     = 0;
      }

      return $delCount;
    }
    
    /**
     * 通过用户ID获取用户组列表
     * @param  number $uid 用户ID
     * @return array       用户组数据
     */
    public function getUserGroupListById($id=0)
    {
      if (empty($id))  return [];

      if (is_numeric($id))
      {
        $id   = [$id];
      }

      $lists      = $this->where('status','=',1)->where('id','in',$id)->field('id,title,rules')->select()->toArray();

      return $lists;
    }
    
    public function getAllUserGorupTitle($ownerid = 0)
    {
        $ckey                       = md5('getAllUserGorupTitle');
        $ctag                       = 'table_' . $this->name . '_getList_Ownerid'.$ownerid;
        $data                       = $this->getCache($ckey);

        if (empty($data))
        {
            $map                    = [];
            $map['status']          = 1;
            $map['ownerid']         = $ownerid;
            
            $data                   = $this->where($map)->field('id,title')->select()->toArray();
            $this->setCache($ckey,$data,$ctag);
        }

        return $data;
    }

    public function getGuserByGroupId($gid = 0)
    {
      if ($gid <= 0) return [];

      $ckey                       = md5('getGuserByGroupId='.$gid);
      $data                       = $this->getCache($ckey);

      if (empty($data))
      {
          //根据分组ID获取所有用户ID
          $ugaModel   = model('user_group_access');
          $glists     = $ugaModel->where("group_id","=",$gid)->field('uid')->select()->toArray();

          $guid       = [];
          $ulist      = [];

          foreach ($glists as $value)
          {
            $guid[$value['uid']]  = $value['uid'];
          }

          if (!empty($guid))
          {
            $uModel   = model('user_center');
            $data    = $uModel->where("id","in",$guid)->field('id,username')->select()->toArray();
          }

          $this->setCache($ckey,$data);
      }

      return $data;
    }

    //自行扩展更多
    //...
}