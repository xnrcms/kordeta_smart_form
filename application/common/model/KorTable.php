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

class KorTable extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk     = 'id';

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
              $field    = explode('_', $key);
              $type     = isset($field[0]) ? $field[0] : '';

              if (in_array($type, ['input','textarea','turnover']) && !empty($value))
              {
                $model->where('main.'.$key,'like','%' . trim($value) . '%');
              }

              if (in_array($type, ['radio']) && !empty($value))
              {
                $model->where('main.'.$key,'eq',trim($value));
              }

              if (in_array($type, ['select','checkbox']) && !empty($value))
              {
                if (is_array($value))
                {
                  foreach ($value as $v)
                  {
                    $model->where('main.'.$key,'like','%' . trim($v) . '%');
                  }
                }else{
                  $model->where('main.'.$key,'like','%' . trim($value) . '%');
                }
              }

              if (in_array($type, ['date']) && !empty($value))
              {
                $stime        = isset($value[0]) ? strtotime($value[0] . " 00:00:00") : 0;
                $etime        = isset($value[1]) ? strtotime($value[1] . " 23:59:59") : 0;
                if ($stime <= 0 && $etime > 0) {
                  $stime  = $etime - (3600*24);
                }elseif ($stime > 0 && $etime <= 0) {
                  $etime  = $stime + (3600*24);
                }else if ($stime <= 0 && $etime <= 0) {
                  $stime      = strtotime(date('Y-m-d 00:00:00'));
                  $etime      = $stime + (3600*24);
                }

                $model->where('main.' . $key,'>=',$stime);
                $model->where('main.' . $key,'<=',$etime);
              }
            }
          }
        }

        return $model;
    }

    public function getRow($tableName,$id = 0)
    {
      $ckey       = 'table_' . $tableName . '_getRow=' . $id;
      $info       = $this->getCache($ckey);

      if (empty($info))
      {
        $map        = [];
        $map['id']  = $id;

        $info       = db($tableName)->where($map)->find();

        $this->setCache($ckey,$info);
      }

      //自定义扩展
      //.......

      return $info;
    }

    public function getList($tableName,$parame)
    {
      $ckey       = (isset($parame['cacheKey']) && !empty($parame['cacheKey'])) ? $this->name . json_encode($parame['cacheKey']) : '';
      $ctag       = 'table_' . $tableName . '_getList';
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

    public function saveData($tableName, $id = 0,$parame = [])
    {
        $this->clearCache([
          'ctag'  => 'table_' . $tableName . '_getList',
          'ckey'  => 'table_' . $tableName . '_getRow=' . $id
        ]);

        $db        = db($tableName);

        if ($id <= 0)
        {
          $id     = $db->insertGetId($parame);
        }else{
          $db->where('id',$id)->update($parame);
        }

        $info      = $this->getRow($tableName,$id);

        return $info;
    }

    public function deleteData($tableName, $id = 0)
    {
      $this->clearCache([
        'ctag'  => 'table_' . $tableName . '_getList',
        'ckey'  => 'table_' . $tableName . '_getRow=' . $id
      ]);

      $delCount     = db($tableName)->delete($id);

      return $delCount;
    }

    public function saveDataAll($tableName,$saveData = [])
    {
      if (!empty($saveData))
      {
        db($tableName)->insertAll($saveData);
        $this->clearCache(['ctag'=>'table_' . $tableName . '_getList']);
      }
    }

    public function getRowForTpl($tableName,$map)
    {
        $info   = db($tableName)->where($map)->find();
        
        return $info;
    }

    //自行扩展更多
    //...
}