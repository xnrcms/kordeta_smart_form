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

class Menus extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'devmenu';
	
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

    /**
     * 接口列表数据
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function listData($parame)
    {
        //权限校验
        /*$menuid  = (isset($parame['menuid']) && (int)$parame['menuid'] > 0) ? (int)$parame['menuid'] : 0;
        if (!$this->checkUserPower($menuid)) return ['Code' => '202', 'Msg'=>lang('202')];*/

        //主表数据库模型
		$dbModel					= model($this->mainTable);
        $authMenuIds                = [];
        $gids                       = $this->getGroupIds();
        
        if (empty($gids)) return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>[]];

        //有权限的菜单ID
        if (in_array(2, $gids))
        {
            $modelParame  = [];
            $parame['ownerid']          = $this->getOwnerId();
            $parame['status']           = 1;
            $modelParame['apiParame']   = $parame;
            $modelParame['whereFun']    = 'formatWhereDefault';

            $menus2                     = model('devmenu')->getPageList($modelParame);
            $menus2                     = isset($menus2['lists']) ? $menus2['lists'] : [];

            foreach ($menus2 as $key => $value)
            {
                $authMenuIds[]      = [
                    'id'        => $value['id'],
                    'title'     => $value['title'],
                    'url'       => $value['url'],
                    'pid'       => $value['pid'],
                    'pos'       => $value['pos'],
                    'url_type'  => $value['url_type'],
                    'open_type' => $value['open_type'],
                ];
            }

            $lists                      = $authMenuIds;
        }else{
            $authMenuIds                = $this->getUserRulesId();
            $lists                      = $dbModel->getMenuAuthList($authMenuIds,$this->getUserId());
        }

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
        //$saveData['parame']         = isset($parame['parame']) ? $parame['parame'] : '';

        //规避遗漏定义入库数据
        if (empty($saveData)) return ['Code' => '120021', 'Msg'=>lang('120021')];

        //自行处理数据入库条件
        //...
		
        //通过ID判断数据是新增还是更新 定义新增条件下数据
    	if ($id <= 0)
        {
            //$saveData['parame']         = isset($parame['parame']) ? $parame['parame'] : '';
    	}

    	$info                                       = $dbModel->saveData($id,$saveData);

        return !empty($info) ? ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info] : ['Code' => '100015', 'Msg'=>lang('100015')];
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
        $info               = $dbModel->getRow($id);

        return !empty($info) ? ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info] : ['Code' => '100015', 'Msg'=>lang('100015')];
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

        //根据ID更新数据
        $info               = $dbModel->saveData($id,[$parame['fieldName']=>$parame['updata']]);

        return !empty($info) ? ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info] : ['Code' => '100015', 'Msg'=>lang('100015')];
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
        //...
        
        //执行删除操作
    	$delCount				= $dbModel->deleteData($id);

    	return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['count'=>$delCount]];
    }

    /*api:90d42ef36be1d53d3d6e1a6275ef4ddf*/
    /**
     * * 获取树状结构菜单数据接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function listDataTree($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        $authMenuIds                = [];
        $listData                   = [];
        $modelParame                = [];
        $parame['ownerid']          = $this->getOwnerId();
        $parame['status']           = 1;
        $modelParame['limit']       = 1000;
        $modelParame['apiParame']   = $parame;
        $modelParame['whereFun']    = 'formatWhereDefault';

        $menus2                     = model('devmenu')->getPageList($modelParame);
        $menus2                     = isset($menus2['lists']) ? $menus2['lists'] : [];

        foreach ($menus2 as $key => $value)
        {
            $authMenuIds[]      = [
                'id'        => $value['id'],
                'title'     => $value['title'],
                'pid'       => $value['pid'],
            ];
        }

        if (!empty($authMenuIds))
        {
            $Tree          = new \xnrcms\DataTree($authMenuIds);
            
            $Tree->setConfig('changeField',['id'=>'key']);
            $Tree->setConfig('deleteField',['id','pid']);
            $Tree->setConfig('childName','children');

            $listData      = $Tree->arrayTree();
        }
        
        $Data     = json_encode($listData);

        return ['Code' => '200', 'Msg'=>lang('200'),'Data'=>$Data];
    }

    /*api:90d42ef36be1d53d3d6e1a6275ef4ddf*/

    /*接口扩展*/
}
