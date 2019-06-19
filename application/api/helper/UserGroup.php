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

class UserGroup extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'user_group';
	
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
        //权限校验
        $menuid  = (isset($parame['menuid']) && (int)$parame['menuid'] > 0) ? (int)$parame['menuid'] : 0;
        if (!$this->checkUserPower($menuid)) return ['Code' => '202', 'Msg'=>lang('202')];

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
		$modelParame['order']		= 'main.id desc';
		
		//数据分页步长定义
		$modelParame['limit']		= isset($parame['limit']) ? $parame['limit'] : 10;

		//数据分页页数定义
		$modelParame['page']		= (isset($parame['page']) && $parame['page'] > 0) ? $parame['page'] : 1;

		//定义缓存KEY
        $modelParame['cacheKey']    = [
            isset($parame['search']) ? $parame['search'] : '',
            $modelParame['limit'],
            $modelParame['order'],
            $modelParame['page'],
        ];

        //列表数据
        $lists                      = $dbModel->getList($modelParame,$this->getOwnerId());

        //数据格式化
        $data                       = (isset($lists['lists']) && !empty($lists['lists'])) ? $lists['lists'] : [];

    	if (!empty($data))
        {
            //自行定义格式化数据输出
    		foreach($data as $k=>$v)
            {
                $gusers                 = '1';
                $data[$k]['gusers']     = $dbModel->getGuserByGroupId($v['id']);
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

        //自行定义入库数据 为了防止参数未定义报错，先采用isset()判断一下
        $saveData                   = [];

        if (isset($parame['title']))        $saveData['title']          = $parame['title'];
        if (isset($parame['description']))  $saveData['description']    = $parame['description'];
        if (isset($parame['status']))       $saveData['status']         = $parame['status'] == 1 ? 1 : 2;
        if (isset($parame['rules']))        $saveData['rules']          = $parame['rules'];

        $saveData['update_time']    = time();

        //数据ID
        $id                         = isset($parame['id']) ? intval($parame['id']) : 0;

        //检测分组名称是否存在
        if ($dbModel->checkValue($saveData['title'],$id,'title'))
        return ['Code' => '203', 'Msg'=>lang('error_title_already_exists')];

        //分组成员ID
        $gusersid                   = [];
        if (isset($parame['gusers']))
        {
            $gusers         = explode(',', $parame['gusers']);
            foreach ($gusers as $key => $value)
            {
                $gid            = model('user_group_access')->getUserGroupAccessListByUid($value);
                if (!empty($gid)) return ['Code' => '203', 'Msg'=>lang('notice_user_already_bind_group')];

                $gusersid[]     = $value;
            }
        }

        //规避遗漏定义入库数据
        if (empty($saveData)) return ['Code' => '203', 'Msg'=>lang('notice_undefined_data')];

        //自行处理数据入库条件
        //...

        //通过ID判断数据是新增还是更新 定义新增条件下数据
        if ($id <= 0)
        {
            $ownerid   = $this->getOwnerId();

            //其他分组暂时不能添加
            if ($ownerid < 0) return ['Code' => '203', 'Msg'=>lang('error_gropu_add_fail')];

            $saveData['ownerid']            = $ownerid;
            $saveData['create_time']        = time();
        }

        $info    = $dbModel->saveData($id,$saveData);

        //添加组员
        if (isset($parame['gusers']))
        {
            foreach ($gusersid as $uid) model('user_group_access')->setGroupAccess($uid,$info['id']);
        }

        return !empty($info) ? ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info] : ['Code' => '203', 'Msg'=>lang('notice_api_fail')];
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

        //数据详情
    	$info 				= $dbModel->getOneById($id);

    	if (!empty($info))
        {	
            //格式为数组
            $info                   = $info->toArray();

            $guser                  = $dbModel->getGuserByGroupId($info['id']);
            $info['guser']          = !empty($guser) ? json_encode($guser) : json_encode([]);

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
        if ($id <= 0) return ['Code' => '203', 'Msg'=>lang('notice_data_id')];

        //其他分组暂时不能编辑
        if ($this->getOwnerId() < 0) return ['Code' => '203', 'Msg'=>lang('error_gropu_edit_fail')];

        //根据ID更新数据
        $info               = $dbModel->saveData($id,[$parame['fieldName']=>$parame['updata']]);

        //清楚分组权限缓存
        if ($parame['fieldName'] == 'rules') model('user_group_access')->clearMenuAuthListByGid($id);

        return !empty($info) ? ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info] : ['Code' => '203', 'Msg'=>lang('notice_api_fail')];
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
        if ($id <= 0) return ['Code' => '203', 'Msg'=>lang('notice_data_id')];

        //其他分组暂时不能删除
        if ($this->getOwnerId() < 0) return ['Code' => '203', 'Msg'=>lang('error_gropu_del_fail')];

        //自行定义删除条件
        //...
        
        //执行删除操作
        $delCount               = $dbModel->deleteData($id,$this->getOwnerId());

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['count'=>$delCount]];
    }

    /*api:1a08f4cc54d01d345a9039698c7da566*/
    /**
     * * 获取用户组列表（用户组设置）
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function glistData($parame)
    {
        //权限校验
        if (!$this->checkUserPower(-1)) return ['Code' => '202', 'Msg'=>lang('202')];

        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码
        $lists                  = $dbModel->getAllUserGorupTitle($this->getOwnerId());
        $gaccess                = model('user_group_access')->getUserGroupAccessListByUid($parame['id']);

        if (!empty($lists)) {
            foreach ($lists as $key => $value) {
                if (in_array($value['id'], $gaccess)) {
                    $lists[$key]['selected']    = 1;
                }else{
                    $lists[$key]['selected']    = 0;
                }
            }
        }

        //需要返回的数据体
        $Data                   = !empty($lists) ? $lists : [];

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$lists];
    }

    /*api:1a08f4cc54d01d345a9039698c7da566*/

    /*api:1c3d588bc0f96f58a7a138a7eedfabb9*/
    /**
     * * 添加用户组组员
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function bindUser($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        $user_name              = isset($parame['username']) ? trim($parame['username']) : '';
        $uid                    = (int)model("user_center")->getUserCenterIdByUserName($user_name);

        //用户是否存在
        if ($uid <= 0) return ['Code' => '203', 'Msg'=>lang('notice_user_not_exist')];

        //用户是否已经加入到其他分组
        $gid            = model('user_group_access')->getUserGroupAccessListByUid($uid);
        if (!empty($gid)) return ['Code' => '203', 'Msg'=>lang('notice_user_already_bind_group')];

        //需要返回的数据体
        $Data['id']                   = $uid;

        return ['Code' => '000000', 'Msg'=>lang('000000'),'Data'=>$Data];
    }

    /*api:1c3d588bc0f96f58a7a138a7eedfabb9*/

    /*接口扩展*/
}
