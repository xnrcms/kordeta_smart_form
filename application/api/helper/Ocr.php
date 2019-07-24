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

//定义百度接口配置
/*const APP_ID        = '16871646';
const API_KEY       = 'WqewUMrIHcvSUom68S8lGUj6';
const SECRET_KEY    = 'cSHH9G1end7QBCajWjc9h0AZ8ox61R85';*/

const APP_ID        = '16871373';
const API_KEY       = '5cTGgnCYWPsL59tnrsChl7yw';
const SECRET_KEY    = 'CeCBiA39qiOvKvFtQdqYr9526gso6clD';

class Ocr extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'empty';
	
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

    /*api:d5e9eb374c7788953b687ad6b5df57cb*/
    /**
     * * 图片识别接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function image($parame)
    {
        $words      = '';
        $client     = new \AipOcr(APP_ID, API_KEY, SECRET_KEY);
        
        //文件上传
        $uploads        = $this->uploadFile($parame);
        if (!(isset($uploads['Code']) && $uploads['Code'] == '200')) return $uploads;

        $filePath   = $uploads['Data'];;
        $image      = file_get_contents($filePath);

        // 如果有可选参数
        $options                    = [];
        $options["language_type"]   = "CHN_ENG";

        // 带参数调用通用文字识别, 图片参数为本地图片
        $res        = $client->basicGeneral($image, $options);

        unlink($filePath);

        if (isset($res['error_code']))
        {
            $msg    = isset($res['error_msg']) && !empty($res['error_msg']) ? $res['error_msg'] . "-" . $res['error_code'] : lang('message_unknown_error');
            return ['Code' => '203', 'Msg'=>$msg];
        }
        
        if (isset($res['words_result_num']) && $res['words_result_num'] > 0)
        {
            $words_result   =  isset($res['words_result']) && !empty($res['words_result']) ? $res['words_result'] : [];

            foreach ($words_result as $key => $value)
            {
                $words  .= $value['words'];
            }
        }

        return ['Code' => '200', 'Msg'=>lang('200'),'Data'=>['words'=>$words]];
    }

    /*api:d5e9eb374c7788953b687ad6b5df57cb*/

    /*接口扩展*/

    private function uploadFile($parame)
    {
        //获取有关图片上传的设置
        $config             = ['size'=> 10*1024*1024,'ext'=>'png,jpeg'] ;

        //获取表单上传的文件
        $files              = request()->file('fileName') ;
        $re                 = [];

        if(empty($files)) return ['Code'=>'203', 'Msg' => lang('notice_upload_file_empty')] ;

        $fileUploadRoot     = './uploads/ocr/';

        //上传文件验证
        $info               = $files->validate($config)->rule('md5')->move($fileUploadRoot) ;

        if($info === false){
            return ['Code' =>'203', 'Msg'=>lang('notice_upload_file_fail',[$files->getError()])] ;
        }else{
            $path                  = $fileUploadRoot . $info->getSaveName();
        }

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$path];
    }
}
