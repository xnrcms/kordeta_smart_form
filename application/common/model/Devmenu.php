<?php
/**
 * Model层-代码示例
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;
use think\Db;

class Devmenu extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    public function formatWhereDefault($model,$parame)
    {
        $model->where('project_id','>=',0);

        $ownerid  = isset($parame['ownerid']) ? $parame['ownerid'] : -1;
        $model->where('ownerid','=',$ownerid);
        if (isset($parame['status'])) $model->where('status','=',$parame['status']);
        return $model;
    }

    public function formatWhereChildMenu($model,$parame)
    {
    	$model->where('pid','=',$parame['id']);

    	return $model;
    }

    /*public function checkValue($value,$id,$field,$ownerid = -1)
    {
        $res    = $this->where('id','not in',[$id])->where($field,'eq',$value)->value($field);

        return !empty($res) ? true : false;
    }*/

    public function saveData($id = 0,$parame = [])
    {
        $info           = $id <= 0 ? $this->addData($parame) : $this->updateById($id,$parame);

        //设置path
        $updata         = [];
        if (isset($parame['pid']))
        {
          if ($parame['pid'] > 0)
          {
            $pinfo              = $this->getOneById($parame['pid']);
            $updata['path']     = $pinfo['path'] . $info['id'] . '-';
          }else{
            $updata['path']     = '-' . $info['id'] . '-';
          }

          $updata['update_time']  = time() + 2;
          $info                   = $this->updateById($info['id'],$updata);
        }

        $ownerid  = isset($info['ownerid']) ? $info['ownerid'] : 0;
        $ctag     = 'table_' . $this->name . '_getList_Ownerid='.$ownerid;

        $this->clearCache(['ctag'=>$ctag]);

        return $info;
    }

    public function getReleaseMenu($parame)
    {
        $project_id     = isset($parame['project_id']) ? (int)$parame['project_id'] : 0;
        $data           = [];
        $res            = $this->where('project_id','eq',$project_id)->where('status','eq',1)->order('sort desc')->select();
        $data           = !empty($res) ? $res->toArray() : [];

        return $data;
    }

    private function getRulesByUid($parame)
    {
        $rules                  = '';
        $gainfo                 = model('auth_group_access')->baseGetPageList(['apiParame'=>$parame,'whereFun'=>'formatWhereAuthGroupAccess']);

        if (isset($gainfo['lists']) && !empty($gainfo['lists'])) {
            
            $gid                   = [];
            foreach ($gainfo['lists'] as $key => $value) {
                
                $gid[$value['group_id']] = $value['group_id'];
            }

            if (!empty($gid)) {

                $grules              = model('auth_group')->getRulesById($gid);

                if (!empty($grules)) {
                    
                    foreach ($grules as $gkey => $gvalue) {
                        
                        $rules      .= $gvalue['rules'] . ',';
                    }

                    $rules          = trim($rules,',');
                }
            }
        }

        return $rules;
    }

    private function getAuthMenuid($rules='',$group_id=0)
    {
        $rules      = (!empty($rules) && is_string($rules)) ? explode(',',$rules) : [0];

        if ($group_id > 0) {
            
            $ginfo                  = model('auth_group')->getOneById($group_id);

            if (!empty($ginfo) && !empty($ginfo['rules'])) {
                
                $rule               = explode(',',$ginfo['rules']);

                $rules              = !empty($rule) ? array_merge($rules,$rule) : $rules;
            }
        }

        $rules  = array_flip(array_flip($rules));

        sort($rules);

        return $rules;
    }

    public function getChildMenuByPid($pid = 0)
    {
      return $this->where("pid","=",$pid)->select()->toArray();
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
      $ctag       = 'table_' . $this->name . '_getList_Ownerid='.$ownerid;
      $data       = $this->getCache($ckey);

      //自定义扩展
      //.......
      
      if (empty($data) || !isset($data['lists']) || empty($data['lists']))
      {
          $data   = $this->getPageList($parame);
          $this->setCache($ckey,$data,$ctag);
      }

      return $data;
    }

    public function deleteData($id = 0)
    {
      $info         = $this->getRow($id);

      if (!empty($info))
      {
        $ownerid      = isset($info['ownerid']) ? $info['ownerid'] : 0;
        $ctag         = 'table_' . $this->name . '_getList_Ownerid='.$ownerid;
        $delCount     = $this->delData($id);

        $this->clearCache(['ctag' => $ctag]);
      }else{
        $delCount     = 0;
      }

      return $delCount;
    }

    public function getMenuAuthList($menuid = [],$uid = 0)
    {
      if (empty($menuid) || (int)$uid <= 0 || !is_array($menuid)) return [];

      /*$ckey       = 'getMenuAuthListByUid=' . $uid;
      $data       = $this->getCache($ckey);
      if (empty($data))
      {*/
        $data     = $this->where("status","=",1)->where("id","in",$menuid)->order("sort desc")->select()->toArray();

        /*$this->setCache($ckey,$data);
      }*/

      return $data;
    }

    public function delOperationMenuByPid($pid = 0,$ownerid = 0)
    {
      if ($pid <= 0)  return ;

      $map              = [];
      $map['ownerid']   = $ownerid;
      $map['pid']       = $pid;

      $this->where($map)->where('title','in',['新增','查看','编辑','删除','导入','导出'])->delete();

      $this->clearCache(['ctag'=>'table_' . $this->name . '_getList_' . $ownerid]);
    }

    public function addDataOperation($addData = [],$ownerid = 0)
    {
      $this->insertAll($addData);
      $this->clearCache(['ctag'=>'table_' . $this->name . '_getList_' . $ownerid]);
    }
}
