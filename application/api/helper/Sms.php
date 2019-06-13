<?php
/**
 * XNRCMS<562909771@qq.com>
 * ============================================================================
 * 版权所有 2018-2028 小能人科技有限公司，并保留所有权利。
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Helper只要处理业务逻辑，默认会初始化数据列表接口、数据详情接口、数据更新接口、数据删除接口、数据快捷编辑接口
 * 如需其他接口自行扩展，默认接口如实在无需要可以自行删除
 */
namespace app\api\helper;

use app\common\helper\Base;
use think\facade\Lang;

class Sms extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'Sms';
    private $smsConfig          = [];
	
	public function __construct($parame=[],$className='',$methodName='',$modelName='')
    {
        parent::__construct($parame,$className,$methodName,$modelName);
        $this->apidoc           = request()->param('apidoc',0);
        $this->smsConfig        = config('sms_config.');     
    }
    
    /**
     * 初始化接口 固定不用动
     * @param  [array]  $parame     接口需要的参数
     * @param  [string] $className  类名
     * @param  [string] $methodName 方法名
     * @return [array]              接口输出数据
     */
    public function apiRun()
    {   
        if (!$this->checkData($this->postData)) return json($this->getReturnData());
        //加载验证器
        $this->dataValidate = new \app\api\validate\DataValidate;
        
        //规避没有设置主表名称
        if (empty($this->mainTable)) return $this->returnData(['Code' => '120020', 'Msg'=>lang('120020')]);
        
        //接口执行分发
        $methodName     = $this->actionName;
        $data           = $this->$methodName($this->postData);
        //设置返回数据
        $this->setReturnData($data);
        //接口数据返回
        return json($this->getReturnData());
    }

    //支持内部调用
    public function isInside($parame,$aName)
    {
        return $this->$aName($parame);
    }

    /*api:704cbfb35b0703f4566dbf3277a0eb63*/
    /**
     * * 发送手机验证码
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function sendMobileCode($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //删除过期短信
        $dbModel->delValidityData();

        $sms_validity_time = intval($this->smsConfig['sms_validity_time']);
        $sms_interval_time = intval($this->smsConfig['sms_interval_time']);
        $sms_limit_ip      = intval($this->smsConfig['sms_limit_ip']);
        $sms_service       = intval($this->smsConfig['sms_service']);
        $sms_sign          = trim($this->smsConfig['sms_sign']);
        $sms_code_num      = trim($this->smsConfig['sms_code_num']);

        if (empty($sms_sign))
        return ['Code' => '200007', 'Msg'=>lang('200007')];

        if (empty($this->smsConfig['sms_service_id']) || empty($this->smsConfig['sms_service_access']) || empty($this->smsConfig['sms_service_pass'])) {
            return ['Code' => '200009', 'Msg'=>lang('200009')];
        }

        //自行书写业务逻辑代码
        $ttime                  = time();
        //手机号校验
        $mobile                 = $parame['mobile'];

        $check                  = model('user_center')->isExistMobile($mobile,0);

        if ($check == -6) return ['Code' => '200001', 'Msg'=>lang('200001')];

        switch ($parame['type']) {
            case 1:
                if ($check == -7) return ['Code' => '200002', 'Msg'=>lang('200002')];
                break;
            case 2:
                if ($check == 0) return ['Code' => '200003', 'Msg'=>lang('200003')];break;
            case 3:
            case 4:
            case 5:
            if ($check == 0) return ['Code' => '200003', 'Msg'=>lang('200003')];break;
            default:
                return ['Code' => '200004', 'Msg'=>lang('200004')];
                break;
        }

        $validity   = 60*($sms_validity_time > 0 ? $sms_validity_time : 30);//30分钟
        $limitime   = $sms_interval_time > 0 ? $sms_interval_time : 120;//120秒
        $limitip    = $sms_limit_ip > 0 ? $sms_limit_ip : 5;//同一个IP最多可以发送5次

        //频率限制，120秒内不允许发送下一条
        $ctime      = $dbModel->get_create_time($mobile);

        if (!empty($ctime) && ($ctime+$limitime) > $ttime)
        return ['Code' => '200005', 'Msg'=>lang('200005')];

        //IP限制，同一个手机号，同个IP每天只能发送5次
        $ipcont         = $dbModel->get_ip_count($mobile);
        if ($ipcont >= $limitip)
        return ['Code' => '200006', 'Msg'=>lang('200006')];

        //生成短信内容
        $code       = randomString((($sms_code_num>=4 && $sms_code_num <= 6) ? $sms_code_num : 6),0);
        $conArr[1]  = '用户注册验证码为：'. $code;
        $conArr[2]  = '找回密码验证码为：'. $code;
        $conArr[3]  = '登录验证码为：'. $code;
        $conArr[4]  = '您的验证码为：'. $code;
        $conArr[5]  = '您的验证码为：'. $code;
        $content    = $conArr[$parame['type']];

        //根据不同短信服务商集成
        switch ($sms_service) {
            case 1:
                $res    = $this->sendCodeMovek($mobile,$content,$sms_sign);break;
            case 2:
                $res    = $this->sendCodeAliyun($mobile,$content,$sms_sign,$code);break;
            default:
                return ['Code' => '200008', 'Msg'=>lang('200008')];
        }

        if ($res[0]) {

            //入库短信信息
            $updata                 = [];
            $updata['mobile']       = $mobile;
            $updata['content']      = $content;
            $updata['checkcode']    = $code;
            $updata['sendtype']     = $parame['type'];
            $updata['create_time']  = $ttime;
            $updata['update_time']  = $ttime;
            $updata['status']       = 1;
            $updata['ip']           = get_client_ip();
            $updata['validity']     = $validity+$ttime;

            $dbModel->addData($updata);

            $Data                   = [];
            $Data['mobile']         = $mobile;
            $Data['type']           = $parame['type'];
            $Data['sendtime']       = $ttime;

            //返回数据
            return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['send_status'=>'ok']];
        }

        return ['Code' => '200009', 'Msg'=>lang('200009',[$res[1]])];
    }

    /*api:704cbfb35b0703f4566dbf3277a0eb63*/

    /*api:0f4a8eb21969e97fbc8f536e888c7ff4*/
    /**
     * * 验证码校验
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function checkCode($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //删除过期短信
        $dbModel->delValidityData();

        //自行书写业务逻辑代码
        $check_type             = isset($parame['check_type']) ? intval($parame['check_type']) : 0;
        switch ($check_type) {
            case 1: return $this->checkMobileCode($parame);break;
            case 2: return $this->checkEmailCode($parame);break;
            default: return ['Code' => '200010', 'Msg'=>lang('200010')];break;
        }

        return ['Code' => '200010', 'Msg'=>lang('200010')];
    }

    /*api:0f4a8eb21969e97fbc8f536e888c7ff4*/

    /*接口扩展*/

    private function checkMobileCode($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //手机号校验
        $check                  = model('user_center')->isExistMobile($parame['mobile'],0);

        if ($check == -6) return ['Code' => '200001', 'Msg'=>lang('200001')];

        switch ($parame['scene']) {
            case 1:
                if ($check == -7) return ['Code' => '200002', 'Msg'=>lang('200002')];
                break;
            case 2:
                if ($check == 0) return ['Code' => '200003', 'Msg'=>lang('200003')];
                break;
            case 3:
            case 4:
            case 5:break;
            default: return ['Code' => '200004', 'Msg'=>lang('200004')];
            break;
        }

        $map                = [];
        $map[]              = ['mobile','=',$parame['mobile']] ;
        $map[]              = ['validity','>=',time()] ;
        $map[]              = ['checkcode','=',$parame['sms_code']] ;
        $map[]              = ['sendtype','=',$parame['scene']];
        $map[]              = ['status','=',1] ;

        $id                 = $dbModel->where($map)->value('id');

        if (!empty($id) && $id>0){

            return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['smsid'=>$id]];
        }

        return ['Code' => '200011', 'Msg'=>lang('200011')];
    }

    private function checkEmailCode()
    {
        return ['Code' => '200010', 'Msg'=>lang('200010')];
    }

    private function sendCodeMovek($mobile='',$content='',$sign='')
    {
        $userid                 = $this->smsConfig['sms_service_id'];
        $account                = $this->smsConfig['sms_service_access'];
        $userpwd                = $this->smsConfig['sms_service_pass'];
        $urls                   = 'http://client.movek.net:8888/sms.aspx';
        $parame['action']       = 'send';
        $parame['userid']       = $userid;
        $parame['account']      = $account;
        $parame['password']     = md5($userpwd);
        $parame['mobile']       = $mobile;
        $parame['content']      = $sign.$content;
        $xml                    = CurlHttp($urls,$parame,'POST');

        libxml_disable_entity_loader(true);
        $xmlstring              = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val                    = json_decode(json_encode($xmlstring),true);

        return $val['returnstatus'] == 'Success' ? [true,'ok'] : [false,$val['message']];
    }

    private function sendCodeAliyun($mobile='',$content='',$sign='',$code=0)
    {
        $parame                 = [];
        $security               = false;

        $userid                 = $this->smsConfig['sms_service_id'];
        $accessKeyId            = $this->smsConfig['sms_service_access'];
        $accessKeySecret        = $this->smsConfig['sms_service_pass'];

        // fixme 必填: 短信接收号码
        $parame["PhoneNumbers"] = $mobile;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $parame["SignName"]         = $sign;
        $parame["TemplateCode"]     = $userid;
        $parame['TemplateParam']    = [
            'code'=>$code,
            'product'=>'您的验证码为：${code}，5分钟内有效，打死也不能告诉别人'
        ];
        $parame['OutId']            = "";
        $parame['SmsUpExtendCode']  = "";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($parame["TemplateParam"]) && is_array($parame["TemplateParam"])) {
            $parame["TemplateParam"] = json_encode($parame["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new \xnrcms\SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($parame,[
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ]),
            $security
        );

        $content        =  json_decode(json_encode( $content),true);
        return $content['Code'] == 'OK' ? [true,'ok'] : [false,$content['Message']];
    }
    private function delCode($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //删除已使用的验证码
        if (isset($parame['id']) && $parame['id'] > 0) {

            return $dbModel->delData($parame['id']);
        }

        return 0;
    }
}
