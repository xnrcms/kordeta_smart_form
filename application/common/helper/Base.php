<?php
/**
 * Helper 接口基础帮助类
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\helper;

use think\Controller;
use think\facade\Lang;

class Base
{   
    public  $ApiId;         //目前固定写死，以后有需要可以动态获取
    public  $ApiKey;        //目前固定写死，以后有需要可以动态获取
    public  $returnData;    //接口数据信息
    public  $backData;      //接口返回的数据格式
    public  $checkData;     //接口请求的数据格式
    public  $controllerName;
    public  $actionName;
    public  $moduleName;
    private $UserToken;
    private $UserId;
    private $notAllowSignField;

    public function __construct($parame=[],$controllerName='',$actionName='',$moduleName='')
    {
      //系统配置合并
      merge_config();

      //参数初始化
      $this->ReturnData       = null;
      $this->ApiId            = config('dev_config.api_sign_id');
      $this->ApiKey           = config('dev_config.api_sign_key');
      $this->controllerName   = $controllerName;
      $this->actionName       = $actionName;
      $this->moduleName       = $moduleName;
      $this->postData         = $parame;
      $this->UserToken        = "";
      $this->UserId           = 0;
      $this->OwnerId          = 0;
      $this->notSignField     = ['fileName'];

      //加载语言包
      $this->loadLang('zh-cn',$this->controllerName);

      //数据初始化
      $this->setBaseParameData();
      $this->setReturnData();
      $this->setParameData();
    }

    /**
    *[getReturnData 返回JSON数据]
    * @access protected
    * @param data array()   返回数据
    * @return bool
    */
    protected function returnData($data=[])
    {
      $this->setReturnData($data);
      return json($this->getReturnData());
    }

    /**
    *[getReturnData 返回JSON数据]
    * @access protected
    * @param data array()   返回数据
    * @return bool
    */
    protected function setReturnData($data=array())
    {
      $domain       = trim(request()->domain(),'/');
      $apiurl       = $domain.'/'.$this->moduleName.'/'.$this->controllerName.'/'.$this->actionName;
      $apitime      = date('Y-m-d H:i:s',time());
      $base         = ['Code' =>'100000','Msg'=>lang('100000'),'Time'=>$apitime,'ApiUrl'=>$apiurl,'Data'=>''];

      $this->returnData = array_merge($base,$data);
      return $this->returnData['Code'] === '200' ? true : false;
    }

    /**
     * [getReturnData 获取接口返回数据]
     * @access public
     * @return array
     */
    public function getReturnData()
    {
      if (empty($this->backData))
      {
        $this->setReturnData(['Code' => '203', 'Msg'=>lang('100016')]);
        return $this->returnData;
      }

      //新接口模式数据格式化调用
      if (isset($this->backData[0][0]) && $this->backData[0][0] == 'Code')
      {
        //接口返回参数结构格式模板
        $dataTpl    = toLevel($this->backData);
        if (empty($dataTpl)) return ['Code' => '203', 'Msg'=>lang('120022')];

        //生成测试数据
        if (isset($this->returnData['Data'][0]) && $this->returnData['Data'][0] == 'TEST')
        {  
          return $this->apiTestData($dataTpl,$this->returnData);
        }

        return $this->formatBackData($dataTpl,$this->returnData);
      }

        return $this->returnData;
    }

    /**
     * [formatBackData 格式化接口返回数据]
     * @access protected
     * @param  array $dataTpl [返回数据格式模板]
     * @param  array $data    [需要格式的数据]
     * @return array          [格式完后的数据]
     */
    private function formatBackData($dataTpl,$data = [])
    {
      if (empty($dataTpl))  return [];

      $bacaData       = [];
      $val            = '';

      foreach ($dataTpl as $key => $value)
      {
        $data[$value[0]]      = isset($data[$value[0]]) ? $data[$value[0]] : '';

        if (isset($data[$value[0]]) )
        {
          //如果设置了函数处理
          $mock     = (isset($value[6]) && !empty($value[6])) ? explode('|',$value[6]) : '';
          $func     = (isset($mock[0]) && !empty($mock[0])) ? $mock[0] : '';
          $extends  = (isset($mock[1]) && !empty($mock[1])) ? $mock[1] : '';

          if (!empty($func) && $func != '/' )
          {
            if (function_exists($func))
            {
              //函数处理
              $data[$value[0]]  = $func($data[$value[0]],$data,$extends);
            }elseif (method_exists($this,$func))
            {
              //helper中处理
              $data[$value[0]]  = $this->$func($data[$value[0]],$data,$extends);
            }else{
              wr($value[0].":未定义函数:".$func .'或Helper类方法：'.$func);
            }
          }
          //--end
          
          switch (strtolower($value[1]))
          {
            case 'string':  $val  = isset($data[$value[0]]) ? trim($data[$value[0]]) : ''; break;
            case 'number':  $val  = intval($data[$value[0]]); break;
            case 'float':   $val  = !empty($data[$value[0]]) ? trim($data[$value[0]]) : '0.00' ; break;
            case 'array':
              if (!empty($value[7]) && isset($data[$value[0]]) && is_array($data[$value[0]]) && !empty($data[$value[0]]))
              {
                if (isset($data[$value[0]][0]))
                {
                  $listsVal = [];

                  foreach ($data[$value[0]] as $key1 => $value1)
                  {
                    $listsVal[]   = $this->formatBackData($value[7],$value1);
                  }

                  $val = $listsVal;
                }else{
                  $val  = $this->formatBackData($value[7],$data[$value[0]]);
                }
              }else{
                $val  = [];
              }
              break;
            case 'object':
              if (isset($data[$value[0]]) && is_array($data[$value[0]]) && !empty($data[$value[0]]))
              {
                  $val  = $this->formatBackData($value[7],$data[$value[0]]);
              }else{
                  $val  = (object)[];
              }
              break;
            case 'json':
              if (is_array($data[$value[0]]))
              {
                $val    = !empty($data[$value[0]]) ? json_encode($data[$value[0]]) : ''; break;
              }else{
                $val    = !empty($data[$value[0]]) ? trim($data[$value[0]]) : ''; break;
              }
            case 'bool':  $val  = !empty($data[$value[0]]) ? true : false; break;
            default:      $val  = trim($data[$value[0]]); break;
          }
        }else{
          $val    = '';
        }

        $bacaData[$value[0]]  = $val;
      }

      return $bacaData;
    }

    /**
     * [checkData 数据安全校验]
     * @access protected
     * @param  array $postData  [接口提交的所有数据]
     * @param  array $checkData [待验证的数据]
     * @return array            [返回合法数据]
     */
    protected function checkData($postData)
    {
      if (isset($postData['is_inside']) && $postData['is_inside'] == 1) return true;
      //if (empty($this->checkData) || !is_array($this->checkData)) return $this->setReturnData();

      $checkData      = array_merge($this->baseParame,$this->checkData);
      $parameData     = array();
      $signData       = [];

      //先判断数据传递是否完整合法
      foreach ($checkData as $val)
      {
        if (in_array($val[0], $this->notSignField)) continue;

        //检验接口参数是否存在
        if (!isset($postData[$val[0]]))
        {
          //漏传必须参数
          return $this->setReturnData(['Code' => '203', 'Msg'=>lang('100007',[$val[0],$val[0]])]);
        }

        //接口参数非空检查
        if ($val[2] == 1)
        {
          if ( 
            ($val[1] == 'number' && intval($postData[$val[0]]) == 0) ||
            ($val[1] == 'string' && empty($postData[$val[0]])) ||
            ($val[1] == 'json' && empty($postData[$val[0]]))
          )
          {
            return $this->setReturnData(['Code' => '203', 'Msg'=>lang('100008',[$val[0],$val[0]])]);
          }
        }

        //如果是Json数据，并且非空，需要判断Json数据格式是否合法
        if ($val[1] == 'json' && !empty($postData[$val[0]]) && !is_json($postData[$val[0]]))
        {
          return $this->setReturnData(['Code' => '203', 'Msg'=>lang('100017',[$val[0],$val[0]])]);
        }

        //原始待签名数据
        $signData[$val[0]]    = $postData[$val[0]];

        //数据处理
        $parameData[$val[0]]  = $val[1] == 'number' ? intval($postData[$val[0]]) : trim($postData[$val[0]]);
      }
      
      //判断签名校验是否通过
      $sign                   = $this->sign($signData);
      if ( empty($sign) || $sign !=  $signData['hash'])
      return $this->setReturnData(array('Code' => '203','Msg'=>lang('100006')));

      //校验是否有权调用接口
      $isApiId  = $this->checkApiId($signData['apiId']);
      if (!$isApiId) return $this->setReturnData(['Code' => '203','Msg'=>lang('120024')]);

      $this->UserToken  = isset($parameData['hashid']) ? trim($parameData['hashid']) : '';

      //删除过期Token
      model('api_token')->deleteTokenByToken();

      //校验用户身份ID是否正确
      if (!empty($this->UserToken))
      {
        //检验UserToken是否合法
        if ( $this->checkHashid() <= 0 )
        return $this->setReturnData(['Code'=>'201','Msg'=>lang('201')]);

        //检验用户是否异常
        $userStatus     = $this->checkUserInfo();

        if ($userStatus !== 1) $this->clearToken();
        if ($userStatus === 0) return $this->setReturnData(['Code'=>'205','Msg'=>lang('205')]);
        if ($userStatus === 2) return $this->setReturnData(['Code'=>'206','Msg'=>lang('206')]);
      }
      
      return $parameData;
    }

    /**
     * [sign 数据签名]
     * @access private
     * @param  array  $data [待签名数据]
     * @return string       [签名串]
     */
    private function sign($data=array())
    {
        if (!empty($data))
        {
          if(isset($data['hash'])) unset($data['hash']);

          //按字母排序
          ksort($data);

          $signStr    = "";
          foreach ($data as $key => $value)
          {
            $signStr  .= $key . $value;
          }

          $signStr  .= $this->ApiKey;
          
          return md5($signStr);
        }

        return "";
    }

    /**
     * [checkApiId 校验ApiId是否合法，合法返回秘钥]
     * @access private
     * @param  string $apiId [接口调用ID]
     * @return bool
     */
    private function checkApiId($apiId)
    {
      return md5('xnrcms_api_key'.$apiId) == $this->ApiKey ? true : false;
    }

    public function getUserId()
    {
      return $this->UserId;
    }

    public function getOwnerId()
    {
      //如果是系统会员
      $gids     = $this->getGroupIds();
      
      if (isset($gids[0]) && in_array($gids[0], [1]) ) {
        $this->OwnerId  = 0;
      } else if (isset($gids[0]) && in_array($gids[0], [2]) ) {
        $this->OwnerId  = $this->UserId;
      }else{
        //暂时不支持 -1
        $this->OwnerId  = -1;
      }

      return $this->OwnerId;
    }

    /**
     * [checkHashid 校验uid和hashid是否合法]
     * @access private
     * @param  int    $uid    用户ID
     * @param  string   $hashid 用户秘钥
     * @return bool
     */
    private function checkHashid()
    {
        if (strlen($this->UserToken) === 32)
        {
          $tokenInfo    = model('api_token')->getTokenInfoByToken($this->UserToken);
          if (!empty($tokenInfo) && isset($tokenInfo['token']) && $tokenInfo['token'] === $this->UserToken && (int)$tokenInfo['uid'] > 0 )
          {
            $this->UserId     = (int)$tokenInfo['uid'];
            return 1;
          }

          //删除不合法的token
          model('api_token')->deleteTokenByToken($token);

          return 0;
        }

        $JWT    = $this->getJwtInfo($this->UserToken);
        $token  = md5($this->UserToken);

        if (is_numeric($JWT) && $JWT === -1) return -1;
        if (is_object($JWT) && !empty($JWT) && (int)$JWT->uid > 0 && $JWT->exp > time())
        {
          $tokenInfo    = model('api_token')->getTokenInfoByToken($token);

          if ( !empty($tokenInfo) && isset($tokenInfo['token']) && $tokenInfo['token'] === $token )
          {
            $this->UserId     = (int)$JWT->uid;
            return 1;
          }
        }

        //删除不合法的token
        model('api_token')->deleteTokenByToken($token);

        return 0;
    }

    private function clearToken()
    {
      model('api_token')->deleteTokenByToken($this->UserToken);
    }

    private function checkUserInfo()
    {
        $userInfo     = model('user_center')->getRow($this->UserId);
        if(!empty($userInfo))
        {
          //1用户正常 2用户被禁用
          return isset($userInfo['status']) && $userInfo['status'] === 1 ? 1 : 2;
        }

        return 0;//用户不存
    }

    private function getJwtInfo($hashid = '')
    {
        try{
          $token  = string_encryption_decrypt(base64_decode($hashid),'DECODE');
          $key    = config('extend.uc_auth_key');
          $JWT    = \Firebase\JWT\JWT::decode($token,$key,["HS256"]);

          return !empty($JWT) ? $JWT : 0;
        }
        catch (\Exception $exception)
        {
          return $exception->getMessage() === 'Expired token' ? -1 : 0;
        }
    }

    private function apiTestData($dataTpl=[],$data=[])
    {
      if (empty($dataTpl))  return [];

      $bacaData       = [];
      $val            = '';

      foreach ($dataTpl as $key => $value)
      {
        $isValue  = ( isset($data[$value[0]]) && !empty($data[$value[0]]) ) ? true : false;

        switch (strtolower($value[1]))
        {
          case 'string':
            $val = $isValue ? trim($data[$value[0]]) : $value[0].'_test_val';
            break;
          case 'number':
            $val = $isValue ? intval($data[$value[0]]) : 1;
            break;
          case 'float':
            $val  = $isValue ? trim($data[$value[0]]) : '0.01' ; break;
          case 'array':
            if (!empty($value[7]) && isset($data[$value[0]]) && is_array($data[$value[0]]) && !empty($data[$value[0]]))
            {
              if (isset($data[$value[0]][0]))
              {
                $listsVal = [];
                foreach ($data[$value[0]] as $key1 => $value1)
                {
                  $listsVal[]   = $this->apiTestData($value[7],$value1);
                }
                
                $val = $listsVal;
              }else{
                $val  = $this->apiTestData($value[7],$data[$value[0]]);
              }
            }else{
              $val  = [];
            }

            break;
          case 'object':
            if (isset($data[$value[0]]) && is_array($data[$value[0]]) && !empty($data[$value[0]]))
            {
                $val  = $this->formatBackData($value[7],$data[$value[0]]);
            }else{
                $val  = (object)[];
            }
            break;
          case 'json':
            if ($isValue)
            {
              if (is_array($data[$value[0]]))
              {
                $val    = !empty($data[$value[0]]) ? json_encode($data[$value[0]]) : ''; break;
              }else{
                $val    = !empty($data[$value[0]]) ? trim($data[$value[0]]) : ''; break;
              }
            }else{

              $val      = json_encode([]);
            }
          case 'bool':  $val  = $isValue ? true : false; break;
          default:    $val  = $isValue ? trim($data[$value[0]]) : $value[0].'_test_val'; break;
        }
        
        $bacaData[$value[0]]  = $val;
      }

      return $bacaData;
    }

   	/**
    * [helper 内部Helper调用]
    * @param  array  $parame     [参数数据]
    * @param  string $namespace  [命名空间]
    * @param  string $className  [对应Helper类名]
    * @param  string $methodName [对应Helper操作的方法名]
    * @param  string $[msgCode]  [错误码号段]
    * @return array              [接口返回数据]
    */
   	protected function helper($parame,$mName,$cName,$aName)
   	{
      //构造命名空间
      $namespace        = '\\'. 'app\\'.strtolower($mName).'\\helper';
      $models           = '\\'. trim($namespace,'\\') . '\\' . trim($cName,'\\');

      //实例化操作对象
      $object         = new $models($parame,$cName,$aName,$mName);

      //返回数据
      return $object->isInside($parame,$aName);
   	}

    private function setBaseParameData()
    {
      $this->baseParame = [
        ['time','number',1,'100001',time(),'调用时间','接口请求调用的时间'],
        ['hash','string',1,'100002','/','签名串','请求的参数按照约定生成的字符串'],
        ['apiId','string',1,'100004','/','接口ID','接口调用授权ID,有接口方提供'],
        ['terminal','number',1,'100018','/','终端类型','终端类型：1-系统后台，2-IOS,3-安卓，4-H5,5-小程序'],
      ];
    }

    private function setParameData()
    {
      $cName              = formatStringToHump($this->controllerName);
      $apicode            = md5(strtolower($this->moduleName.$cName.$this->actionName));
      $parameContent      = get_release_data($apicode,'api',1);
      $this->checkData    = isset($parameContent['request_parame']) ? $parameContent['request_parame'] : [];
      $this->backData     = isset($parameContent['back_parame']) ? $parameContent['back_parame'] : '';
    }

    private function loadLang($code = '',$cname = '')
    {
      Lang::load(\Env::get('APP_PATH') . 'common/lang/' . $code . '/lang.php');
      Lang::load(\Env::get('APP_PATH') . 'common/lang/' . $code . '/common.php');
      Lang::load(\Env::get('APP_PATH') . 'common/lang/' . $code . '/' . strtolower($cname) . '.php');
    }

    public function getGroupIds()
    {
      return model('user_group_access')->getUserGroupAccessListByUid($this->UserId);
    }

    public function getUserRulesId()
    {
      //获取用户私有权限
      $userInfo       = model('user_detail')->getRow($this->UserId);

      //私有权限
      $urules         = (isset($userInfo['rules']) && !empty($userInfo['rules'])) ? explode(',', $userInfo['rules']) : [];

      //分组权限
      $gids           = $this->getGroupIds();

      //获取用户组列表
      $glist          = model('user_group')->getUserGroupListById($gids);
      
      //整理组权限
      $grules         = [];
      if (!empty($glist))
      {    
          foreach ($glist as $key => $value)
          {
              $rules  = !empty($value['rules']) ? explode(',', $value['rules']) : [];
              $grules = array_merge($rules,$grules);
          }
      }

      $allRules     = array_merge($urules,$grules);
      $allRules     = array_flip(array_flip($allRules));

      return $allRules;
    }

    public function checkUserPower( $menuid = 0 )
    {
      //不需要验证或者是超级管理员ID 不校验权限
      $administrator_id        = config('extend.administrator_id');
      if ($menuid == -1 || (int)$administrator_id === $this->UserId) return true;
      
      $userAllRules    = $this->getUserRulesId();
      
      return in_array($menuid, $userAllRules) ? true : false;
    }
}