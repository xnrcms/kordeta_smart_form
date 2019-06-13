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

class Devform extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'devform';
	
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
		$modelParame['order']		= 'main.sort DESC,main.id DESC';		
		
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

    	if (!empty($data))
        {
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
        $saveData['status']         = isset($parame['status']) ? $parame['status'] : 2;
        $saveData['tag']            = isset($parame['tag']) ? $parame['tag'] : '';
        $saveData['cname']          = isset($parame['cname']) ? strtolower($parame['cname']) : '';
        $saveData['sort']           = isset($parame['sort']) ? $parame['sort'] : 1;
        $saveData['pid']            = isset($parame['pid']) ? $parame['pid'] : 0;
        $saveData['config']         = isset($parame['config']) ? $parame['config'] : '';
        $saveData['update_time']    = time();
        //$saveData['parame']       = isset($parame['parame']) ? $parame['parame'] : '';

        if ($saveData['pid'] <= 0)
        {
            $cname              = explode('/', $saveData['cname']);
            $saveData['tag']    = implode('_', $cname);

            if (count($cname) !== 3)  return ['Code' => '200013', 'Msg'=>lang('200013')];
            if ($dbModel->checkFieldExist($saveData['tag'],$id,'tag')) return ['Code' =>'200012','Msg'=>lang('200012')];
        }
        else
        {
            $formtpl            = $dbModel->getOneById($saveData['pid']);
            if (empty($formtpl))  return ['Code' => '200014', 'Msg'=>lang('200014')];

            $saveData['cname']  = $formtpl['cname'] . '_' . $saveData['tag'];
            if ($dbModel->checkFieldExist($saveData['cname'],$id,'cname')) return ['Code' =>'200015','Msg'=>lang('200015')];
        }

        //规避遗漏定义入库数据
        if (empty($saveData)) return ['Code' => '120021', 'Msg'=>lang('120021')];

        //自行处理数据入库条件
        //...
		
        //通过ID判断数据是新增还是更新
    	if ($id <= 0)
        {
            $saveData['create_time']                = time();
    	}

        $info                                       = $dbModel->saveData($id,$saveData);

        //保存后发布数据
        $this->releaseData(['id'=>!empty($info) ? ($info['pid'] > 0 ? $info['pid'] : $info['id']) : 0]);

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

    	if (is_numeric($parame['id']))
        {    
            $info               = $dbModel->getOneById($parame['id']);
        }else{

            $info               = $dbModel->where('cname','eq',$parame['id'])->find();
        }

    	if (!empty($info))
        {
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

        //根据ID更新数据
        $info               = $dbModel->saveData($id,[$parame['fieldName']=>$parame['updata']]);

        //保存后发布数据
        $this->releaseData(['id'=>!empty($info) ? ($info['pid'] > 0 ? $info['pid'] : $info['id']) : 0]);

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
    	$dbModel				   = model($this->mainTable);

        //数据ID
        $id                 = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

        //自行定义删除条件
        $modelParame['whereFun']    = 'formatWhereChildForm';
        $modelParame['apiParame']   = $parame;

        $childCount                 = $dbModel->getDataCount($modelParame);

        if ($childCount > 0) return ['Code' => '200007', 'Msg'=>lang('200007')];
        //...
        
        //执行删除操作
    	$delCount				= $dbModel->delData($id);

        $this->releaseData(['id'=>$id]);

    	return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['count'=>$delCount]];
    }

    /*api:6d85a74742f0718de2bfd3d994955525*/
    /**
     * * 表单模板克隆接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function saveClone($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码

        $formId                     = $parame['formid'];
        $parame['pid']              = $formId;

        $cloneData                  = json_decode($parame['cloneData'],true);

        //主表名称，可以为空，默认当前模型名称
        $modelParame['MainTab']     = $this->mainTable;

        //接口数据
        $modelParame['apiParame']   = $parame;
        
        //检索条件 需要对应的模型里面定义查询条件 格式为formatWhere...
        $modelParame['whereFun']    = 'formatWhereDefault';
        
        //数据分页步长定义
        $modelParame['limit']       = 1000;
        
        //列表数据
        $lists                      = $dbModel->getPageList($modelParame);

        if (empty($lists) || !isset($lists['lists']) || empty($lists['lists']))
        return ['Code' => '200008', 'Msg'=>lang('200008')];

        $lists                      = $lists['lists'];
        $tpl                        = [];

        foreach ($lists as $key => $value)
        {
            foreach ($value as $kk => $vv)
            {  
                if ($kk == 'config')
                {
                    $value[$kk]   = json_decode($vv,true);
                }
            }

            $tpl[$value['id']]  = $value;
        }

        $title              = isset($parame['formname']) ? trim($parame['formname']) : '';
        $formtag            = isset($parame['formtag']) ? explode('/', $parame['formtag']) : [];
        $cname              = implode('/', $formtag);
        $tag                = implode('_', $formtag);

        if (count($formtag) !== 3)  return ['Code' => '200013', 'Msg'=>lang('200013')];
        if ($dbModel->checkFieldExist($title,0,'title')) return ['Code' =>'200001','Msg'=>lang('200001')];
        if ($dbModel->checkFieldExist($tag,0,'tag')) return ['Code' =>'200012','Msg'=>lang('200012')];


        $saveData['title']                      = $parame['formname'];
        $saveData['tag']                        = $tag;
        $saveData['cname']                      = $cname;
        $saveData['status']                     = 1;
        $saveData['sort']                       = 1;
        $saveData['pid']                        = 0;
        $saveData['config']                     = json_encode([]);
        $saveData['update_time']                = time();
        $saveData['create_time']                = time();
        $info                                   = $dbModel->addData($saveData);

        if (empty($info) || $info['id'] <= 0)
        return ['Code' => '200009', 'Msg'=>lang('200009')];

        $pid                    = $info['id'];

        $updata                 = [];
        $cloneFormId            = $cloneData['formid'];

        foreach ($cloneFormId as $key => $value)
        {
            $title   = (isset($cloneData['title'][$key]) && !empty($cloneData['title'][$key])) ? $cloneData['title'][$key] : $tpl[$value]['title'];
            $tag     = (isset($cloneData['tag'][$key]) && !empty($cloneData['tag'][$key])) ? $cloneData['tag'][$key] : $tpl[$value]['tag'];
            $fname   = $cname . '_' . $tag;
            $status  = (isset($cloneData['status'][$key]) && !empty($cloneData['status'][$key])) ? $cloneData['status'][$key] : $tpl[$value]['status'];
            $sort    = (isset($cloneData['sort'][$key]) && !empty($cloneData['sort'][$key])) ? $cloneData['sort'][$key] : $tpl[$value]['sort'];

            $type    = (isset($cloneData['type'][$key]) && !empty($cloneData['type'][$key])) ? $cloneData['type'][$key] : $tpl[$value]['config']['type'];
            $group   = (isset($cloneData['group'][$key]) && !empty($cloneData['group'][$key])) ? $cloneData['group'][$key] : $tpl[$value]['config']['group'];
            $require = (isset($cloneData['require'][$key]) && !empty($cloneData['require'][$key])) ? $cloneData['require'][$key] : $tpl[$value]['config']['require'];
            $add     = (isset($cloneData['add'][$key]) && !empty($cloneData['add'][$key])) ? $cloneData['add'][$key] : $tpl[$value]['config']['add'];
            $edit    = (isset($cloneData['edit'][$key]) && !empty($cloneData['edit'][$key])) ? $cloneData['edit'][$key] : $tpl[$value]['config']['edit'];
            $notice  = (isset($cloneData['notice'][$key]) && !empty($cloneData['notice'][$key])) ? $cloneData['notice'][$key] : $tpl[$value]['config']['notice'];
            $default = (isset($cloneData['default'][$key]) && !empty($cloneData['default'][$key])) ? $cloneData['default'][$key] : $tpl[$value]['config']['default'];
            $attr    = (isset($cloneData['attr'][$key]) && !empty($cloneData['attr'][$key])) ? $cloneData['attr'][$key] : $tpl[$value]['config']['attr'];
        
            $config                   = [];

            $config['title']          = $title;
            $config['tag']            = $tag;
            $config['type']           = $type;
            $config['group']          = $group;
            $config['require']        = $require;
            $config['add']            = $add;
            $config['edit']           = $edit;
            $config['notice']         = $notice;
            $config['default']        = $default;
            $config['attr']           = $attr;
            $config                   = json_encode($config);

            $updata[] = [
                'title'=>$title,
                'pid'=>$pid,
                'tag'=>$tag,
                'cname'=>$fname,
                'config'=>$config,
                'status'=>$status,
                'sort'=>$sort,
                'create_time'=>time(),
                'update_time'=>time()
            ];
        }

        if (!empty($updata))
        {    
            $res = $dbModel->insertAll($updata);

            $this->releaseData(['id'=>$pid]);

            return ['Code' => '200', 'Msg'=>lang('200011'),'Data'=>['id'=>$pid]];
        }
        
        $dbModel->delData($pid);

        return ['Code' => '200010', 'Msg'=>lang('200010')];
    }

    /*api:6d85a74742f0718de2bfd3d994955525*/

    /*api:d23bb21d40e0fa32e9c5ab7fc21f9f6a*/
    /**
     * * 表单模板发布接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function releaseData($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //数据ID
        $id                 = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

        //自行书写业务逻辑代码
        $info               = $dbModel->getOneById($id);
        $info               = !empty($info) ? $info->toArray() : [];

        if (!empty($info))
        {
            $cname          = isset($info['cname']) ? $info['cname'] : '';
            if (empty($cname)) return ['Code' => '200004', 'Msg'=>lang('200004')];

            $cname          = explode('/', $cname);
            if (count($cname) !== 3)  return ['Code' => '200013', 'Msg'=>lang('200013')];

            $data           = $dbModel->getReleaseFormTplList($id);
            $releaseCount   = count($data);
            
            if ($releaseCount <= 0) return ['Code' => '200014', 'Msg'=>lang('200014')];

            set_release_data($data,$info['cname'],'form');

            return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['count'=>$releaseCount]];
        }

        return ['Code' => '200014', 'Msg'=>lang('200014')];
    }

    /*api:d23bb21d40e0fa32e9c5ab7fc21f9f6a*/

    /*api:3a6f32c1317c6b3070e395aaf910a89a*/
    /**
     * * 表单模板初始化接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function initFormData($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码
        $cname                  = isset($parame['cname']) ? strtolower($parame['cname']) : '';
        $title                  = isset($parame['title']) ? $parame['title'] : '';
        $id                     = $dbModel->getTplIdByCname($cname);
        
        if (!empty($id) && $id >= 0)
        {
            $info     = $dbModel->getOneById($id);

            //表单名称相同 直接返回
            if ( $info['title'] != $title)
            {
                $updata                 = [];
                $updata['title']        = $title;
                $updata['update_time']  = time();

                $dbModel->updateById($id,$updata);

                //保存后发布数据
                $this->releaseData(['id'=>$id]);
            }
        }else{

            $updata                     = [];
            $cname                      = explode('/', $cname);
            $updata['tag']              = implode('_', $cname);

            if (count($cname) !== 3)  return ['Code' => '200013', 'Msg'=>lang('200013')];
            if ($dbModel->checkFieldExist($updata['tag'],0,'tag')) return ['Code' =>'200012','Msg'=>lang('200012')];

            //不存在新增并返回模板ID
            $updata['title']        = $title;
            $updata['pid']          = 0;
            $updata['status']       = 1;
            $updata['cname']        = implode('/', $cname);
            $updata['config']       = '';
            $updata['create_time']  = time();
            $updata['update_time']  = time();

            //入库数据
            $info                   = $dbModel->addData($updata);
            $id                     = isset($info['id']) ? $info['id'] : 0;

            //自动生成一个ID字段
            $updata                 = [];
            $updata['title']        = 'ID';
            $updata['pid']          = $id;
            $updata['tag']          = 'id';
            $updata['sort']         = 0;
            $updata['status']       = 1;
            $updata['cname']        = implode('/', $cname) . '_id';
            $updata['config']       = '{"title":"ID","tag":"id","type":"hidden","group":"","require":2,"add":1,"edit":2,"notice":"","default":"","field_value":"","attr":""}';
            $updata['create_time']  = time();
            $updata['update_time']  = time();
            $dbModel->insert($updata);

            $this->releaseData(['id'=>$id]);
        }

        //需要返回的数据体
        $Data['id']                   = $id;

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$Data];
    }

    /*api:3a6f32c1317c6b3070e395aaf910a89a*/

    /*接口扩展*/
}
