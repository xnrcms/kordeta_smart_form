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
namespace app\admin\helper;

use app\common\helper\Base;
use think\facade\Lang;

class DevapiModule extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'devapi_module';
	
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

    /**
     * 接口列表数据
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function listData($parame)
    {
        //主表数据库模型
		$dbModel					= model($this->mainTable);

		/*定义数据模型参数*/
		//主表名称，可以为空，默认当前模型名称
		$modelParame['MainTab']		= $this->mainTable;

		//主表名称，可以为空，默认为main
		$modelParame['MainAlias']	= 'main';

		//主表待查询字段，可以为空，默认全字段
		$modelParame['MainField']	= [];

		//定义关联查询表信息，默认是空数组，为空时为单表查询,格式必须为一下格式
		//Rtype :`INNER`、`LEFT`、`RIGHT`、`FULL`，不区分大小写，默认为`INNER`。
		$RelationTab				= [];
		//$RelationTab['member']		= array('Ralias'=>'me','Ron'=>'me.uid=main.uid','Rtype'=>'LEFT','Rfield'=>array('nickname'));

		$modelParame['RelationTab']	= $RelationTab;

        //接口数据
        $modelParame['apiParame']   = $parame;

		//检索条件 需要对应的模型里面定义查询条件 格式为formatWhere...
		$modelParame['whereFun']	= 'formatWhereDefault';

		//排序定义
		$modelParame['order']		= 'main.sort desc,main.id asc';		
		
		//数据分页步长定义
		$modelParame['limit']		= $this->apidoc == 2 ? 1 : 1000;

		//数据分页页数定义
		$modelParame['page']		= (isset($parame['page']) && $parame['page'] > 0) ? $parame['page'] : 1;

		//数据缓存是时间，默认0 不缓存 ,单位秒
		$modelParame['cacheTime']	= 0;

		//列表数据
		$lists 						= $dbModel->getPageList($modelParame);

		//数据格式化
		$data 						= (isset($lists['lists']) && !empty($lists['lists'])) ? $lists['lists'] : [];

    	if (!empty($data)) {

            //自行定义格式化数据输出
    		//foreach($data as $k=>$v){

    		//}
    	}

    	$lists['lists'] 			= $data;

    	return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$lists];
    }

    /**
     * 接口数据添加/更新
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function saveData($parame)
    {
        //主表数据库模型
    	$dbModel					= model($this->mainTable);

        //数据ID
        $id                         = isset($parame['id']) ? intval($parame['id']) : 0;

        //自行定义入库数据 为了防止参数未定义报错，先采用isset()判断一下
        $saveData                   = [];
        $saveData['title']          = isset($parame['title']) ? $parame['title'] : '';
        $saveData['description']    = isset($parame['description']) ? $parame['description'] : '';
        $saveData['project_id']     = isset($parame['project_id']) ? $parame['project_id'] : 0;
        $saveData['user_id']        = isset($parame['user_id']) ? $parame['user_id'] : 0;
        $saveData['update_time']    = time();
        //$saveData['parame']         = isset($parame['parame']) ? $parame['parame'] : '';

        //数据校验
        if(empty($saveData['title'])) return ['Code' => '100008', 'Msg'=>lang('100008',['title'])];
        if(empty($saveData['description'])) return ['Code' => '100008', 'Msg'=>lang('100008',['description'])];
        if(empty($saveData['project_id'])) return ['Code' => '100008', 'Msg'=>lang('100008',['project_id'])];
        if(empty($saveData['user_id'])) return ['Code' => '100008', 'Msg'=>lang('100008',['user_id'])];

        //规避遗漏定义入库数据
        if (empty($saveData))
        return ['Code' => '203', 'Msg'=>lang('notice_helper_data_error')];

        //自行处理数据入库条件
        //判断接口地址是否存在
        if ($dbModel->titleCheck($saveData['title'],$id)) return ['Code' => '200002', 'Msg'=>lang('200002')];
        //...
		
        //通过ID判断数据是新增还是更新
    	if ($id <= 0) {

            $saveData['create_time']                = time();
            $saveData['sort']                       = 1;

            //执行新增
    		$info 									= $dbModel->addData($saveData);
    	}else{

            //执行更新
    		$info 									= $dbModel->updateById($id,$saveData);
    	}

    	if (!empty($info)) {

    		return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info];
    	}else{

    		return ['Code' => '100015', 'Msg'=>lang('100015')];
    	}
    }

    /**
     * 接口数据详情
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function detailData($parame)
    {
        //主表数据库模型
    	$dbModel			= model($this->mainTable);

        //数据ID
        $id                 = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

    	$info 				= $dbModel->getOneById($id);

    	if (!empty($info)) {
    		
            //格式为数组
            $info                   = $info->toArray();

            //自行对数据格式化输出
            //...

    		return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info];
    	}else{

    		return ['Code' => '100015', 'Msg'=>lang('100015')];
    	}
    }

    /**
     * 接口数据快捷编辑
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function quickEditData($parame)
    {
        //主表数据库模型
    	$dbModel			= model($this->mainTable);

        //数据ID
        $id                 = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

    	$info 				= $dbModel->updateById($id,[$parame['fieldName']=>$parame['updata']]);

    	if (!empty($info)) {

    		return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['id'=>$id]];
    	}else{

    		return ['Code' => '100015', 'Msg'=>lang('100015')];
    	}
    }

    /**
     * 接口数据删除
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function delData($parame)
    {
        //主表数据库模型
    	$dbModel				= model($this->mainTable);

        //数据ID
        $id                 = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

        //自行定义删除条件
        //检测是否还有接口数据
        $modelParame['whereFun']    = 'formatWhereDevapiCount';
        $modelParame['apiParame']   = $parame;

        $devapiCount                = model('devapi')->getDataCount($modelParame);

        if ($devapiCount > 0) {

            return ['Code' => '200001', 'Msg'=>lang('200001',[$devapiCount])];
        }
        //...
        
        //执行删除操作
    	$delCount				= $dbModel->delData($id);

    	return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['count'=>$delCount]];
    }

    /*api:9b970e254e738a4c48c26d1a92c615db*/
    /**
     * * 接口导出接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function exportData($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        $modelid                = isset($parame['model_id']) ? intval($parame['model_id']) : 0;
        $apiid                  = isset($parame['api_id']) ? intval($parame['api_id']) : 0;

        $apilist                = model('devapi')->getDevapiListByModelid($modelid,$apiid);
        $parameList             = [];

        if (!empty($apilist)) {
            $apiids             = [];
            foreach ($apilist as $value) {
                $apiids[$value['id']]   = $value['id'];
            }

            $parameList         = model('devapi_parame')->getDevapiParameByApiids($apiids);
        }

        $exportData['apilist']      = $apilist;
        $exportData['parameList']   = $parameList;

        //需要返回的数据体
        $Data['exportData']         = json_encode($exportData);
        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$Data];
    }

    /*api:9b970e254e738a4c48c26d1a92c615db*/

    /*api:e395328b9fd150e692a62db9cd0b6cb6*/
    /**
     * * 接口导入接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function importData($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码
        $importData             = isset($parame['importData']) ? json_decode($parame['importData'],true) : [];
        $apilist                = isset($importData['apilist']) ? $importData['apilist'] : [];
        $parameList             = isset($importData['parameList']) ? $importData['parameList'] : [];

        if (!empty($apilist) && !empty($parameList))
        {   
            $plist          = [];
            foreach ($apilist as $av) {
                //检验接口地址是否存在
                $urlmd5         = md5(strtolower($av['apiurl'].$parame['project_id']));
                $isok           = model('devapi')->apiurlCheck($urlmd5,0);
                if ($isok <= 0) {
                    $apid           = $av['id'];
                    unset($av['id']);

                    $av['module_id']    = $parame['module_id'];
                    $av['user_id']      = $parame['uid'];
                    $av['create_time']  = time();
                    $av['update_time']  = time();
                    $av['urlmd5']       = $urlmd5;
                    
                    $aid                = model('devapi')->insertGetId($av);
                    foreach ($parameList as $key => $value)
                    {
                        if ($apid == $value['api_id']) {
                            unset($value['id']);
                            $value['api_id']    = $aid;
                            $plist[]            = $value;
                        }
                    }
                }
            }

            if (!empty($plist)) {
                model('devapi_parame')->insertAll($plist);
            }
        }
        //需要返回的数据体
        $Data['id']                   = 1;

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$Data];
    }

    /*api:e395328b9fd150e692a62db9cd0b6cb6*/

    /*接口扩展*/
}
