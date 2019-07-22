<?php
/**
 * Model层-代码示例
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;
use think\Db;

class Devform extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    public function formatWhereDefault($model,$parame)
    {
    	if (is_numeric($parame['pid'])) {

            $model->where('pid','eq',$parame['pid']);
        }else{

            $pid        = $this->where('cname','eq',$parame['pid'])->value('id');

            $model->where('pid','eq',$pid);
        }

        return $model;
    }

    public function formatWhereChildForm($model,$parame)
    {
        $model->where('pid','=',$parame['id']);

        return $model;
    }

    public function saveData($id = 0,$parame = [])
    {
        $info      = $id <= 0 ? $this->addData($parame) : $this->updateById($id,$parame);
        $info      = !empty($info) ? $info->toArray() : [];

        //自定义扩展
        //.......
        
        return $info;
    }

    public function checkValue($value,$id,$field,$ownerid = -1)
    {
        $info   = $this->getOneById($id);
        $pid    = empty($info) ? 0 : $info['pid'];
    	$res    = $this->where('id','<>',$id)->where('pid','=',$pid)->where($field,'eq',$value)->value($field);

    	return !empty($res) ? true : false;
    }

    public function checkFieldExist($value,$id,$field)
    {
        $res    = $this->where('id','not in',[$id])->where($field,'eq',$value)->value($field);

        return !empty($res) ? true : false;
    }

    public function getReleaseFormTplList($id = 0)
    {
        if ((int)$id <= 0) return [];

        return $this->where('id|pid','eq',$id)->order('sort DESC')->select()->toArray();
    }

    public function getTplIdByCname($cname = '')
    {
        return $this->where('cname','=',$cname)->value('id');
    }
}
