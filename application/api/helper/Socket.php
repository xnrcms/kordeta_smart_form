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
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        wr(['$this->clientId'=>$this->clientId]);
        //绑定UID
        //Gateway::bindUid($parame['client_id'],$parame['userid']);

        //自行书写业务逻辑代码
        wr("sssssssssssssss");
        //需要返回的数据体
        $Data                   = ['TEST'];

        return ['Code' => '200', 'Msg'=>lang('200'),'Data'=>$Data];
    }

    /*api:e9a7f5ae41b8a1ed58f8e7d69366f9c8*/

    /*接口扩展*/

    public function setClientId($client_id = 0)
    {
        $this->clientId     = $client_id;
    }
}
