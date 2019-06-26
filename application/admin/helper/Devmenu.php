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

class Devmenu extends Base
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

        $parame['ownerid']          = $this->getOwnerId();
        //接口数据
        $modelParame['apiParame']   = $parame;

		//检索条件 需要对应的模型里面定义查询条件 格式为formatWhere...
		$modelParame['whereFun']	= 'formatWhereDefault';

		//排序定义
		$modelParame['order']		= 'main.sort desc,main.id desc';		
		
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

            $status                 = ['未知','启用','禁用'];
            //自行定义格式化数据输出
    		foreach($data as $k=>$v){
                $data[$k]['status']     = $status[$v['status']];
                $data[$k]['status2']    = (int)$v['status'];
    		}
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
        
        //数据操作类型
        $operation                      = isset($parame['operation']) ? trim($parame['operation']) : [];
        if (!empty($operation))
        {
            $operation                  = explode(',', $operation);
            foreach ($operation as $val) {
                if (!in_array($val, [1,2,3,4]))
                return ['Code' => '203', 'Msg'=>lang('notice_operation_type')];
            }
        }

        //自行定义入库数据 为了防止参数未定义报错，先采用isset()判断一下
        $saveData                   = [];
        $saveData['title']          = isset($parame['title']) ? $parame['title'] : '';
        $saveData['status']         = isset($parame['status']) ? $parame['status'] : 2;
        $saveData['url']            = isset($parame['url']) ? $parame['url'] : '';
        $saveData['sort']           = isset($parame['sort']) ? $parame['sort'] : 1;
        $saveData['pid']            = isset($parame['pid']) ? $parame['pid'] : 0;
        $saveData['posttype']       = isset($parame['posttype']) ? $parame['posttype'] : '';
        $saveData['pos']            = isset($parame['pos']) ? $parame['pos'] : 1;
        $saveData['icon']           = isset($parame['icon']) ? $parame['icon'] : '';
        $saveData['fsize']          = isset($parame['fsize']) ? $parame['fsize'] : '800*550';
        $saveData['project_id']     = isset($parame['project_id']) ? $parame['project_id'] : 1;
        $saveData['update_time']    = time();
        $saveData['open_type']      = isset($parame['open_type']) ? (int)$parame['open_type'] : 0;
        $saveData['url_type']       = isset($parame['url_type']) ? (int)$parame['url_type'] : 0;
        $saveData['operation']      = !empty($operation) ? implode(',', $operation) : '';
        //$saveData['parame']       = isset($parame['parame']) ? $parame['parame'] : '';

        //数据校验
        if(empty($saveData['title'])) return ['Code' => '100008', 'Msg'=>lang('100008',['title'])];
        if(empty($saveData['url'])) return ['Code' => '100008', 'Msg'=>lang('100008',['url'])];

        $saveData['fsize']          = !empty($saveData['fsize']) ? $saveData['fsize'] : '800*550';
        $saveData['pos']            = ($saveData['pos'] >= 10 || $saveData['pos'] <= 0) ? 1 : $saveData['pos'];
        $saveData['posttype']       = !in_array($saveData['posttype'],[1,2,3]) ? 1 : $saveData['posttype'];

        //规避遗漏定义入库数据
        if (empty($saveData)) return ['Code' => '120021', 'Msg'=>lang('120021')];

        //自行处理数据入库条件
        //...
		
        //通过ID判断数据是新增还是更新
    	if ($id <= 0) {
            $saveData['ownerid']                = $this->getOwnerId();
            $saveData['create_time']            = time();
    	}

        $info                                   = $dbModel->saveData($id,$saveData);

    	if (!empty($info)) {

            //处理数据操作
            $this->addDataOperation($operation,$info);

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

        $menuinfo           = $dbModel->getOneById($id);
        
        if ( isset($menuinfo['operation']) && !empty($menuinfo['operation']))
        {
            $childMenus     = $dbModel->getChildMenuByPid($menuinfo['id']);
            foreach ($childMenus as $value)
            {
                $dbModel->delData($value['id']);
            }

            //执行删除操作
            $delCount               = $dbModel->delData($id);

            return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['count'=>$delCount]];
        }

        //自行定义删除条件
        //...
        $modelParame['whereFun']    = 'formatWhereChildMenu';
        $modelParame['apiParame']   = $parame;

        $childMenuCount             = $dbModel->getDataCount($modelParame);

        if ($childMenuCount > 0) {

            return ['Code' => '203', 'Msg'=>lang('notice_have_children')];
        }

        //执行删除操作
    	$delCount				= $dbModel->delData($id);

    	return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['count'=>$delCount]];
    }

    /*api:3fb60204afa7d463d2e65238cc913f37*/
    /**
     * * 菜单发布
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function releaseData($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码
        $project_id             = 1;
        $menu                   = $dbModel->getReleaseMenu(['project_id'=>$project_id]);
        $filecode               = 'menu.data.project_id=' . $project_id;

        set_release_data($menu,$filecode,'menu');

        //需要返回的数据体
        $Data                   = ['id'=>$project_id];

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$Data];
    }

    /*api:3fb60204afa7d463d2e65238cc913f37*/

    /*接口扩展*/

    //处理数据操作菜单
    private function addDataOperation($operation = [],$info)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);
        $pid                    = isset($info['id']) ? $info['id'] : 0;
        $ownerid                = isset($info['ownerid']) ? $info['ownerid'] : 0;

        $purl                   = isset($info['url']) ? $info['url'] : '';
        $purl                   = !empty($purl) ? explode('/', $purl) : [];
        $purl                   = isset($purl[0]) ? $purl[0] : '';

        //删除子菜单 根据父ID
        $dbModel->delOperationMenuByPid($pid,$this->getOwnerId());

        //无操作直接返回
        if (empty($operation)) return;

        $title      = [1=>'新增',2=>'查看',3=>'编辑',4=>'删除'];
        $url        = [1=>'addOperation',2=>'viewOperation',3=>'editOperation',4=>'delOperation'];
        $addData    = [];

        foreach ($operation as $value)
        {
            $addData[]  = [
                'title'             => isset($title[$value]) ? $title[$value] : '',
                'pid'               => $pid,
                'sort'              => 100 - $value,
                'url'               => trim($purl,'/') . '/' . (isset($url[$value]) ? $url[$value] : ''),
                'pos'               => in_array($value, [1]) ? 2 : 3,
                'status'            => 1,
                'create_time'       => time(),
                'update_time'       => time(),
                'posttype'          => 0,
                'ownerid'           => $ownerid,
                'open_type'         => 0,
                'url_type'          => 0
            ]; 
        }

        if (!empty($addData))
        {
            $dbModel->addDataOperation($addData,$ownerid);
        }
    }
}
