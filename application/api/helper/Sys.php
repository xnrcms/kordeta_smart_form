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

class Sys extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'devmenu';
	
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

    /*api:50832e1dd757d4c7a43fbed57ee438af*/
    /**
     * * 清理缓存
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function clearCache($parame)
    {
        \Cache::clear();
        delFile(\Env::get('RUNTIME_PATH'),true);
        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['res'=>'ok']];
    }

    /*api:50832e1dd757d4c7a43fbed57ee438af*/

    /*api:32f1425373f20c820bf8c97645f5d42e*/
    /**
     * * 系统配置接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function config($parame)
    {
        $config                         = [];
        $config['ios_version']          = '1.0.0';
        $config['android_version']      = '1.0.0';
        $config['sys_notice']           = 'xxxxxxxxxx';

        $ios_pay                        = config('system_config.ios_pay');

        $config['show_pay']             = $ios_pay == 1 ? 0 : 1;
        $config['kf_info_one']          = config('system_config.kf_info_one');
        $config['kf_info_two']          = config('system_config.kf_info_two');
        $config['share_title']          = config('system_config.share_title');
        $config['share_desc']           = config('system_config.share_desc');

        $paytype                        = config('system_config.paytype');
        $paytype                        = !empty($paytype) ? (string)$paytype : '';
        $config['pay_type']             = $paytype;

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$config];
    }

    /*api:32f1425373f20c820bf8c97645f5d42e*/

    /*api:edc438abfae19f530dedb76108d9d370*/
    /**
     * * 通用字段校验是否存在接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function checkField($parame)
    {
        $table             = [
            '',
            'devform2'
        ];

        $dataTag                = isset($parame['dataTag']) ? $parame['dataTag'] : '';
        $dataId                 = isset($parame['dataId']) ? $parame['dataId'] : '';
        $fieldName              = isset($parame['fieldName']) ? $parame['fieldName'] : '';
        $fieldValue             = isset($parame['fieldValue']) ? $parame['fieldValue'] : '';
        $tableName              = isset($table[$dataTag]) ? $table[$dataTag] : '';

        if (empty($tableName))
        return ['Code' => '203', 'Msg'=>lang('notice_tag_not_exists')];


        //主表数据库模型
        $dbModel                = model($tableName);

        //自行书写业务逻辑代码

        $isOk   = (int)$dbModel->checkValue($fieldValue,$dataId,$fieldName);

        return ['Code' => '200', 'Msg'=>lang('200'),'Data'=>$isOk];
    }

    /*api:edc438abfae19f530dedb76108d9d370*/

    /*接口扩展*/
}
