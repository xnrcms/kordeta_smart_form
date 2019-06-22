<?php
/**
 * XNRCMS<562909771@qq.com>
 * ============================================================================
 * 版权所有 2018-2028 小能人科技有限公司，并保留所有权利。
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用TP5助手函数可实现单字母函数M D U等,也可db::name方式,可双向兼容
 * ============================================================================
 * Author: 王远庆
 * Date: 2018-02-08
 * Description:数据树结构格式化类
 */
namespace xnrcms;

class DataTree
{
  //用于树型数组完成递归格式的全局变量
  private $formatTree;
  private $data;
  private $root;
  //分类配置
  private $config  = array(
      'pk'          => 'id',
      'pid'         => 'pid',
      'cname'       => 'title',
      'showTag'     => '└',
      'childName'   => '_child',
      'appendField' => [],
      'changeField' => [],
      'deleteField' => [],
  );

  /**
   * 架构函数
   * @param array $totalRows  总的记录数
   * @param array $listRows  每页显示记录数
   * @param array $parameter  分页跳转的参数
   */
  public function __construct($data=[], $root=0)
  {
    $this->data       = $data;
    $this->root       = $root;        
  }

  /**
   * 定制分类树
   * @param string $name  设置名称
   * @param string $value 设置值
   */
  public function setConfig($name,$value) {
      if(isset($this->config[$name])) {
          $this->config[$name] = $value;
      }
  }

  private function _toFormatTree($list = [],$level=0,$path='0_')
  {
    if (!empty($list))
    {
      foreach($list as $key=>$val){
        $tmp_str                = str_repeat('&nbsp;',$level*2) . $this->config['showTag'];
        $title                  = $this->config['cname'];
        $val['count']           = 0;
        $val['level']           = $level;
        $val['parent_id_path']  = $path.$val[$this->config['pk']];
        $val['title_show']      = $level==0 ? $val[$title] . '&nbsp;' : $tmp_str . $val[$title];

        if(!array_key_exists($this->config['childName'],$val)){

          array_push($this->formatTree,$val);
        }else{

          $tmp_ary          = $val[$this->config['childName']];
          $val['count']     = count($tmp_ary);

          unset($val[$this->config['childName']]);

          array_push($this->formatTree,$val);
          
          $this->_toFormatTree($tmp_ary,$level+1,$path.$val[$this->config['pk']].'_'); //进行下一层递归
        }
      } 
    }

    return;
  }

  public function toFormatTree()
  {
    $this->formatTree   = [];
    $this->_toFormatTree($this->listToTree($this->data,$this->root),1);
    return $this->formatTree;
  }

  public function arrayTree()
  {
    return $this->listToTree($this->data,$this->root);
  }

  /**
   * 把返回的数据集转换成Tree
   * @param array $list 要转换的数据集
   * @param string $pid parent标记字段
   * @param string $level level标记字段
   * @return array
   */
  private function listToTree($list, $root = 0)
  {
    // 创建Tree
    $tree = [];
    if(is_array($list))
    {
      //创建基于主键的数组引用
      $refer = array();
      foreach ($list as $key => $data)
      {
        $list[$key][$this->config['childName']]   = [];
        $refer[$data[$this->config['pk']]]        = &$list[$key];
      }

      foreach ($list as $key => $data)
      {
        // 判断是否存在parent
        $parentId =  $data[$this->config['pid']];

        //扩展于某个字段相同值的字段
        if (isset($this->config['appendField']) && !empty($this->config['appendField']))
        {
          foreach ($this->config['appendField'] as $akey => $avalue)
          {
            if (isset($data[$akey]) && !empty($avalue)) $list[$key][$avalue] = $data[$akey];
          }
        }

        //改变某个字段为新的字段，如把id改成key
        if (isset($this->config['changeField']) && !empty($this->config['changeField']))
        {
          foreach ($this->config['changeField'] as $ckey => $cvalue)
          {
            if (isset($data[$ckey]) && !empty($cvalue))
            {
               $list[$key][$cvalue] = $data[$ckey];
            }
          }
        }

        //删除某个字段
        if (isset($this->config['deleteField']) && !empty($this->config['deleteField']))
        {
          foreach ($this->config['deleteField'] as $dvalue)
          {
            if (isset($data[$dvalue])) unset($list[$key][$dvalue]);
          }
        }

        if ($root == $parentId) {
          $tree[] = &$list[$key];
        }else{
          if (isset($refer[$parentId]))
          {
            $parent                               = &$refer[$parentId];
            $parent[$this->config['childName']][] = &$list[$key];
          }
        }
      }
    }

    return $tree;
  }
}
?>
