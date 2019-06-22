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
use Payment\Client\Charge;
use Payment\Common\PayException;

class Pay extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'order_recharge';
	
	public function __construct($parame=[],$className='',$methodName='',$modelName='')
    {
        parent::__construct($parame,$className,$methodName,$modelName);
        $this->apidoc           = request()->param('apidoc',0);
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

    /*api:3134925f23ab1238e4a57211b7f16e53*/
    /**
     * * 账户充值
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function recharge($parame)
    {
        try{

            $paytype        = $parame['pay_type'] ;
            $banktag        = '';

            $pay_type       = config('system_config.paytype');
            $pay_type       = !empty($pay_type) ? explode(',',$pay_type) : [];

            if(!in_array($paytype,$pay_type))
            return ['Code' => '200001', 'Msg'=>lang('200001')];

            //支付方式为银联支付时需要校验银行是否存在
            /*if ($paytype == 3) {
                $banktag    = isset($parame['banktag']) ? trim($parame['banktag']) : '';
                $bank       = $this->bankInfo($banktag);
                if (empty($bank)) return ['Code' => '200003', 'Msg'=>lang('200003')];
            }*/

            //订单编号
            $order_sn               = date('ymdHis',time()).randomString(6,0) ;

            $body                   = '充值订单' ;
            $attach                 = '充值订单' ;
            $fee                    = floatval($parame['money']);
            $order_type             = 1 ;
            $uid                    = $parame['uid'];

            //$fee = 0.02;

            $extend                  = [] ;
            $extend['order_sn']     = $order_sn;
            $extend['money']        = $fee;
            $extend['order_type']   = $order_type;
            $extend['pay_type']     = $paytype;
            $extend['uid']          = $parame['uid'] ;

            $payInfo   = $this->getPayInfo($order_sn,$body,$attach,$fee,$paytype,$extend,$uid,$banktag,$parame['terminal']);
            if($payInfo['Code'] !== '200') return ['Code' => $payInfo['Code'], 'Msg'=>$payInfo['Msg']];

            //事先写入订单数据 未支付状态
            $orderData                  = [] ;
            $orderData['uid']           = $uid ;
            $orderData['order_sn']      = $order_sn;
            $orderData['out_trade_no']  = $order_sn ;
            $orderData['money']         = $fee ;
            $orderData['price']         = $fee ;
            $orderData['pay_type']      = $paytype;
            $orderData['status']        = 1 ;
            $orderData['create_time']   = time() ;
            $orderData['update_time']   = time() ;

            model('order_recharge') ->addData($orderData) ;

            return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$payInfo['Data']];
        }catch (\Exception $exception){
            return ['Code'=>(string)$exception->getCode(),'Msg'=>$exception->getCode()==0?$exception->getMessage().$exception->getLine():$exception->getMessage()] ;
        }
    }

    /*api:3134925f23ab1238e4a57211b7f16e53*/

    /*api:97a96cfbdb3effe2be005c5df98736af*/
    /**
     * * 提现代付
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function substitute($parame)
    {
        $dbModel                    = model('bank_cash');
        //获取用户提现信息
        $id                         = isset($parame['id']) ? intval($parame['id']) : '';
        $cash_info                  = $dbModel->getOneByid($id);
        $cash_info                  = !empty($cash_info) ? $cash_info->toArray() : [];

        $accNo                      = '';
        $accName                    = '';
        $money                      = 0;
        $cash_uid                   = 0;

        if (!empty($cash_info)) {
            $accName                = isset($cash_info['real_name']) ? $cash_info['real_name'] : '';
            $accNo                  = isset($cash_info['bank_num']) ? $cash_info['bank_num'] : '';
            $money                  = isset($cash_info['money']) ? $cash_info['money']*1 : 0;
            $cash_uid               = isset($cash_info['uid']) ? $cash_info['uid']*1 : 0;
        }

        if (empty($accName) || $cash_uid <= 0) return ['Code' => '200005', 'Msg'=>lang('200005')];
        if (empty($accNo)) return ['Code' => '200006', 'Msg'=>lang('200006')];
        if ($money <= 0) return ['Code' => '200007', 'Msg'=>lang('200007')];

        $config                     = config('pay.sandpay');
        $time                       = time();
        $mid                        = $config['mid'];
        $currencyCode               = 156;
        $order_sn                   = date('YmdHis',$time) . randomString(6);

        $data                       = [
            'transCode' => 'RTPM', // 实时代付
            'merId'     => $mid, // 此处更换商户号
            'url'       => 'https://caspay.sandpay.com.cn/agent-main/openapi/agentpay',
            'pt'        => [
                'version'       => '01',
                'productId'     => '00000004',
                'tranTime'      => date('YmdHis', $time),
                'orderCode'     => $order_sn,
                'tranAmt'       => substr('000000000000' . ($money*100), -12),
                'currencyCode'  => '156',
                'accAttr'       => '0',
                'accType'       => '4',
                'accNo'         => $accNo,
                'accName'       => $accName,
                'remark'        => '用户ID：【'.$cash_uid.'】余额提现',
            ]
        ];

        // step1: 拼接报文及配置
        $transCode          = $data['transCode']; // 交易码
        $accessType         = '0'; // 接入类型 0-商户接入，默认；1-平台接入
        $merId              = $data['merId']; // 此处更换商户号
        $path               = $data['url']; // 服务地址
        $pt                 = $data['pt']; // 报文

        // 获取公私钥匙
        $priKey             = pd_loadPk12Cert(\Env::get('APP_PATH').'cert/privte.pfx', $config['CretPwd']);
        $pubKey             = pd_loadX509Cert(\Env::get('APP_PATH').'cert/public.cer');

        // step2: 生成AESKey并使用公钥加密
        $AESKey             = pd_aes_generate(16);
        $encryptKey         = pd_RSAEncryptByPub($AESKey, $pubKey);

        // step3: 使用AESKey加密报文
        $encryptData        = pd_AESEncrypt($pt, $AESKey);

        // step4: 使用私钥签名报文
        $sign               = pd_sign($pt, $priKey);

        // step5: 拼接post数据
        $post = [
            'transCode'     => $transCode,
            'accessType'    => $accessType,
            'merId'         => $merId,
            'encryptKey'    => $encryptKey,
            'encryptData'   => $encryptData,
            'sign'          => $sign
        ];

        // step6: post请求
        $result             = pd_http_post_json($path, $post);

        parse_str($result, $arr);

        try {
            // step7: 使用私钥解密AESKey
            $decryptAESKey      = pd_RSADecryptByPri($arr['encryptKey'], $priKey);

            // step8: 使用解密后的AESKey解密报文
            $decryptPlainText   = pd_AESDecrypt($arr['encryptData'], $decryptAESKey);

            // step9: 使用公钥验签报文
            if (pd_verify($decryptPlainText, $arr['sign'], $pubKey)) {
                $result         = json_decode($decryptPlainText,true);
                if (isset($result['respCode']) && $result['respCode'] == '0000') {

                    $dbModel->updateById($id,['status'=>3,'order_sn'=>$result['orderCode']]);
                    return ['Code' => '200', 'Msg'=>lang('text_req_success')];
                }else{
                    $msg        = (isset($result['respDesc'])) ? $result['respDesc'] : lang('200009');
                    return ['Code' => '200007', 'Msg'=>$msg];
                }
            }else{
                return ['Code' => '200007', 'Msg'=>lang('200008')];
            }
        } catch (\Exception $e) {
            return ['Code' => '200007', 'Msg'=>$e->getMessage()];
        }
    }

    /*api:97a96cfbdb3effe2be005c5df98736af*/

    /*接口扩展*/

    private function getPayInfo($order_sn, $body, $attach, $fee, $paytype,$extend,$uid,$banktag='',$terminal = 0)
    {
        switch (intval($paytype)){
            case 1 :
                try{
                    //获取配置
                    $config = config('pay.alipay');

                    if (empty($config)) return ['Code' => '200002', 'Msg'=>lang('200002')];

                    $options = [
                        'order_no'          => $order_sn, // 订单号
                        'amount'            => $fee , // 订单金额，**单位：元**
                        'subject'           => $attach, // 订单描述
                        'body'              => $body, // 订单描述
                        'spbill_create_ip'  => get_client_ip() , // 支付人的 IP
                        'return_param'      => urlsafe_b64encode(json_encode($extend)),     //不变的返回参数
                        'goods_type'        => 1 ,
                    ];

                    $payInfo = Charge::run('ali_app',$config,$options);

                    return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['alipay'=>$payInfo,'wxpay'=>[]]];

                }catch (PayException $exception){
                    return ['Code'=>'10000','Msg'=>$exception->errorMessage()] ;
                }
                break ;
            case 2 :
                try{
                    //获取配置
                    $config = config('pay.wechat');

                    if (empty($config)) return ['Code' => '200002', 'Msg'=>lang('200002')];

                    $options = [
                        'order_no'          => $order_sn, // 订单号
                        'amount'            => $fee , // 订单金额，**单位：元**
                        'subject'           => $attach, // 订单描述
                        'body'              => $body, // 订单描述
                        'client_ip'         => get_client_ip() , // 支付人的 IP
                        'timeout_express' => time() + 600,// 表示必须 600s 内付款
                        'return_param'      => urlsafe_b64encode(json_encode($extend)),//不变的返回参数
                        'goods_type'        => 1,
                    ];

                    $payInfo = Charge::run('wx_app',$config,$options);
                    $payInfo = !empty($payInfo) ? json_encode($payInfo) : '';
                    return ['Code' => '200','Msg'=>lang('text_req_success'),'Data'=>['alipay'=>"",'wxpay'=>$payInfo]];

                }catch (PayException $exception){
                    return ['Code'=>'10000','Msg'=>$exception->errorMessage()] ;
                }
                break ;
            case 3:
                try{
                    $compkey                = "0401090933523utT0MeA";   //商户密钥
                    $p1_yingyongnum         = "01018111680801";         //商户应用号
                    $p2_ordernumber         = $order_sn;                //商户订单号
                    $p3_money               = $fee;                     //商户订单金额，保留两位小数
                    $p6_ordertime           = date("YmdHis", time());   //商户订单时间
                    $p7_productcode         = "ZFB";                    //产品支付类型编码
                    $presign                = $p1_yingyongnum."&".$p2_ordernumber."&".$p3_money."&".$p6_ordertime."&".$p7_productcode."&".$compkey;
                    $p8_sign                = md5($presign);                //订单签名

                    $p9_signtype            = "";                       //签名方式
                    $p10_bank_card_code     = "";                       //银行卡或卡类编码

                    $types                  = [2=>2,3=>3];
                    $p25_terminal           = isset($types[$terminal]) ? intval($types[$terminal]) : 0; //商户终端设备值
 
                    // 终端设备值1 pc 2 ios  3 安卓
                    $p26_ext1              = "1.1";                     //商户标识

                    $url        = 'http://toamlxm.sunlin1.com/jh-web-order/order/receiveOrder';
                    $html       = '<!doctype html><html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8"><meta charset="utf-8"></head><body onload="submitForm();">';
                    $html       .= "<form id='yeepay' action='".$url."' method='post' >";
                    $html       .= "<input type='hidden' name='p1_yingyongnum' value='".$p1_yingyongnum."'>";
                    $html       .= "<input type='hidden' name='p2_ordernumber'          value='".$p2_ordernumber."'>";
                    $html       .= "<input type='hidden' name='p3_money' value='".$p3_money."'>";
                    $html       .= "<input type='hidden' name='p6_ordertime' value='".$p6_ordertime."'>";
                    $html       .= "<input type='hidden' name='p7_productcode' value='".$p7_productcode."'>";
                    $html       .= "<input type='hidden' name='p8_sign' value='".$p8_sign."'>";
                    $html       .= "<input type='hidden' name='p9_signtype' value='".$p9_signtype."'>";
                    $html       .= "<input type='hidden' name='p10_bank_card_code' value='".$p10_bank_card_code."'>";
                    $html       .= "<input type='hidden' name='p25_terminal' value='".$p25_terminal."'>";
                    $html       .= "<input type='hidden' name='p26_ext1' value='".$p26_ext1."'>";
                    $html       .= "</form>";
                    $html       .= '<script type="text/javascript">function submitForm() { document.getElementById("yeepay").submit();}</script></body></html>';
                    return ['Code' => '200','Msg'=>lang('text_req_success'),'Data'=>['alipay'=>$html,'wxpay'=>[]]];
                    break ;
                }catch (PayException $exception){
                    return ['Code'=>'10000','Msg'=>$exception->errorMessage()] ;
                }
                break ;
            case 5://杉德快捷支付
                //获取配置
                $config                     = config('pay.sandpay');
                $time                       = time();

                $mid                        = $config['mid'];
                $currencyCode               = 156;
                $order_sn                   = $order_sn;
                $money                      = substr('000000000000' . ($fee*100), -12);
                $subject                    = '余额充值';
                $body                       = '余额充值';
                $frontUrl                   = request()->domain() . '/home/notify/paySuccess';
                $clearCycle                 = '0';
                $notifyUrl                  = $config['ReturnURL'];
                $data = [
                    'head' => [
                        'version'           => '1.0',
                        'method'            => 'sandPay.fastPay.quickPay.index',
                        'productId'         => '00000016',
                        'accessType'        => '1',
                        'mid'               => $mid,
                        'channelType'       => '07',
                        'reqTime'           => date('YmdHis', $time)
                    ],
                    'body' => [
                        'userId'            => $uid,
                        'orderCode'         => $order_sn,
                        'orderTime'         => date('YmdHis', $time),
                        'totalAmount'       => $money,
                        'subject'           => $subject,
                        'body'              => $body,
                        'currencyCode'      => $currencyCode,
                        'notifyUrl'         => $notifyUrl,
                        'frontUrl'          => $frontUrl,
                        'clearCycle'        => $clearCycle,
                        'extend'            => ''
                    ]
                ];

                //私钥签名
                $pri_path   = \Env::get('APP_PATH').'cert/privte.pfx';
                $prikey     = pd_loadPk12Cert($pri_path, $config['CretPwd']);
                $sign       = pd_sign($data, $prikey);

                $charset    = 'utf-8';
                $signType   = '01';
                $data       = json_encode($data);
                $sign       = urlencode($sign);

                //拼接post数据
                /*$post     = ['charset'=>$charset,'signType'=>$signType,'data'=>$data,'sign' => $sign];*/

                //组装form表单
                $url        = 'https://cashier.sandpay.com.cn/fastPay/quickPay/index';
                
                $html       = '<!doctype html><html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8"><meta charset="utf-8"></head><body onload="submitForm();">';

                $html       .= '<form id="sandpay" action="'.$url.'" method="post" hidden="hidden"><textarea name="charset">'.$charset.'</textarea><textarea name="signType">'.$signType.'</textarea><textarea name="data">'.$data.'</textarea><textarea name="sign">'.$sign.'</textarea></form>';
                $html       .= '<script type="text/javascript">function submitForm() { document.getElementById("sandpay").submit();}</script></body></html>';
                //unlink('./pay.html');
                //file_put_contents('./pay.html',$html,FILE_APPEND);
                return ['Code' => '200','Msg'=>lang('text_req_success'),'Data'=>['alipay'=>$html,'wxpay'=>[]]];
                break;
            default :
                return ['Code' => '200001', 'Msg'=>lang('200001')];break ;
        }
    }

    private function bankInfo($tag='')
    {
        $back               = [];
        $bank['ICBC']       = '工商银行';
        $bank['ABC']        = '农业银行';
        $bank['BOCSH']      = '中国银行';
        $bank['CCB']        = '建设银行';
        $bank['CMB']        = '招商银行';
        $bank['SPDB']       = '上海浦东发展银行';
        $bank['GDB']        = '广发银行';
        $bank['BOCOM']      = '交通银行';
        $bank['PSBC']       = '邮政储蓄银行';
        $bank['CNCB']       = '中信银行';
        $bank['CMBC']       = '民生银行';
        $bank['CEB']        = '光大银行';
        $bank['HXB']        = '华夏银行';
        $bank['CIB']        = '兴业银行';
        $bank['PAB']        = '平安银行';
        $bank['BOS']        = '上海银行';
        $bank['BCCB']       = '北京银行';
        $bank['SRCB']       = '上海农村商业银行';
        $bank['BRCB']       = '北京农村商业银行';

        return isset($bank[$tag]) ? $bank[$tag] : '';
    }
}
