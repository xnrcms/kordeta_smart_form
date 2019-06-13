<?php
/**
 * XNRCMS<562909771@qq.com>
 * ============================================================================
 * 版权所有 2018-2028 小能人科技有限公司，并保留所有权利。
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用TP5助手函数可实现单字母函数M D U等,也可db::name方式,可双向兼容
 * ============================================================================
 * Author: xnrcms<562909771@qq.com>
 * Date: 2018-02-05
 * Description:接口设计
 */

namespace app\manage\controller;

use app\manage\controller\Base;

class Devapi extends Base
{
    private $apiUrl         = [];
    private $project_id     = 0;

    public function __construct()
    {
        parent::__construct();

        $this->tpl                                  = new \xnrcms\DevTpl();
        $this->apiUrl['index']                      = 'admin/Devapi/listData';
        $this->apiUrl['edit']                       = 'admin/Devapi/detailData';
        $this->apiUrl['debug']                      = 'admin/Devapi/detailData';
        $this->apiUrl['add_save']                   = 'admin/Devapi/saveData';
        $this->apiUrl['edit_save']                  = 'admin/Devapi/saveData';
        $this->apiUrl['quickedit']                  = 'admin/Devapi/quickEditData';
        $this->apiUrl['del']                        = 'admin/Devapi/delData';
        $this->apiUrl['apirelease']                 = 'admin/Devapi/apiRelease';
        $this->apiUrl['addbaseapi']                 = 'admin/Devapi/addBaseapi';
        $this->apiUrl['import']                     = 'admin/DevapiModule/importData';

        //接口参数
        $this->apiUrl['setparame']                  = 'admin/DevapiParame/listData';
        $this->apiUrl['quickeditdevapiparame']      = 'admin/DevapiParame/quickEditData';
        $this->apiUrl['deldevapiparame']            = 'admin/DevapiParame/delData';

        //接口模块
        $this->apiUrl['module_index']               = 'admin/DevapiModule/listData';
        $this->apiUrl['editmodule']                 = 'admin/DevapiModule/detailData';
        $this->apiUrl['addmodule_save']             = 'admin/DevapiModule/saveData';
        $this->apiUrl['editmodule_save']            = 'admin/DevapiModule/saveData';
        $this->apiUrl['delmodule']                  = 'admin/DevapiModule/delData';

        $this->project_info                         = $this->getProjectInfo();
    }

	//列表页面
	public function index()
    {
        //参数数据接收
        $param      = request()->param();

        //初始化模板
        $listNode   = $this->tpl->showListTpl($this->getTplData('','接口列表','list'));
        $listId     = isset($listNode['info']['id']) ? intval($listNode['info']['id']) : 0;
        $listTag    = isset($listNode['tags']) ? $listNode['tags'] : '';

        //参数定义
        $menuid     = isset($param['menuid']) ? $param['menuid'] : 0;
        $page       = isset($param['page']) ? $param['page'] : 1;
        $search     = $this->getSearchParame($param);
        $isTree     = 0;

        $module_id  = isset($param['mid']) ? $param['mid'] : -1;
        $mkeys      = isset($param['keys']) ? $param['keys'] : 0;


        //初始化接口列表ID
        session('api_list_ids_to_release',null);

        $search['project_id']   = $this->project_info['id'];

        //模块列表
        $parame             = [];
        $parame['uid']      = $this->uid;
        $parame['hashid']   = $this->hashid;
        $parame['page']     = $page;
        $parame['search']   = !empty($search) ? json_encode($search) : '' ;

        $listData2          = [];
        $res2               = $this->apiData($parame,$this->apiUrl['module_index']);
        $data2              = $this->getApiData() ;

        if ($res2)
        {    
            $listData2      = $data2['lists'];
            $mid            = isset($listData2[0]['id']) ? $listData2[0]['id'] : -1;
            $module_id      = $module_id <= 0 ? $mid : $module_id;
        }

        //页面操作功能菜单
        $topMenu    = formatMenuByPidAndPos($menuid,2, $this->menu);
        $rightMenu  = formatMenuByPidAndPos($menuid,3, $this->menu);

        //获取接口列表数据
        $search                 = [];
        $search['module_id']    = intval($module_id);

        $parame             = [];
        $parame['uid']      = $this->uid;
        $parame['hashid']   = $this->hashid;
        $parame['page']     = input('page',1);
        $parame['search']   = !empty($search) ? json_encode($search) : '' ;

        //请求数据
        if (!isset($this->apiUrl[request()->action()]) || empty($this->apiUrl[request()->action()]))
        $this->error('未设置接口地址');

        $res                = $this->apiData($parame,$this->apiUrl[request()->action()]);
        $data               = $this->getApiData() ;

        $total 				= 0;
        $p 					= '';
        $listData 			= [];

        if ($res)
        {
            $p              = $this->pageData($data);//分页信息
            $total          = $data['total'];
            $listData       = $data['lists'];
        }

        $project_name           = !empty($this->project_info['title'])?'【<font color="red">'.$this->project_info['title'].'</font>】项目':'';
        
        //页面头信息设置
        $pageData['isback']     = 0;
        $pageData['title1']     = '接口设计';
        $pageData['title2']     = $project_name.'接口快速设计';
        $pageData['notice']     = ['接口只有在上线状态才能正常使用'];
        $pageData['mkeys']      = $mkeys;

        $this->extends_param    .= 'module_id/'.$module_id.'/';

        //渲染数据到页面模板上
        $assignData['_page']            = $p;
        $assignData['_total']           = $total;
        $assignData['topMenu']          = $topMenu;
        $assignData['rightMenu']        = $rightMenu;
        $assignData['listId']           = $listId;
        $assignData['listNode']         = $listNode;
        $assignData['listData']         = $listData;
        $assignData['listData2']        = $listData2;
        $assignData['pageData']         = $pageData;
        $this->assignData($assignData);

        $apiids         = [];
        if (!empty($listData)) {

            foreach ($listData as $key => $value) $apiids[$value['id']]   = $value['id'];

            sort($apiids);
            session('api_list_ids_to_release',$apiids);
        }

        //记录当前列表页的cookie
        cookie('__forward__',$_SERVER['REQUEST_URI']);
        cookie('__listtag__',$listTag);

        //异步请求处理
        if(request()->isAjax())
        {
        	echo json_encode(['listData'=>$this->fetch('public/list/listData'),'listPage'=>$p]);exit;
        }

        //加载视图模板
        return view();
	}

	//新增页面
	public function add()
    {
		//数据提交
        if (request()->isPost()) $this->update();

        //初始化表单模板 默认当前路由为唯一标识，自己可以自定义标识
        $formNode   = $this->tpl->showFormTpl($this->getTplData('addedit','新增/编辑接口设计表单','form'),0);
        $formId     = isset($formNode['info']['id']) ? intval($formNode['info']['id']) : 0;
        $formTag    = isset($formNode['tags']) ? $formNode['tags'] : '';
        $formList   = isset($formNode['list']) ? $formNode['list'] : [];

        //数据详情
        $info                           = $this->getDetail(0);
        $apidoc_name                    = session('apidoc_user_auth.apidoc_name');
        $info['author']                 = !empty($apidoc_name) ? $apidoc_name : '';
        $info['module_id']              = intval(input('module_id'));
        $info['project_id']             = $this->project_info['id'];
        $info['user_id']                = $this->project_info['user_id'];
        
        //页面数据
        $pageData                       = [];
        $pageData['isback']             = 0;
        $pageData['title1']             = '';
        $pageData['title2']             = '';
        $pageData['notice']             = [''];

        //渲染数据到页面模板上
        $assignData['formId']           = $formId;
        $assignData['formTag']          = $formTag;
        $assignData['formFieldList']    = $formList;
        $assignData['pageData']         = $pageData;
        $assignData['defaultData']      = $this->getDefaultParameData();
        $assignData['info']             = $info;
        $this->assignData($assignData);

        //加载视图模板
        return view('addedit');
	}

	//编辑页面
	public function edit($id = 0)
    {
		//数据提交
        if (request()->isPost()) $this->update();

		//初始化表单模板 默认当前路由为唯一标识，自己可以自定义标识
        $formNode   = $this->tpl->showFormTpl($this->getTplData('addedit','新增/编辑接口设计表单','form'),0);
        $formId     = isset($formNode['info']['id']) ? intval($formNode['info']['id']) : 0;
        $formTag    = isset($formNode['tags']) ? $formNode['tags'] : '';
        $formList   = isset($formNode['list']) ? $formNode['list'] : [];

        //数据详情
        $info                           = $this->getDetail($id);
        $apidoc_name                    = session('apidoc_user_auth.apidoc_name');
        $info['author']                 = !empty($apidoc_name) ? $apidoc_name : '';
        $info['module_id']              = intval(input('module_id'));
        $info['project_id']             = $this->project_info['id'];

        //页面数据
        $pageData                       = [];
        $pageData['isback']             = 0;
        $pageData['title1']             = '';
        $pageData['title2']             = '';
        $pageData['notice']             = [''];

        //渲染数据到页面模板上
        $assignData['formId']           = $formId;
        $assignData['formTag']          = $formTag;
        $assignData['formFieldList']    = $formList;
        $assignData['pageData']         = $pageData;
        $assignData['defaultData']      = $this->getDefaultParameData();
        $assignData['info']             = $info;
        $this->assignData($assignData);

        //加载视图模板
        return view('addedit');
	}

    //数据删除
    public function del()
    {
        $ids     = request()->param();
        $ids     = (isset($ids['ids']) && !empty($ids['ids'])) ? $ids['ids'] : $this->error('请选择要操作的数据');;
        $ids     = is_array($ids) ? implode($ids,',') : '';

        //请求参数
        $parame['uid']          = $this->uid;
        $parame['hashid']       = $this->hashid;
        $parame['id']           = $ids ;

        //请求地址
        if (!isset($this->apiUrl[request()->action()]) || empty($this->apiUrl[request()->action()]))
        $this->error('未设置接口地址');

        //接口调用
        $res       = $this->apiData($parame,$this->apiUrl[request()->action()]) ;
        $data      = $this->getApiData() ;

        if($res == true){

            $this->success('删除成功',Cookie('__forward__')) ;
        }else{
            
            $this->error($this->getApiError()) ;
        }
    }

    //快捷编辑
	public function quickEdit()
    {
        //请求地址
        if (!isset($this->apiUrl[request()->action()]) || empty($this->apiUrl[request()->action()]))
        $this->error('未设置接口地址');
        
        //接口调用
        if ($this->questBaseEdit($this->apiUrl[request()->action()])) $this->success('更新成功');
        
        $this->error('更新失败');
    }

    //处理提交新增或编辑的数据
    private function update()
    {
        //表单数据
        $postData                = request()->param();
        if(!$this->tpl->checkFormTpl($postData)) $this->error('表单模板数据不存在');

        //接口数据
        $signData                   = $this->tpl->getFormTplData($postData);
        $signData['uid']            = $this->uid;
        $signData['hashid']         = $this->hashid;

        //请求数据
        if (!isset($this->apiUrl[request()->action().'_save'])||empty($this->apiUrl[request()->action().'_save'])) 
        $this->error('未设置接口地址');

        $res       = $this->apiData($signData,$this->apiUrl[request()->action().'_save']) ;
        $data      = $this->getApiData() ;

        if($res){

            $this->success($signData['id']  > 0 ? '更新成功' : '新增成功',Cookie('__forward__')) ;
        }else{

            $this->error($this->getApiError()) ;
        }
    }
    
    //获取数据详情
    private function getDetail($id = 0)
    {
        $info           = [];

        if ($id > 0) {
            
            //请求参数
            $parame             = [];
            $parame['uid']      = $this->uid;
            $parame['hashid']   = $this->hashid;
            $parame['id']       = $id ;

            //请求数据
            $apiUrl     = (isset($this->apiUrl[request()->action()]) && !empty($this->apiUrl[request()->action()])) ? $this->apiUrl[request()->action()] : $this->error('未设置接口地址');
            $res        = $this->apiData($parame,$apiUrl);
            $info       = $res ? $this->getApiData() : $this->error($this->getApiError());
        }

        return $info;
    }

    //扩展枚举，布尔，单选，复选等数据选项
    public function getDefaultParameData()
    {
        $defaultData['api_type']        = ['默认',"列表","数据更新","数据详情","快捷编辑","数据删除"];
        $defaultData['is_required']     = [1=>"必填",2=>"不必填"];
        $defaultData['ptype']           = ['string'=>"字符串",'json'=>"JSON",'number'=>'整形','float'=>'浮点型'];
        $defaultData['ptype2']          = ['string'=>"字符串",'json'=>"JSON",'number'=>'整形','float'=>'浮点型','array'=>"数组",'object'=>"对象"];

        return $defaultData;
    }

    //设置接口参数
    public function setParame($id)
    {
        $public_parame      = $this->get_devapi_parame(3,0);
        $request_parame     = $this->get_devapi_parame(1,$id);
        $back_parame        = $this->get_devapi_parame(2,$id);

        //页面数据
        $pageData                       = [];
        $pageData['isback']             = 1;
        $pageData['title1']             = '接口参数设计';
        $pageData['title2']             = '项目接口参数快速设计';
        $pageData['notice']             = [
            '接口接口参数设计分为请求参数和返回参数',
            '在设计参数类型时请选择正确的参数类型',
            '返回参数只有参数类型为数组才能添加二级参数'
        ];

        //渲染数据到页面模板上
        $assignData['id']               = $id;
        $assignData['public_parame']    = $public_parame[0];
        $assignData['request_parame']   = $request_parame[0];
        $assignData['back_parame']      = $this->toLevel($back_parame[0],'&nbsp;&nbsp;&nbsp;&nbsp;');
        $assignData['pageData']         = $pageData;
        $assignData['defaultData']      = $this->getDefaultParameData();
        $this->assignData($assignData);

        //加载视图模板
        return view('set_parame');
    }

    //接口调试
    public function debug($id)
    {
        //数据提交
        if (request()->isPost()) $this->startDebug();

        session("debug_parame_detail_".$id,null);
        session("debug_get_devapi_parame_".$id,null);

        $envs               = $this->project_info['envs'];
        $info               = $this->getDetail($id);
        $request_fields     = $this->get_devapi_parame(1,$id);
        $request_fields     = !empty($request_fields[0]) ? $request_fields[0] : [];

        session("debug_parame_detail_".$id,$info);
        session("debug_get_devapi_parame_".$id,$request_fields);

        if (!empty($request_fields))
        {
            foreach ($request_fields as $key => $value)
            {
                if (in_array($value['tag'],['uid','hashid'])) 
                $request_fields[$key]['default_value'] = session('api_'.$value['tag']);
                
                if (!in_array($value['tag'],['uid','hashid']) && $value['default_value'] == '/') 
                $request_fields[$key]['default_value'] = '';
            }
        }

        //页面头信息设置
        $api_name                       = !empty($info['title'])?'【<font color="red">'.$info['title'].'</font>】':'';
        $pageData['isback']             = 1;
        $pageData['title1']             = '接口调试';
        $pageData['title2']             = $api_name.'接口快捷调试';
        $pageData['notice']             = ['接口调试请正确选择接口域名，默认本地调试'];

        $assignData['pageData']         = $pageData;
        $assignData['envs']             = $envs;
        $assignData['info']             = $info;
        $assignData['request_fields']   = $request_fields;
        $assignData['project']          = $this->project_info;

        $this->assignData($assignData);
        return view();
    }

    private function startDebug()
    {
        $postData       = request()->param();
        $info           = session("debug_parame_detail_".$postData['id']);
        $request_fields = session("debug_get_devapi_parame_".$postData['id']);

        $apiData        = !empty($postData['api']) ? $postData['api'] : $this->error('未发现接口数据');

        if (empty($info))  $this->error('抱歉，接口不存在');

        $apiName        = !empty($info['apiurl']) ? explode('/',trim($info['apiurl'],'/')) : [];
        $mName          = isset($apiName[0]) ? $apiName[0] : '';
        $cName          = isset($apiName[1]) ? humpToLine($apiName[1]) : '';
        $aName          = isset($apiName[2]) ? $apiName[2] : '';

        if (empty($mName) || empty($cName) || empty($aName))
        $this->error('接口名称格式错误');

        $apiName                = $mName . '/' . $cName . '/' . $aName;
        $envs                   = $this->project_info['envs'];

        if (empty($request_fields))  $this->error('未发现接口参数');

        $parame         = [];
        foreach ($request_fields as $val)
        {
            //必填
            if ($val['is_required'] == 1)
            {
               if (!isset($apiData[$val['tag']])) $this->error('抱歉，缺少【'.$val['tag'].'】参数');
               if (empty($apiData[$val['tag']])) $this->error('抱歉，【'.$val['title'].'】不能为空');
            }
            else
            {
                if (!isset($apiData[$val['tag']]) || empty($apiData[$val['tag']]))
                $apiData[$val['tag']]          = '';//$defaultDataType[$apiData['type']];
            }

            $parame[$val['tag']]          = $apiData[$val['tag']];
        }

        $api_auth_id            = $this->project_info['api_id'];
        $api_auth_key           = $this->project_info['api_key'];
        $api_auth_url           = trim($envs[$apiData['domain']]['domain']);

        $apiRequest             = new \xnrcms\ApiRequest($api_auth_url, $api_auth_id, $api_auth_key);
        $backData               = $apiRequest->postData($parame,$apiName);
        $errorInfo              = $apiRequest->getError();

        if(empty($errorInfo))
        {
            $backInfo           = json_decode($backData,true);
            //$backData           = json_encode($backInfo);
            if ($backInfo)
            {
                if ($backInfo['Code'] === "200")
                {
                    if (strpos(strtolower($info['apiurl']).'@','login'))
                    {
                        session('api_uid',$backInfo['Data']['uid']);
                        session('api_hashid',$backInfo['Data']['hashid']);
                    }

                    //正确数据事例
                    request()->post(['fieldName'=>'demo_success']);
                    request()->post(['dataId'=>$info['id']]);
                    request()->post(['value'=>$backData]);

                    $this->questBaseEdit($this->apiUrl['quickedit']);
                }
            }
        }else{
            $backData           = json_encode($errorInfo);
        }

        /*if (!preg_match("/^((?!<\!DOCTYPE html>).)*$/is", $backData)){

            $arr['Code']        = '404';
            $arr['Msg']         = '接口错误,请复制下方地址在地址栏访问查看具体错误';
            $arr['Time']        = time();

            $urls               = '/xnrcms?';
            foreach ($apiRequest->getApiData() as $key => $value) {
                $urls .=  $key.'=' . $value .'&';
            }

            $arr['ApiUrl']      = str_replace('/xnrcms',trim($api_auth_url,'/').'/'.trim($info['apiurl'],'/'),trim($urls,'&'));
            $arr['Data']        = $apiRequest->getApiData();
            $backData           = json_encode($arr);
        }*/ 

        $this->success('请求成功','',$backData);
    }

    private function get_devapi_parame($method = 0,$apiid=0)
    {
        $search['method']   = $method;
        $search['api_id']   = $apiid;

        //获取列表数据
        $parame['uid']      = $this->uid;
        $parame['hashid']   = $this->hashid;
        $parame['page']     = input('page',1);
        $parame['search']   = !empty($search) ? json_encode($search) : '' ;

        //请求数据
        if (!isset($this->apiUrl['setparame']) || empty($this->apiUrl['setparame']))
        $this->error('未设置接口地址');

        $res                = $this->apiData($parame,$this->apiUrl['setparame']);
        $data               = $this->getApiData() ;

        $listData           = [];

        if ($res)
        {
            $listData       = $data['lists'];
        }

        $delIds             = [];
        if (!empty($listData))
        {
            foreach ($listData as $key => $value)
            {
                $delIds[$value['id']] = $value['id'];
            }
        }

        return [$listData,$delIds];
    }

    //快捷编辑接口参数
    public function quickEditDevapiParame()
    {
        //请求地址
        if (!isset($this->apiUrl[request()->action()]) || empty($this->apiUrl[request()->action()]))
        $this->error('未设置接口地址');

        $dataId         = intval(input('dataId'));
        $value          = trim(input('value'));
        $apiid          = trim(input('apiid',''));
        $addType        = intval(input('addType'));
        $parentId       = intval(input('parentId'));
        $user_id        = $this->project_info['user_id'];

        if ($dataId == -1 && !empty($apiid) && !empty($value))
        {
            $value      = $value.'|@'.$apiid.'|@'.$addType.'|@'.$parentId.'|@'.$user_id;
            request()->post(['value'=>$value]);

            $id          = $this->questBaseEdit($this->apiUrl[request()->action()]);

            if ($id)
            {
                $this->success('添加成功','',$id);
            }
        }

        $id          = $this->questBaseEdit($this->apiUrl[request()->action()]);
        
        //接口调用
        if ($id)
        {
            $this->success('更新成功','',$id);
        }
        
        $this->error('更新失败');
    }

    //数据删除
    public function delDevapiParame()
    {
        $ids     = request()->param();
        $ids     = (isset($ids['ids']) && !empty($ids['ids'])) ? $ids['ids'] : $this->error('请选择要操作的数据');;
        $ids     = is_array($ids) ? implode($ids,',') : '';

        //请求参数
        $parame['uid']          = $this->uid;
        $parame['hashid']       = $this->hashid;
        $parame['id']           = $ids ;

        //请求地址
        if (!isset($this->apiUrl[request()->action()]) || empty($this->apiUrl[request()->action()]))
        $this->error('未设置接口地址');

        //接口调用
        $res       = $this->apiData($parame,$this->apiUrl[request()->action()]) ;
        $data      = $this->getApiData() ;

        if($res)
        {
            $this->success('删除成功',url('index')) ;
        }else{
            $this->error($this->getApiError()) ;
        }
    }

    //接口发布
    public function apiRelease()
    {
        $ids     = request()->param();
        $ids     = (isset($ids['ids']) && !empty($ids['ids'])) ? $ids['ids'] : $this->error('请选择要操作的数据');;
        $ids     = is_array($ids) ? implode($ids,',') : '';

        $data    = $this->startApiRelease($ids);

        if($data[0]){

            $this->success('发布成功') ;
        }else{
            
            $this->error($data[2]) ;
        }
    }

    //批量发布
    public function batchRelease()
    {
        //数据提交
        if (request()->isPost())
        {
            $apiids         = session('api_list_ids_to_release');
            $nums           = input('nums',1);
            
            if (empty($apiids) || !isset($apiids[$nums-1])) $this->success("success",'',['nums'=>100]);
            
            $apiid          = $apiids[$nums-1];

            unset($apiids[$nums-1]);
            session("api_list_ids_to_release",$apiids);

            $this->startApiRelease($apiid);

            $this->success("success",'',['nums'=>$nums+1]);
        }

        $apiids         = session('api_list_ids_to_release');
        $count          = count($apiids);

        $assignData['limits']          = round(100/($count+1),3);

        $this->assignData($assignData);
        //加载视图模板
        return view('batchrelease');
    }

    private function startApiRelease($apiid)
    {
        //请求参数
        $parame['uid']          = $this->uid;
        $parame['hashid']       = $this->hashid;
        $parame['id']           = $apiid;

        //请求地址
        if (!isset($this->apiUrl['apirelease']) || empty($this->apiUrl['apirelease']))
        $this->error('未设置接口地址');
        
        //接口调用
        $res       = $this->apiData($parame,$this->apiUrl['apirelease']);

        return [$res,$this->getApiData(),$this->getApiError()];
    }

    //接口文档导出
    public function exportApi()
    {
        //数据提交
        if (request()->isPost()) $this->startExportApi();

        $proid                  = $this->project_info['id'];

        //模块列表
        $search                 = [];
        $search['project_id']   = $proid;

        $parame             = [];
        $parame['uid']      = $this->uid;
        $parame['hashid']   = $this->hashid;
        $parame['page']     = input('page',1);
        $parame['search']   = !empty($search) ? json_encode($search) : '' ;

        $moduleList         = [];
        $res                = $this->apiData($parame,$this->apiUrl['module_index']);

        //初始化数据
        session("exportApi_moduleList",null);
        session("exportApi_moduleId",null);

        if ($res)
        {    
            $apiData        = $this->getApiData() ;
            $moduleList     = $apiData['lists'];
            $moduleId       = [];

            if (!empty($moduleList))
            {
                foreach ($moduleList as $key => $value)
                {
                    $moduleList[$key]['api_list'] = $this->get_api_list($value['id']);
                    $moduleId[]     = $value['id'];
                }
            }

            $assignData['project']          = $this->project_info;
            $assignData['moduleList']       = $moduleList;
            $assignData['modelid']          = 0;

            $envs                           = $this->project_info['envs'];
            $assignData['envs']             = $envs;

            $this->assignData($assignData);

            //生成静态页面
            $this->makr_api_doc('index.html',$this->fetch("api_index"));

            session("exportApi_moduleList",$moduleList);
            session("exportApi_moduleId",$moduleId);
        }

        $this->assign('limits',round(100/(count($moduleId)+1),3));

        //加载视图模板
        return view('exportapi');
    }

    private function makr_api_doc($fileName,$fienCon)
    {
        if (empty($fileName) || empty($fienCon))  return false;
        file_put_contents('./apidoc/web/'.$fileName,$fienCon);
        return true;
    }

    private function startExportApi()
    {
        $moduleList = session("exportApi_moduleList");
        $moduleId   = session("exportApi_moduleId");
        $nums       = input('nums',1);
        $proid      = $this->project_info['id'];

        if (!empty($moduleList))
        {
            if (empty($moduleId) || !isset($moduleId[$nums-1])) $this->success("success",'',['nums'=>100]);

            $modelid    = $moduleId[$nums-1];
            
            //销毁掉
            unset($moduleId[$nums-1]);
            session("exportApi_moduleId",$moduleId);

            //公共参数
            $public_parame                  = $this->get_devapi_parame(3,0);
            $assignData['public_parame']    = $public_parame[0];

            foreach ($moduleList as $key => $value)
            {
                if ($modelid  == $value['id'] && !empty($value['api_list']))
                {
                    foreach ($value['api_list'] as $ak => $av)
                    {
                        //生成详细的接口文档
                        $file_name = 'api_detail_'.$proid.'_'.$value['id'].'_'.$av['id'].'.html';
                        $av['module_name']      = $value['title'];

                        $request_parame     = $this->get_devapi_parame(1,$av['id']);
                        $back_parame        = $this->get_devapi_parame(2,$av['id']);
                        $back               = $this->toLevel($back_parame[0],'&nbsp;&nbsp;&nbsp;&nbsp;');
                        $envs               = $this->project_info['envs'];

                        $assignData['project']          = $this->project_info;
                        $assignData['moduleList']       = $moduleList;
                        $assignData['apiInfo']          = $av;
                        $assignData['request_parame']   = $request_parame[0];
                        $assignData['back_parame']      = $back;
                        $assignData['envs']             = $envs;

                        $this->assignData($assignData);

                        //生成静态页面
                        $this->makr_api_doc($file_name,$this->fetch("api_detail"));
                    }
                }
            }
        }

        $this->success("success",'',['nums'=>$nums+1]);
    }

    private function get_api_list($module_id = 0)
    {
        //获取接口列表数据
        $search                 = [];
        $search['module_id']    = intval($module_id);

        $parame             = [];
        $parame['uid']      = $this->uid;
        $parame['hashid']   = $this->hashid;
        $parame['page']     = input('page',1);
        $parame['search']   = !empty($search) ? json_encode($search) : '' ;

        //请求数据
        if (!isset($this->apiUrl['index']) || empty($this->apiUrl['index']))
        return [];

        $res                = $this->apiData($parame,$this->apiUrl['index']);
        $data               = $this->getApiData() ;
        $listData           = [];

        if ($res && isset($data['lists']) && !empty($data['lists'])){

            $listData       = $data['lists'];
        }

        return $listData;
    }

    //新增接口模块
    public function addModule()
    {
        //数据提交
        if (request()->isPost()) $this->update();

        //参数数据接收
        $param      = request()->param();

        //初始化表单模板 默认当前路由为唯一标识，自己可以自定义标识
        $formNode   = $this->tpl->showFormTpl($this->getTplData('','新增/编辑接口模块表单','form'),1);
        $formId     = isset($formNode['info']['id']) ? intval($formNode['info']['id']) : 0;
        $formTag    = isset($formNode['tags']) ? $formNode['tags'] : '';
        $formList   = isset($formNode['list']) ? $formNode['list'] : [];

        //数据详情
        $info                           = $this->getDetail(0);
        $info['project_id']             = $this->project_info['id'];
        $info['user_id']                = $this->project_info['user_id'];

        //页面数据
        $pageData                       = [];
        $pageData['isback']             = 0;
        $pageData['title1']             = '';
        $pageData['title2']             = '';
        $pageData['notice']             = [''];

        //渲染数据到页面模板上
        $assignData['formId']           = $formId;
        $assignData['formTag']          = $formTag;
        $assignData['formFieldList']    = $formList;
        $assignData['pageData']         = $pageData;
        $assignData['defaultData']      = $this->getDefaultParameData();
        $assignData['info']             = $info;
        $this->assignData($assignData);

        //加载视图模板
        return view('addedit');
    }

    //编辑接口模块
    public function editModule($id)
    {
        //数据提交
        if (request()->isPost()) $this->update();

        //参数数据接收
        $param      = request()->param();

        //初始化表单模板 默认当前路由为唯一标识，自己可以自定义标识
        $formNode   = $this->tpl->showFormTpl($this->getTplData('','新增/编辑接口模块表单','form'),0);
        $formId     = isset($formNode['info']['id']) ? intval($formNode['info']['id']) : 0;
        $formTag    = isset($formNode['tags']) ? $formNode['tags'] : '';
        $formList   = isset($formNode['list']) ? $formNode['list'] : [];

        //数据详情
        $info                           = $this->getDetail($id);
        $info['project_id']             = $this->project_info['id'];
        $info['user_id']                = $this->project_info['user_id'];

        //页面数据
        $pageData                       = [];
        $pageData['isback']             = 0;
        $pageData['title1']             = '';
        $pageData['title2']             = '';
        $pageData['notice']             = [''];
        
        //记录当前列表页的cookie
        cookie('__forward__',$_SERVER['REQUEST_URI']);
        cookie('__formtag__',$formTag);

        //渲染数据到页面模板上
        $assignData['formId']           = $formId;
        $assignData['formTag']          = $formTag;
        $assignData['formFieldList']    = $formList;
        $assignData['pageData']         = $pageData;
        $assignData['defaultData']      = $this->getDefaultParameData();
        $assignData['info']             = $info;
        $this->assignData($assignData);

        //加载视图模板
        return view('addedit');
    }

    //删除接口模块
    public function delModule()
    {
        $ids     = request()->param();
        $ids     = (isset($ids['ids']) && !empty($ids['ids'])) ? $ids['ids'] : $this->error('请选择要操作的数据');;
        $ids     = is_array($ids) ? implode($ids,',') : '';

        //请求参数
        $parame['uid']          = $this->uid;
        $parame['hashid']       = $this->hashid;
        $parame['id']           = $ids ;

        //请求地址
        if (!isset($this->apiUrl[request()->action()]) || empty($this->apiUrl[request()->action()]))
        $this->error('未设置接口地址');

        //接口调用
        $res       = $this->apiData($parame,$this->apiUrl[request()->action()]) ;
        $data      = $this->getApiData() ;

        if($res == true){

            $this->success('删除成功',Cookie('__forward__')) ;
        }else{
            
            $this->error($this->getApiError()) ;
        }
    }


    private function toLevel($category, $delimiter = '———', $parent_id = 0, $level = 0)
    {
        $data = [];
        
        if (!empty($category)) {
            foreach ($category as $v) {
                if ($v['parent_id'] == $parent_id) {
                    $v['level']     = $level + 1;
                    $v['margin']    = ($level + 1) * 15;
                    $v['delimiter'] = str_repeat($delimiter, $level);
                    $data[]         = $v;
                    $data           = array_merge($data,$this->toLevel($category,$delimiter,$v['id'],$v['level']));
                }
            }
        }

        return $data;
    }

    //基础API一键添加
    public function baseapi($id = 0)
    {
        //数据提交
        if (request()->isPost()) $this->addBaseApi();

        //参数数据接收
        $param      = request()->param();

        //初始化表单模板 默认当前路由为唯一标识，自己可以自定义标识
        $formNode   = $this->tpl->showFormTpl($this->getTplData('','基础API一键添加表单','form'),0);
        $formId     = isset($formNode['info']['id']) ? intval($formNode['info']['id']) : 0;
        $formTag    = isset($formNode['tags']) ? $formNode['tags'] : '';
        $formList   = isset($formNode['list']) ? $formNode['list'] : [];

        //数据详情
        $info                           = $this->getDetail($id);
        $info['module_id']              = isset($param['module_id']) ? $param['module_id'] : 0;

        //页面头信息设置
        $pageData['isback']             = 0;
        $pageData['title1']             = '';
        $pageData['title2']             = '';
        $pageData['notice']             = [];
        
        //记录当前列表页的cookie
        cookie('__forward__',$_SERVER['REQUEST_URI']);
        cookie('__formtag__',$formTag);

        //渲染数据到页面模板上
        $assignData['formId']           = $formId;
        $assignData['formTag']          = $formTag;
        $assignData['formFieldList']    = $formNode['list'];
        $assignData['info']             = $info;
        $assignData['defaultData']      = $this->getDefaultParameData();
        $assignData['pageData']         = $pageData;
        $this->assignData($assignData);

        //加载视图模板
        return view('addedit');
    }

    private function addBaseApi()
    {
        //表单数据
        $postData     = request()->param();

        //表单模板
        if(!$this->tpl->checkFormTpl($postData)) $this->error('表单模板数据不存在');

        $api_title    = (isset($postData['api_title']) && !empty($postData['api_title'])) ? trim($postData['api_title']) : '';
        if( empty($api_title) ) $this->error('接口名称不能为空');

        $api_url      = (isset($postData['api_url']) && !empty($postData['api_url'])) ? trim($postData['api_url']) : '';
        if( empty($api_url) ) $this->error('接口地址不能为空');

        $api_name     = (isset($postData['api_name']) && !empty($postData['api_name'])) ? implode(',', $postData['api_name']) : '';
        if( empty($api_name) ) $this->error('请选择基础接口');

        $module_id    = (isset($postData['module_id']) && !empty($postData['module_id'])) ? (int)$postData['module_id'] : 0;
        if( $module_id <= 0 ) $this->error('模块ID错误');

        $saveData                   = [];
        $saveData['uid']            = $this->uid;
        $saveData['hashid']         = $this->hashid;
        $saveData['api_title']      = $api_title;
        $saveData['api_name']       = $api_name;
        $saveData['api_url']        = $api_url;
        $saveData['module_id']      = $module_id;

        //接口调用
        $res       = $this->apiData($saveData,$this->apiUrl['addbaseapi']) ;
        
        if($res == true){

            $this->success('新增成功',Cookie('__forward__')) ;
        }else{
            
            $this->error($this->getApiError()) ;
        }
    }

    //导入
    public function import()
    {
        //数据提交
        if (request()->isPost()) $this->startImport();

        //表单模板
        $formData           = $this->getFormFields('Devapi/import',0);
        //数据详情
        $info['apiurl']                 = config('extend.import_api_url');
        $info['apiid']                  = config('extend.import_api_id');
        $info['apikey']                 = config('extend.import_api_key');
        $info['model_id']               = 0;
        $info['api_id']                 = 0;
        $info['module_id']              = intval(input('module_id'));

        //页面数据
        $pageData                       = [];
        $pageData['isback']             = 0;
        $pageData['title1']             = '';
        $pageData['title2']             = '';
        $pageData['notice']             = [''];

        //渲染数据到页面模板上
        $assignData['formId']           = isset($formData['info']['id']) ? intval($formData['info']['id']) : 0;
        $assignData['formFieldList']    = $formData['list'];
        $assignData['pageData']         = $pageData;
        $assignData['defaultData']      = $this->getDefaultParameData();
        $assignData['info']             = $info;
        $this->assignData($assignData);

        //加载视图模板
        return view('addedit');
    }

    private function startImport()
    {
        $data           = request()->param();

        $api_auth_id    = !empty($data['apiid']) ? $data['apiid'] : config('extend.import_api_id');
        $api_auth_key   = !empty($data['apikey']) ? $data['apikey'] : config('extend.import_api_key');
        $api_auth_url   = !empty($data['apiurl']) ? $data['apiurl'] : config('extend.import_api_url');

        if (empty($api_auth_id) || empty($api_auth_url) || empty($api_auth_key))
        $this->error('未设置授权信息');

        $apiRequest             = new \xnrcms\ApiRequest($api_auth_url, $api_auth_id, $api_auth_key);
        $parame                 = [];
        $parame['uid']          = $this->uid;
        $parame['hashid']       = $this->hashid;
        $parame['model_id']     = $data['model_id'];
        $parame['api_id']       = $data['api_id'];
        $apiName                = 'admin/'.humpToLine('DevapiModule').'/exportData';

        $backData               = $apiRequest->postData($parame,$apiName);
        $errorInfo              = $apiRequest->getError();
        if(empty($errorInfo)){
            $backInfo           = json_decode($backData,true);
            if (!empty($backInfo))
            {
                if ($backInfo['Code'] === "000000")
                {
                    $importData    = isset($backInfo['Data']['exportData'])?$backInfo['Data']['exportData']:'';
                    if (!empty($importData)){
                        //获取列表数据
                        $parame                 = [];
                        $parame['uid']          = $this->uid;
                        $parame['hashid']       = $this->hashid;
                        $parame['importData']   = $importData;
                        $parame['project_id']   = $this->project_info['id'];
                        $parame['module_id']    = $data['module_id'];

                        //请求数据
                        if (!isset($this->apiUrl[request()->action()]) || empty($this->apiUrl[request()->action()]))
                        $this->error('未设置接口地址');

                        $res                = $this->apiData($parame,$this->apiUrl[request()->action()]);
                        $data               = $this->getApiData() ;

                        if($res == true){

                            $this->success('导入成功',Cookie('__forward__')) ;
                        }else{
                            
                            $this->error($this->getApiError()) ;
                        }
                    }
                }else{
                    $this->error($backInfo['Msg']);
                }
            }
        }
        
        $this->error('未设置接口地址');
    }

    //临时固定写死项目信息，后期完善项目方面的管理
    private function getProjectInfo()
    {
        $api_sign_id        = config('dev_config.api_sign_id');
        $api_sign_key       = config('dev_config.api_sign_key');
        $product_name       = config('extend.xnrcms_name');
        $api_url            = config('dev_config.api_debug_url');
        $api_url            = !empty($api_url) ? explode("|", $api_url) : [];

        $envs               = 
        [
            ['name'=>'product','title'=>'生产环境','domain'=>isset($api_url[0]) ? $api_url[0] : ''],
            ['name'=>'develop','title'=>'开发环境','domain'=>isset($api_url[1]) ? $api_url[1] : ''],
        ];

        $project_info       =
        [
            'id'            => 1,
            'user_id'       => 1,
            'title'         => $product_name,
            'description'   => '',
            'envs'          => $envs,
            'allow_search'  => 1,
            'create_time'   => '2018-05-10 14:02:15',
            'update_time'   => '2018-05-10 14:02:15',
            'sort'          => 1,
            'api_id'        => $api_sign_id,
            'api_key'       => $api_sign_key,
        ];

        return $project_info;
    }
}
?>