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
 * Description:接口统一调用类
 */
namespace xnrcms;

class ApiRequest
{
  /**
   *  ApiRequestUrl 接口请求url
   *  @var string
   */
  private $ApiDomain; // 接口调用域名或者地址
  private $ApiId;     // 接口调用ID
  private $ApiKey;    // 接口调用秘钥
  private $ApiData;   // 接口数据
  private $ApiError;  // 接口错误信息
  private $ApiUrl;    // 完整的URL

  /**
   * @access public
   * @return void
   */
  public function __construct($ApiDomain="", $ApiId="", $ApiKey="") {
    $this->ApiError   = [];
    $this->ApiDomain  = $ApiDomain;
    $this->ApiUrl     = $ApiDomain;
    $this->ApiId      = $ApiId;
    $this->ApiKey     = $ApiKey;
    $this->ApiData    = [];
    $this->ApiTime    = time();
    $this->baseParame = ['time'=>$this->ApiTime,'apiId'=>$this->ApiId,'terminal'=>1];
  }

  public function getApiData(){

    return $this->ApiData;
  }

  /**
   * 数据校验
   * @access public
   * @return bool
   */
   public function checkData($data){
    if (empty($this->ApiUrl)) {
      $this->ApiError = array("Code" => "api_10001","Msg"=>"ApiUrl not empty");
      return false;
    }
    if (empty($this->ApiId)) {
      $this->ApiError = array("Code" => "api_10002","Msg"=>"ApiId not empty");
      return false;
    }
    if (empty($this->ApiKey)) {
      $this->ApiError = array("Code" => "api_10003","Msg"=>"ApiKey not empty");
      return false;
    }

    if (empty($data)) {
      $this->ApiError = array("Code" => "api_10004","Msg"=>"data not empty");
      return false;
    }

    $sign               = $this->sign($data);
    
    if (empty($sign)) {
      $this->ApiError = array("Code" => "api_10006","Msg"=>"sign is error");
      return false;
    }

    $data['hash']       = $sign;

    $this->ApiData      = $data;
    return true;
   }

  /**
   * 数据POST请求
   * @access public
   * @return bool
   */
  public function postData($data,$name="",$headers=array()){
    $name             = empty($name) ? "" : "/" . trim($name,"/");
    $this->ApiUrl     = trim($this->ApiDomain,"/") . $name;

    $data             = array_merge($this->baseParame,$data);

    if (!$this->checkData($data)) {
      return "";
    }
  
    return $this->RequestHttp($this->ApiUrl,$this->ApiData,"POST",$headers);
  }

  /**
   * 数据GET请求
   * @access public
   * @return bool
   */
  public function getData($name="",$headers=array()){
    $name             = empty($name) ? "" : "/" . trim($name,"/");
    $this->ApiUrl     = trim($this->ApiDomain,"/") . $name;

    if (!$this->checkData($data)) {
      return "";
    }
  
    return $this->RequestHttp($this->ApiUrl,$this->ApiData,"GET",$headers);
  }

  /**
  * 数据请求
  * @access private
  * @return json
  */
  private function RequestHttp($url,$body='',$method='GET',$headers=array()){
    $httpinfo=array();
    $ci=curl_init();
    /* Curl settings */
    curl_setopt($ci,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0);
    curl_setopt($ci,CURLOPT_CONNECTTIMEOUT,30);
    curl_setopt($ci,CURLOPT_TIMEOUT,30);
    curl_setopt($ci,CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($ci,CURLOPT_ENCODING,'');
    curl_setopt($ci,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ci,CURLOPT_HEADER,FALSE);
    switch($method){
      case 'POST':
        curl_setopt($ci,CURLOPT_POST,TRUE);
        if(!empty($body)){
          curl_setopt($ci,CURLOPT_POSTFIELDS,$body);
        }
        break;
      case 'GET':
        curl_setopt($ci,CURLOPT_CUSTOMREQUEST,'DELETE');
        if(!empty($body)){
          $url=$url.'?'.str_replace('amp;', '', http_build_query($body));
        }
    }//wr([$url.'?'.str_replace('amp;', '', http_build_query($body))]);
    curl_setopt($ci,CURLOPT_URL,$url);
    curl_setopt($ci,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ci,CURLINFO_HEADER_OUT,TRUE);
    $response=curl_exec($ci);
    $httpcode=curl_getinfo($ci,CURLINFO_HTTP_CODE);
    $httpinfo=array_merge($httpinfo,curl_getinfo($ci));
    curl_close($ci);
    return $response;
  }

  /**
  * 获取错误信息
  * @access private
  * @return string
  */
  public function getError()
  {
    return !empty($this->ApiError)?array_merge($this->ApiError,['Data'=>$this->ApiData,'Time'=>date('Y-m-d H:i:s',$this->ApiTime),'ApiUrl'=>$this->ApiUrl]):[];
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
   * [getSign 获取参数签名]
   * @param  [array] $data [待签名数据]
   * @return [string]      [签名字符串]
   */
  public function getSign($data = [])
  {

    $data        = array_merge($this->baseParame,$data);

    return $this->sign($data);
  }
}

?>
