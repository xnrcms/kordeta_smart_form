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
 * Author: xnrcms<562909771@qq.com><562909771@qq.com>
 * Date: 2018-04-09
 * Description:用户控制器
 */

namespace app\manage\controller;

use app\manage\controller\Base;

class User extends Base
{
    private $apiUrl         = [];

    public function __construct()
    {
        parent::__construct();

        $this->tpl                                  = new \xnrcms\DevTpl();
        $this->apiUrl['index']                      = 'api/User/listData';
        $this->apiUrl['edit']                       = 'api/User/userDetail';
        $this->apiUrl['save_data']                  = 'api/User/saveData';
        $this->apiUrl['quickedit']                  = 'api/User/quickEditData';
        $this->apiUrl['del']                        = 'api/User/delData';
        $this->apiUrl['quickEditUserDetailData']    = 'api/User/quickEditUserDetailData';
        $this->apiUrl['setUserPrivilege']           = 'api/User/setUserPrivilege';
        $this->apiUrl['updatepassword']             = 'api/User/updatePasswordByOld';
    }

    //所有用户，不按用户组划分
    public function alluser()
    {
        $arr['listid']             = 'index';
        $arr['gid']                = 0;
        $arr['isback']             = 0;
        $arr['title1']             = '用户-用户管理';
        $arr['title2']             = '网站系统用户-用户索引与管理';
        $arr['notice']             = ['用户-用户列表管理, 对系统用户进行维护.'];

        return $this->index($arr);
    }

    //管理员
    public function adminuser()
    {
        $arr['listid']             = 'index';
        $arr['gid']                = 1;
        $arr['isback']             = 0;
        $arr['title1']             = '用户-管理员管理';
        $arr['title2']             = '网站系统用户-管理员索引与管理';
        $arr['notice']             = ['用户-管理员列表管理, 对系统管理员进行维护.'];

        return $this->index($arr);
    }

    //会员列表
    public function homeuser()
    {
        $arr['listid']             = 'index';
        $arr['gid']                = 2;
        $arr['isback']             = 0;
        $arr['title1']             = '用户-会员管理';
        $arr['title2']             = '网站系统用户-会员索引与管理';
        $arr['notice']             = ['用户-会员列表管理, 对系统会员进行维护.'];

        return $this->index($arr);
    }

    //代理列表
    public function agentuser()
    {
        $arr['listid']             = 'index';
        $arr['gid']                = 3;
        $arr['isback']             = 0;
        $arr['title1']             = '用户-代理管理';
        $arr['title2']             = '网站系统用户-代理索引与管理';
        $arr['notice']             = ['用户-代理列表管理, 对系统代理进行维护.'];

        return $this->index($arr);
    }

	//列表页面
	private function index($arr)
    {
        //参数数据接收
        $param      = request()->param();

        //初始化模板
        $listNode   = $this->tpl->showListTpl($this->getTplData($arr['listid'],'会员列表','list'));
        $listId     = isset($listNode['info']['id']) ? intval($listNode['info']['id']) : 0;
        $listTag    = isset($listNode['tags']) ? $listNode['tags'] : '';

        //参数定义
        $menuid     = isset($param['menuid']) ? $param['menuid'] : 0;
        $page       = isset($param['page']) ? $param['page'] : 1;
        $search     = $this->getSearchParame($param);
        $isTree     = 0;

        //页面操作功能菜单
        $topMenu    = formatMenuByPidAndPos($menuid,2, $this->menu);
        $rightMenu  = formatMenuByPidAndPos($menuid,3, $this->menu);

        $search['group_id'] = $arr['gid'];

        //获取列表数据
        $parame             = [];
        $parame['uid']      = $this->uid;
        $parame['hashid']   = $this->hashid;
        $parame['page']     = $page;
        $parame['search']   = !empty($search) ? json_encode($search) : '' ;

        //请求数据
        if (!isset($this->apiUrl['index']) || empty($this->apiUrl['index']))
        $this->error('未设置接口地址');

        $res                = $this->apiData($parame,$this->apiUrl['index']);
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

        //页面头信息设置
        $pageData['isback']             = $arr['isback'];
        $pageData['title1']             = $arr['title1'];
        $pageData['title2']             = $arr['title2'];
        $pageData['notice']             = $arr['notice'];

        $assignData['testData']         = json_encode(["id"=>1,"name"=>"wangyuanq"]);
        //渲染数据到页面模板上
        $assignData['isTree']           = $isTree;
        $assignData['_page']            = $p;
        $assignData['_total']           = $total;
        $assignData['topMenu']          = $topMenu;
        $assignData['rightMenu']        = $rightMenu;
        $assignData['listId']           = $listId;
        $assignData['listNode']         = $listNode;
        $assignData['listData']         = $listData;
        $assignData['pageData']         = $pageData;
        $this->assignData($assignData);

        //记录当前列表页的cookie
        cookie('__forward__',$_SERVER['REQUEST_URI']);
        cookie('__listtag__',$listTag);

        //异步请求处理
        if(request()->isAjax()){
            if ($total <= 0) {
                echo json_encode(['code'=>1,'msg'=>'暂无数据','count'=>0,'data'=>[]]);exit();
            }else{
                echo json_encode(['code'=>0,'msg'=>'请求成功','count'=>$total,'data'=>$listData]);exit();
            }
            echo json_encode(['listData'=>$this->fetch('public/list/listData'),'listPage'=>$p]);exit();
        }

        //加载视图模板
        return view('index');
	}

	//新增页面
	public function add()
    {
		//数据提交
        if (request()->isPost()) $this->update();

        //参数数据接收
        $param      = request()->param();

        //初始化表单模板 默认当前路由为唯一标识，自己可以自定义标识
        $formNode   = $this->tpl->showFormTpl($this->getTplData('addedit','新增/编辑用户表单','form'),0);
        $formId     = isset($formNode['info']['id']) ? intval($formNode['info']['id']) : 0;
        $formTag    = isset($formNode['tags']) ? $formNode['tags'] : '';
        $formList   = isset($formNode['list']) ? $formNode['list'] : [];

        //数据详情
        $info                           = $this->getDetail(0);

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
        $assignData['formFieldList']    = $formList;
        $assignData['info']             = $info;
        $assignData['defaultData']      = $this->getDefaultParameData();
        $assignData['pageData']         = $pageData;
        $this->assignData($assignData);

        //加载视图模板
        return view('addedit');
	}

	//编辑页面
	public function edit($id = 0)
    {
		//数据提交
        if (request()->isPost()) $this->update();

        //参数数据接收
        $param      = request()->param();

        //初始化表单模板 默认当前路由为唯一标识，自己可以自定义标识
        $formNode   = $this->tpl->showFormTpl($this->getTplData('addedit','新增/编辑用户表单','form'),1);
        $formId     = isset($formNode['info']['id']) ? intval($formNode['info']['id']) : 0;
        $formTag    = isset($formNode['tags']) ? $formNode['tags'] : '';
        $formList   = isset($formNode['list']) ? $formNode['list'] : [];

        //数据详情
        $info                           = $this->getDetail($id);

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
        $assignData['formFieldList']    = $formList;
        $assignData['info']             = $info;
        $assignData['defaultData']      = $this->getDefaultParameData();
        $assignData['pageData']         = $pageData;
        $this->assignData($assignData);

        //加载视图模板
        return view('addedit');
	}

    //数据删除
    public function del()
    {
        //参数数据接收
        $param   = request()->param();
        $ids     = (isset($param['ids']) && !empty($param['ids'])) ? $param['ids'] : $this->error('请选择要操作的数据');;
        $ids     = is_array($ids) ? implode($ids,',') : '';

        //请求参数
        $parame                 = [];
        $parame['uid']          = $this->uid;
        $parame['hashid']       = $this->hashid;
        $parame['id']           = $ids ;

        //请求地址
        if (!isset($this->apiUrl[request()->action()]) || empty($this->apiUrl[request()->action()]))
        $this->error('未设置接口地址');

        //接口调用
        $res       = $this->apiData($parame,$this->apiUrl[request()->action()]);
        $data      = $this->getApiData() ;

        if($res == true){

            $this->success('删除成功',Cookie('__forward__'));
        }else{
            
            $this->error($this->getApiError());
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

    //密码修改
    public function updatePassword($id = 0)
    {
        //数据提交
        if (request()->isPost()) $this->updatePasswordByOld();

        //参数数据接收
        $param      = request()->param();

        //初始化表单模板 默认当前路由为唯一标识，自己可以自定义标识
        $formNode   = $this->tpl->showFormTpl($this->getTplData('','用户密码修改表单','form'),1);
        $formId     = isset($formNode['info']['id']) ? intval($formNode['info']['id']) : 0;
        $formTag    = isset($formNode['tags']) ? $formNode['tags'] : '';
        $formList   = isset($formNode['list']) ? $formNode['list'] : [];

        $id         = (int)$id <= 0 ? $this->uid : (int)$id;

        //数据详情
        $info                           = [];
        $info['safeid']                 = string_encryption_decrypt($id);

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
        $assignData['formFieldList']    = $formList;
        $assignData['info']             = $info;
        $assignData['defaultData']      = $this->getDefaultParameData();
        $assignData['pageData']         = $pageData;
        $this->assignData($assignData);

        //加载视图模板
        return view('addedit');
    }

    //处理提交新增或编辑的数据
    private function update()
    {
        //表单数据
        $postData                   = request()->param();

        //表单模板
        if(!$this->tpl->checkFormTpl($postData)) $this->error('表单模板数据不存在');

        //接口数据
        $signData                   = $this->tpl->getFormTplData($postData);
        $signData['uid']            = $this->uid;
        $signData['hashid']         = $this->hashid;
        $signData['group_id']       = 0;
        $signData['mark']           = '';

        //请求数据
        if (!isset($this->apiUrl['save_data'])||empty($this->apiUrl['save_data'])) 
        $this->error('未设置接口地址');

        $res       = $this->apiData($signData,$this->apiUrl['save_data']) ;
        $data      = $this->getApiData() ;

        if($res){

            $this->success($signData['id']  > 0 ? '更新成功' : '新增成功',Cookie('__forward__')) ;
        }else{

            $this->error($this->getApiError()) ;
        }
    }
    
    private function updatePasswordByOld()
    {
        //表单数据
        $postData                   = request()->param();

        //表单模板
        if(!$this->tpl->checkFormTpl($postData)) $this->error('表单模板数据不存在');

        //接口数据
        $signData                   = $this->tpl->getFormTplData($postData);
        $signData['uid']            = $this->uid;
        $signData['hashid']         = $this->hashid;
 
        //请求数据
        if (!isset($this->apiUrl[request()->action()])||empty($this->apiUrl[request()->action()])) 
        $this->error('未设置接口地址');

        $res       = $this->apiData($signData,$this->apiUrl[request()->action()]) ;
        $data      = $this->getApiData() ;

        if($res)
        {
            $this->success('修改成功',Cookie('__forward__')) ;
        }else{

            $this->error($this->getApiError()) ;
        }
    }

    //获取数据详情
    private function getDetail($id = 0)
    {
        $info           = [];

        if ($id > 0)
        {
            //请求参数
            $parame             = [];
            $parame['uid']      = $this->uid;
            $parame['hashid']   = $this->hashid;
            $parame['id']       = $id ;

            //请求数据
            $apiUrl     = (isset($this->apiUrl[request()->action()]) && !empty($this->apiUrl[request()->action()])) ? $this->apiUrl[request()->action()] : $this->error('未设置接口地址');
            $res        = $this->apiData($parame,$apiUrl,false);
            $info       = $res ? $this->getApiData() : $this->error($this->getApiError());
        }

        return $info;
    }

    //扩展枚举，布尔，单选，复选等数据选项
    protected function getDefaultParameData()
    {
        $defaultData['parame']   = [];

        return $defaultData;
    }

    //用户授权
    public function setUserPrivilege()
    {
        if(request()->isPost())
        {
            $apiTag         = 'setUserPrivilege';
            //请求地址
            if (!isset($this->apiUrl[$apiTag]) || empty($this->apiUrl[$apiTag]))
            $this->error('未设置接口地址');

            //参数数据接收
            $param          = request()->param();
            $id             = isset($param['id']) ? intval($param['id']) : 0;
            $menu_id        = isset($param['rules']) ? $param['rules'] : [];
            $group_id       = isset($param['group_id']) ? $param['group_id'] : [];
            
            if ($id <= 0) $this->error('更新失败！');

            $menu_id                = !empty($menu_id) ? implode(',',$menu_id) : '-1';
            $group_id               = !empty($group_id) ? implode(',',$group_id) : '-1';

            $parame                 = [];
            $parame['uid']          = $this->uid;
            $parame['hashid']       = $this->hashid;
            $parame['id']           = $id;
            $parame['group_id']     = $group_id;
            $parame['menu_id']      = $menu_id;

            $res                    = $this->apiData($parame,$this->apiUrl[$apiTag]);

            $res ? $this->success('授权成功',Cookie('__forward__')) : $this->error($this->getApiError());
        }

        //请求地址
        if (!isset($this->apiUrl['edit']) || empty($this->apiUrl['edit'])) $this->error('未设置接口地址');

        //请求参数
        $parame             = [];
        $parame['uid']      = $this->uid;
        $parame['hashid']   = $this->hashid;
        $parame['id']       = input('id');

        //接口调用
        $res                = $this->apiData($parame,$this->apiUrl['edit']) ;
        $info               = $res ? $this->getApiData() : [];

        $userAuth           = empty($info['urules']) ? array() : explode(',',$info['urules']) ;

        /**获取所有的菜单权限*/
        $Tree                           = new \xnrcms\DataTree($this->menu);
        $menuList                       = $Tree->arrayTree();

        $authList           =[];

        if (!empty($menuList)) {
            
            foreach ($menuList as $key => $value) {
                
                if (($key+3)%3 == 0) {
                    $authList['left'][]     = $value;
                }

                if (($key+3)%3 == 1) {
                    $authList['middle'][]   = $value;
                }

                if (($key+3)%3 == 2) {
                    $authList['right'][]    = $value;
                }
            }
        }

        //用户组列表 接口调用
        $parame                 = [];
        $parame['uid']          = $this->uid;
        $parame['hashid']       = $this->hashid;
        $parame['id']           = input('id');
        $res                    = $this->apiData($parame,'api/UserGroup/glistData') ;
        $glist                  = $res ? $this->getApiData() : [];

        //页面头信息设置
        $pageData['isback']     = 1;
        $pageData['title1']     = '权限';
        $pageData['title2']     = '用户专有权限设置';
        $pageData['notice']     = ['请勾选对应的操作节点',];

        //渲染数据到页面模板上
        $assignData['authList']         = $authList;
        $assignData['userAuth']         = $userAuth;
        $assignData['info']             = $info;
        $assignData['glist']            = $glist;
        $assignData['pageData']         = $pageData;
        $this->assignData($assignData);

        //记录当前列表页的cookie
        cookie('__forward__',$_SERVER['REQUEST_URI']);

        //加载视图模板
        return view();
    }
}
?>