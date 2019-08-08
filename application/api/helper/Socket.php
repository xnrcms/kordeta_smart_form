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
use GatewayWorker\Lib\Gateway;

class Socket extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'empty';
    private $clientId           = 0;
	
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
        if (!$this->checkData($this->postData)) return $this->getReturnData();
        
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
        return $this->getReturnData();
    }

    //支持内部调用
    public function isInside($parame,$aName)
    {
        return $this->$aName($parame);
    }

    /*api:e9a7f5ae41b8a1ed58f8e7d69366f9c8*/
    /**
     * * 手签-Socket通信接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function handSign($parame)
    {
        wr('ssssssss');
        //文件上传
        $uploads        = $this->uploadFile($parame);

        if (!(isset($uploads['Code']) && $uploads['Code'] == '200')) return $uploads;

        $socketData             = [
            'id'    => $uploads['Data']['id'],
            'url'   => get_cover($uploads['Data']['id'],'path'),
            'field' => $parame['fieldName']
        ];

        $data                   = [];
        $data['socketType']     = 'signature';
        $data['socketData']     = $socketData;

        Gateway::sendToUid($this->getUserId(), json_encode($data));

        return ['Code' => '200', 'Msg'=>lang('200')];
    }

    /*api:e9a7f5ae41b8a1ed58f8e7d69366f9c8*/

    /*api:bb11599b1bc689a4180ae58301262de2*/
    /**
     * * 建立Socket通信接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function index($parame)
    {
        //绑定UID
        Gateway::bindUid($this->clientId,$this->getUserId());

        return ['Code' => '200', 'Msg'=>lang('200')];
    }

    /*api:bb11599b1bc689a4180ae58301262de2*/

    /*接口扩展*/

    public function setClientId($client_id = 0)
    {
        $this->clientId     = $client_id;
    }

    private function uploadFile($parame)
    {
        //获取有关图片上传的设置
        $config             = ['size'=> 10*1024*1024,'ext'=>''] ;
        $tags               = 'signature';

        //获取表单上传的文件
        $files              = request()->file('fileName');
        $re                 = [];

        if(empty($files)) return ['Code'=>'203', 'Msg' => lang('notice_upload_file_empty')] ;

        $fileUploadRoot     = './uploads/' . $tags . '/';

        //上传文件验证
        $info               = $files->validate($config)->rule('md5')->move($fileUploadRoot) ;
        
        if($info === false)
        {
            return ['Code' =>'203','Msg'=>lang('notice_upload_file_fail',[$files->getError()])] ;
        }else{
            $path                  = $fileUploadRoot . $info->getSaveName();
            rename($path, $path.'png');

            $path                  = $path.'png';
            $url                   = $path;
        }

        $finfo                      = $info->getInfo();
        $thisTime                   = time();
        $umark                      = md5($thisTime . randomString(10,7));

        $saveData          = [
                'path'         => $path,
                'imgurl'       => $url,
                'tags'         => $tags,
                'img_type'     => 1,
                'infos'        => json_encode($finfo),
                'create_time'  => $thisTime,
                'umark'        => $umark,
        ];

        $data       = model('picture')->addData($saveData);
        $data       = !empty($data) ? $data->toArray() : [];

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$data];
    }
}
