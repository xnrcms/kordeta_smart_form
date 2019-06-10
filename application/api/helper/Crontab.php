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
use Payment\Client\Notify;
use Payment\Common\PayException;
use Payment\Notify\PayNotifyInterface;

class Crontab extends Base
{
    private $dataValidate       = null;
    private $mainTable          = 'ad';
    
    public function __construct($parame=[],$className='',$methodName='',$modelName='')
    {
        parent::__construct($parame,$className,$methodName,$modelName);
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
    
    private function makelottery()
    {
        $lottery        = new \app\api\lottery\Lottery(0);
        $lottery->updateLottery();
        return;
    }

    /**
     * 接口列表数据
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function crontab($parame)
    {
        wr("",'info.txt',false);
        wr("\n\n...........开始执行[".date("Y-m-d H:i:s")."]...........\n\n");

        //时时彩
        $this->ffsscApiData();
        //$this->sfsscApiData();
        $this->inffc5ApiData();
        $this->cqsscApiData();
        $this->xjsscApiData();
        $this->tjsscApiData();
        $this->hljsscApiData();

        $this->pk10ApiData();
        $this->hk6ApiData();

        //快3
        $this->ahk3ApiData();
        $this->jlk3ApiData();
        $this->gxk3ApiData();
        $this->jsk3ApiData();
        $this->hubk3ApiData();

        //11选5
        $this->sd11x5ApiData();
        $this->gd11x5ApiData();
        $this->sh11x5ApiData();
        $this->js11x5ApiData();
        $this->hub11x5ApiData();
        $this->gx11x5ApiData();

        //PC蛋蛋
        $this->bjkl8ApiData();

        //分分时时彩彩和三分时时彩
        /*$this->ssc1f();
        $this->ssc3f();*/

        //开奖
        $this->openPrize();

        wr("\n\n...........结束执行[".date("Y-m-d H:i:s")."]...........\n\n");
        return;
    }
    //分分时时彩接口数据采集
    private function ffsscApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(89);
        $lottery->updateData();
        return true;
    }
    //3分时时彩接口数据采集
    /*private function sfsscApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(90);
        $lottery->updateData();
        return true;
    }*/
    //印尼5分时时彩接口数据采集
    private function inffc5ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(90);
        $lottery->updateData();
        return true;
    }
    //重庆时时彩接口数据采集
    private function cqsscApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(92);
        $lottery->updateData();
        return true;
    }

    //新疆时时彩接口数据采集
    private function xjsscApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(93);
        $lottery->updateData();
        return true;
    }

    //黑龙江时时彩接口数据采集
    private function hljsscApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(94);
        $lottery->updateData();
        return true;
    }

    //天津时时彩接口数据采集
    private function tjsscApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(95);
        $lottery->updateData();
        return true;
    }

    //北京PK10接口数据采集
    private function pk10ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(97);
        $lottery->updateData();
        return true;
    }

    //香港六合彩 接口数据采集
    private function hk6ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(100);
        $lottery->updateData();
        return true;
    }

    //快3 - 安徽
    private function ahk3ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(103);
        $lottery->updateData();
        return true;
    }
    //快3 - 吉林
    private function jlk3ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(104);
        $lottery->updateData();
        return true;
    }
    //快3 - 广西
    private function gxk3ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(105);
        $lottery->updateData();
        return true;
    }
    //快3 - 江苏
    private function jsk3ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(106);
        $lottery->updateData();
        return true;
    }
    //快3 - 湖北
    private function hubk3ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(107);
        $lottery->updateData();
        return true;
    }

    //11选5 - 山东
    private function sd11x5ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(109);
        $lottery->updateData();
        return true;
    }

    //11选5 - 广东
    private function gd11x5ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(110);
        $lottery->updateData();
        return true;
    }

    //11选5 - 上海
    private function sh11x5ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(111);
        $lottery->updateData();
        return true;
    }

    //11选5 - 江苏
    private function js11x5ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(112);
        $lottery->updateData();
        return true;
    }

    //11选5 - 湖北
    private function hub11x5ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(113);
        $lottery->updateData();
        return true;
    }

    //11选5 - 广西
    private function gx11x5ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(114);
        $lottery->updateData();
        return true;
    }

    //PC蛋蛋 - 北京28
    private function bjkl8ApiData()
    {
        $lottery        = new \app\api\lottery\Lottery(116);
        $lottery->updateData();
        return true;
    }

    public function openPrize()
    {
        $lottery        = new \app\api\lottery\Lottery(0);
        $lottery->openPrize();
        return true;
    }

    /**
     * @param array $parame
     * @return array
     */
    private function paySuccess($parame=[])
    {
        $parame             = is_array($parame) ? $parame : json_decode($parame,true) ;
        $payType            = intval($parame['pay_type']) ;
        $out_trade_no       = '';
        $money              = 0;

        switch ($payType){
            case 1:
                //获取配置
                $config = config('pay.alipay');
                try {

                    $ret = Notify::run('ali_charge', $config, new ThirdPayNoticeHelper());// 处理回调，内部进行了签名检查

                    $data = Notify::getNotifyData('ali_charge', $config);

                    $passback_params            = $data['passback_params'] ;
                    $passback_params            = json_decode(urlsafe_b64decode($passback_params),true) ;

                    $out_trade_no               = $data['out_trade_no'] ;
                    $money                      = $data['total_amount'] ;

                    print_r($ret);
                } catch (PayException $e) {
                    dblog($e) ; exit;
                }

                break;
            case 2:
                try {
                    //获取配置
                    $config         = config('pay.wechat');
                    $ret            = Notify::run('wx_charge', $config, new ThirdPayNoticeHelper());// 处理回调，内部进行了签名检查

                    $data           = Notify::getNotifyData('wx_charge', $config);

                    $passback_params            = $data['attach'] ;
                    $passback_params            = json_decode(urlsafe_b64decode($passback_params),true) ;

                    $out_trade_no               = $data['out_trade_no'] ;
                    $money                      = $data['total_fee']/100 ;

                    print_r($ret) ;
                } catch (PayException $e) {
                    dblog($e) ;
                    exit;
                }

                break ;
            case 3:
                try {
                    $compkey                = "0401090933523utT0MeA";        
                    $return                 = request()->param();
                    
                    $p1_yingyongnum         = $return['p1_yingyongnum'];               
                    $p2_ordernumber         = $return['p2_ordernumber'];
                    $p3_money               = $return['p3_money'];
                    $p4_zfstate             = $return['p4_zfstate'];
                    $p5_orderid             = $return['p5_orderid'];
                    $p6_productcode         = $return['p6_productcode'];
                    $p7_bank_card_code      = $return['p7_bank_card_code'];
                    $p8_charset             = $return['p8_charset'];
                    $p9_signtype            = $return['p9_signtype'];
                    $p10_sign               = $return['p10_sign'];
                    $p11_pdesc              = $return['p11_pdesc'];
                    $p13_zfmoney            = $return['p13_zfmoney'];
                
                    $presign                = $p1_yingyongnum."&".$p2_ordernumber."&".$p3_money."&".$p4_zfstate."&".$p5_orderid."&".$p6_productcode."&".$p7_bank_card_code."&".$p8_charset."&".$p9_signtype."&".$p11_pdesc."&".$p13_zfmoney."&".$compkey;
                    // echo $presign."<br/>";
                    $sign                       =strtoupper(md5($presign));
                    wr([$sign,$return],'paylog.txt');
                    if ($sign == $return['p10_sign'] && $return['p4_zfstate'] == "1")
                    {
                        
                        $payType       = isset($return['pay_type']) ? $return['pay_type'] : 0;
                        $money         = isset($return['p3_money']) ? $return['p3_money'] : 0;
                        $out_trade_no  = $p2_ordernumber;

                        //业务处理
                       echo "success";

                    }else{
                          exit('fail');
                    }
                } catch (PayException $e) {
                    dblog($e) ;
                    exit;
                }
                break ;
            case 100://代付回调
                $return_sign       = isset($parame['sign']) ? $parame['sign'] : '';
                $return_data       = isset($parame['data']) ? stripslashes($parame['data']) : '';

                if (empty($return_sign) || empty($return_data)) {
                    dblog('pay fail:return_data or return_sign empty');exit;
                }

                $config         = config('pay.sandpay');

                //公钥
                $pub_path       = \Env::get('APP_PATH').'cert/public.cer';
                $pubkey         = pd_loadX509Cert($pub_path);

                if (pd_verify($return_data, $return_sign, $pubkey)) {
                    $return_data    = json_decode($return_data,true);
                    
                    if (isset($return_data['head']) && !empty($return_data['head'])) {
                        if ($return_data['head']['respCode'] === '000000') {
                            $out_trade_no       = $return_data['body']['orderCode'];
                            $money              = intval($return_data['body']['totalAmount']) / 100;
                        }else{
                            dblog('pay fail');exit;
                        }
                    }else{
                        dblog('pay fail:return_data not head data');exit;
                    }
                } else {
                    dblog('pay fail:return_sign is error');exit;
                }

                break;
            default : break ;
        }

        return $this->updateOrder($parame,$out_trade_no,$money,$payType) ;
    }

    /**
     * @param $passback_params
     * @param $order_type
     * @param $out_trade_no
     * @param $money
     * @param $payType
     * @return array
     * @throws
     */
    private function updateOrder($passback_params, $out_trade_no, $money, $payType)
    {
        return $this->updateRechargeOrder($passback_params,$out_trade_no,$money,$payType) ;
    }

    private function updateRechargeOrder($passback_params, $out_trade_no, $money, $payType)
    {
        try{
            $map                        = [];
            $map['order_sn']            = $out_trade_no;
            //$map['uid']                 = $passback_params['uid'];
            $find_status                = model('order_recharge')->where($map)->value('status');

            $cacheKey                   = 'updateRechargeOrder==' . $out_trade_no;

            if (cache($cacheKey) == 'success') return 1;

            if($find_status != 2){
                cache($cacheKey,'success');

                //准备用户订单购买数据
                $uid                    = model('order_recharge')->where($map)->value('uid');
                model('order_recharge')->where($map)->update(['status'=>2]);
                model('user_account_log')->addAccountLog($uid,$money,'余额充值',1,3);

                //用户收入增加
                $res = model('user_detail')->where('uid',$uid)->setInc('account',$money) ;
                model('user_detail')->delDetailDataCacheByUid($uid);
            }
            
            return 1;
        }catch (\Exception $exception){
            return ['Code'=>(string)$exception->getCode(),'Msg'=>$exception->getCode()==0?$exception->getMessage().$exception->getLine():$exception->getMessage()] ;
        }
    }

    public function ssc1f()
    {
        $cacheKey       = 'ssc1f_key';
        $addtime        = cache($cacheKey);
        if (!empty($addtime) && $addtime > time()) return false;

        $dbModel        = model('lottery_ssc1');
        $info           = $dbModel->getInfoByLimitTime();
        
        $ff1            = strtotime(date('Ymd 00:00:00'));
        $ff2            = strtotime(date('Ymd H:i:00'));

        //是否被1分钟整除
        if (($ff2-$ff1)%60 != 0) return false;

        $ff             = ($ff2-$ff1)/60;
        if ($ff >= 0 && $ff < 10) {
            $ff             = '000' . $ff;
        }elseif ($ff >= 10 && $ff < 100) {
            $ff             = '00' . $ff;
        }elseif ($ff >= 100 && $ff < 1000) {
            $ff             = '0' . $ff;
        }

        $expect         = date('Ymd').$ff;

        $code           = randomString(5);
        $temp           = [];
        for ($i=0; $i < 5; $i++) { 
            $temp[]     = substr($code,$i,1);
        }

        if(empty($info)){
            $updata['expect']       = $expect;
            $updata['opencode']     = implode(',',$temp);
            $updata['opentime']     = date('Y-m-d H:i:s',$ff2);
            $updata['opentimestamp']= $ff2;
            
            $dbModel->addData($updata);
        }else{
            $addtimestamp          = $info['opentimestamp']+60;
            if ($addtimestamp > time()) {
                cache($cacheKey,$addtimestamp);
                return false;
            }else{

                $updata['expect']       = $expect;
                $updata['opencode']     = implode(',',$temp);
                $updata['opentime']     = date('Y-m-d H:i:s',$ff2);
                $updata['opentimestamp']= $ff2;
                $dbModel->addData($updata);
            }
        }

        //删除无用数据
        $info       = $dbModel->getInfoByLimitTime();
        $id         = isset($info['id']) ? $info['id']-11 : 0;
        $dbModel->delInfoById($id);

        $addtimestamp       = $ff2+60;
        cache($cacheKey,$addtimestamp);
    }

    public function ssc3f()
    {
        $cacheKey       = 'ssc3f_key';
        $addtime        = cache($cacheKey);
        if (!empty($addtime) && $addtime > time()) return false;

        $dbModel        = model('lottery_ssc3');
        $info           = $dbModel->getInfoByLimitTime();
        
        $ff1            = strtotime(date('Ymd 00:00:00'));
        $ff2            = strtotime(date('Ymd H:i:00'));

        //是否被3分钟整除
        if (($ff2-$ff1)%180 != 0) return false;

        $ff             = ($ff2-$ff1)/180;
        $number         = (substr(date('Ymd'),5).'000')*1+$ff;
        $expect         = substr(date('Ymd'),0,5).$number;
        $code           = randomString(5);
        $temp           = [];
        for ($i=0; $i < 5; $i++) { 
            $temp[]     = substr($code,$i,1);
        }

        if(empty($info)){
            $updata['expect']       = $expect;
            $updata['opencode']     = implode(',',$temp);
            $updata['opentime']     = date('Y-m-d H:i:s',$ff2);
            $updata['opentimestamp']= $ff2;
            $dbModel->addData($updata);
        }else{
            $addtimestamp          = $info['opentimestamp']+180;
            if ($addtimestamp > time()) {
                cache($cacheKey,$addtimestamp);
                return false;
            }else{

                $updata['expect']       = $expect;
                $updata['opencode']     = implode(',',$temp);
                $updata['opentime']     = date('Y-m-d H:i:s',$ff2);
                $updata['opentimestamp']= $ff2;
                $dbModel->addData($updata);
            }
        }

        //删除无用数据
        $info       = $dbModel->getInfoByLimitTime();
        $id         = isset($info['id']) ? $info['id']-10 : 0;
        $dbModel->delInfoById($id);

        $addtimestamp       = $ff2+180;
        cache($cacheKey,$addtimestamp);
    }
    /*接口扩展*/
}
