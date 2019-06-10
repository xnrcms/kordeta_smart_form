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
 * Description:列表模板类
 */
namespace xnrcms;

class DevTpl
{
  private $config  = [];
  private $model   = null;
  private $tplType = 0;
  private $tt      = 0;
  private $error   = '';
  private $tplid   = 0;

  /**
   * @access public
   * @return void
   */
  public function __construct($type=0) {
    //定义模型
    $this->tplType  = $type;
    $this->tt       = time();
    $this->tplid    = 0;
    $this->pk       = 'id';
  }

  //配置定制
  public function setConfig($name,$value)
  {
      if(isset($this->config[$name])) $this->config[$name] = $value;
  }

  public function getTplId()
  {
    return $this->tplid;
  }

  //显示表单模板
  public function showFormTpl($tplData = [],$isEdit = 1)
  {
    $data     = $this->tplData($tplData);

    return $this->formatFormTplData($data['list'],$data['info'],$data['tags'],$isEdit);
  }

  //显示列表模板
  public function showListTpl($tplData = [])
  {
    $data     = $this->tplData($tplData);

    return $this->formatListTplData($data['list'],$data['info'],$data['tags']);
  }

  private function tplData($tplData = [])
  {
    $data['tags']   = '';
    $data['info']   = [];
    $data['list']   = [];

    if (empty($tplData)) return $data;

    foreach ($tplData as $key => $tvalue)
    {
      $data['tags'] = $key;

      if (!empty($tvalue))
      {
        foreach ($tvalue as $value)
        {
          if (!isset($value['status']) || $value['status'] != 1)  continue;

          if ( isset($value['pid']) && $value['pid'] == 0)
          {
            $data['info']   = $value;
          }

          if ( isset($value['pid']) && $value['pid'] > 0)
          {
            $config         = !empty($value['config']) ? json_decode($value['config'], true) : [];
            unset($value['config']);

            $data['list'][] = array_merge($value,$config);;
          }
        }
      }
    }

    return $data;
  }

  //格式化列表模板数据
  private function formatListTplData($listNote = [], $info = [], $tags = '')
  {
      //初始化数据
      $search                 = [];
      $thead                  = [];
      $data                   = ['info'=>$info,'search'=>$search,'thead'=>$thead];

      //格式化数据
      if (!empty($listNote)) {

          $width              = 0;
          $counts             = count($listNote);
          $nums               = 0;
          $i                  = 0 ;
          foreach ($listNote as $index => $item)
          {
              $nums++;

              //处理默认数据
              $default            = !empty($item['default']) ? explode(':',$item['default']) : [];
              $item['default']    = [];

              if (isset($default[0]) && isset($default[1]))
              {
                  if ($default[0] == 'parame')
                  {
                      $listNote[$index]['default']['type'] = $default[0];
                      $listNote[$index]['default']['parame'] = $default[1];
                  } else
                  {
                      $parame = array() ;
                      $arr = explode(',',$default[1]) ;
                      foreach ($arr as $key => $value)
                      {
                          $arr = explode('=',$value) ;
                          $parame[$arr[0]] = $arr[1] ;
                      }
                      $item['default']['type'] = $default[0];
                      $item['default']['parame'] = count($arr)>1 ? $parame : $default[1];
                  }
              }

              if ($counts == $nums)
              {
                  $item['width']          = $width >= 100 ? 0 : 100-$width;
              }else{
                  $width                  += $item['width'];
              }

              if ($width >= 100)  continue;

              //表头位数据
              $thead[$index]['id']       = $item['id'] ;
              $thead[$index]['title']    = $item['title'] ;
              $thead[$index]['tag']      = $item['tag'] ;
              $thead[$index]['width']    = $item['width'] ;
              $thead[$index]['edit']     = $item['edit'] ;
              $thead[$index]['search']   = $item['search'] ;
              $thead[$index]['type']     = $item['type'] ;
              $thead[$index]['attr']     = $item['attr'] ;
              $thead[$index]['default']  = $item['default'] ;

              //搜索位数据
              if ($item['search'] === 2)
              {
                  $search[$i]['id']       = $item['id'] ;
                  $search[$i]['title']    = $item['title'] ;
                  $search[$i]['tag']      = $item['tag'] ;
                  $search[$i]['width']    = $item['width'] ;
                  $search[$i]['edit']     = $item['edit'] ;
                  $search[$i]['search']   = $item['search'] ;
                  $search[$i]['type']     = $item['type'] ;
                  $search[$i]['attr']     = $item['attr'] ;
                  $search[$i]['default']  = $item['default'] ;

                  $i++ ;
              }
          }
      }

      $data['info']   = $info ;
      $data['search'] = $search ;
      $data['thead']  = $thead ;
      $data['tags']   = $tags;

      return $data ;
  }

  private function formatFormTplData($formFields = [], $info = [], $tags = '', $isEdit = 0)
  {
    $type                   = $isEdit > 0 ? 'edit' : 'add' ;

    //格式化
    $i                      = 0;
    $formField              = [];
    foreach ($formFields as $index => $item)
    {
        $formFields[$index] = $item;

        if ($formFields[$index][$type] <= 0 && $isEdit != '-1') continue;
        if(!empty($formFields[$index]['default']))
        {
            //获取当前默认值类型
            $default          = explode(':', $formFields[$index]['default']);
            $formFields[$index]['default'] = [];
            if ( isset($default[0]) && isset($default[1]) )
            {
                if ($default[0] == 'parame') {
                    $formFields[$index]['default']['type'] = $default[0];
                    $formFields[$index]['default']['parame'] = $default[1];
                } else {
                    $parame = array() ;
                    $arr = explode(',',$default[1]) ;
                    foreach ($arr as $key => $value) {
                        $arr = explode('=',$value) ;
                        $parame[$arr[0]] = $arr[1] ;
                    }
                    $formFields[$index]['default']['type'] = $default[0];
                    $formFields[$index]['default']['parame'] = count($arr)>1 ? $parame : $default[1];
                }
            }else{
              $formFields[$index]['default']    = isset($default[0]) ? $default[0] : '';
            }
        }

        $formField[$i] = $formFields[$index];
        $i++;
    }

    $arr = [];
    if (!empty($formField))
    {
        foreach ($formField as $k => $v)
        {
            $group = empty($v['group']) ? '基本信息' : $v['group'];
            $arr[$group][]        = $v;
        }
    }

    return ['tags'=>$tags,'info'=>$info,'list'=>$arr];
  }

  

  //获取模板允许提交的数据
  public function getFormTplData($param = [])
  {
    $formtag    = isset($param['formTag']) ? trim($param['formTag']) : '';

    if (empty($formtag) || empty($param))  return [];

    $list       = $this->getReleaseData($formtag,'form','list');
    $field      = [];
    $signData   = [];

    //定义允许提交的字段
    if (!empty($list))
    {
        foreach ($list as $arr)
        {
            $field[]    = $arr['tag'];
        }
    }
    
    //过滤允许提交的数据
    if (!empty($field))
    {
        foreach ($param as $key => $value)
        {
            if (in_array($key,$field))
            {
                $signData[$key]     = $value;
            }
        }
    }

    return $signData;
  }

  private function errorMsg($code='')
  {
    $msg        = [];
    $msg[0]     = '模板标识不能为空';
    $msg[1]     = '模板数据不存在';

    return isset($msg[$code]) ? $msg[$code] : '未知错误';
  }

  //通过模板ID获取模板数据
  public function getTplByFormtag($devtag = '', $code = '', $id=0)
  {
    if ((int)$id <= 0 || empty($devtag) || !in_array($code, ['form','list'])) return [];

    return $this->getReleaseData($devtag,$code,(int)$id);
  }

  public function checkFormTpl($param = [])
  {
    $formtag    = isset($param['formTag']) ? trim($param['formTag']) : '';
    $formid     = isset($param['formId']) ? intval($param['formId']) : 0;

    if (empty($formtag) || $formid <= 0) return false;

    $info       = $this->getReleaseData($formtag,'form','info');

    if (!empty($info) && (int)$info['id'] === (int)$formid) return true;
 
    return false;
  }

  private function getReleaseData($tag = '',$code = '',$type)
  {
    if (empty($tag) || empty($code)) return [];

    $releasePath      = \Env::get('APP_PATH') . 'common/release/' . trim($code,'/') . '/' . $tag.'.php';
    $releaseData      = [];
    $tplData['info']  = [];
    $tplData['list']  = [];

    if (file_exists($releasePath))
    {
        $releaseData      = file_get_contents($releasePath);
        $releaseData      = !empty($releaseData) ? unserialize(urldecode($releaseData)) : [];
    }

    if (!empty($releaseData))
    {
      //如果数据类型是数字，获取指定ID下的数据
      if (is_numeric($type))
      {
        if ($type > 0)
        {
          foreach ($releaseData as $value)
          {
            if ((int)$value['id'] === $type) return $value;
          }
        }
        
        return $releaseData;
      }

      foreach ($releaseData as $key => $value)
      {
        if ($value['pid'] == 0)
        {
          $tplData['info']    = $value;
        }else{
          $tplData['list'][]  = $value;
        }
      }

      return in_array($type, ['info','list']) ? $tplData[$type] : $tplData;
    }

    return $tplData;
  }
}

?>
