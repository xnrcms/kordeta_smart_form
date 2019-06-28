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

class KorTable7 extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    //默认查询方法，如果特殊需求，则自行改造
    public function formatWhereDefault($model,$parame)
    {
        if (isset($parame['search']) && !empty($parame['search']))
        {
          $search  = json_decode($parame['search'],true);

          if (!empty($search))
          {
            foreach ($search as $key => $value)
            {
              if (!empty($value) && (is_string($value) || is_numeric($value)) )
              {
                $model->where('main.'.$key,'eq',trim($value));
              }
            }
          }
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
      $ctag       = 'table_' . $this->name . '_getList';
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

    public function saveData($id = 0,$parame = [])
    {
        $info      = $id <= 0 ? $this->addData($parame) : $this->updateById($id,$parame);
        $info      = !empty($info) ? $info->toArray() : [];

        //自定义扩展
        //.......
        
        $this->clearCache(['ctag'=>'table_' . $this->name . '_getList']);

        return $info;
    }

    public function deleteData($id = 0)
    {
      $delCount     = $this->delData($id);

      //自定义扩展
      //.......
      
      $this->clearCache(['ctag'=>'table_' . $this->name . '_getList']);

      return $delCount;
    }

    //自行扩展更多
    //...
}