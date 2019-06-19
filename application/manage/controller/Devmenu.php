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
 * Description:系统功能菜单管理
 */

namespace app\manage\controller;

use app\manage\controller\Base;

/**
 * 后台配置控制器
 */
class Devmenu extends Base
{
	private $apiUrl         = [];

    public function __construct()
    {
        parent::__construct();

        $this->tpl                    = new \xnrcms\DevTpl();
        $this->apiUrl['index']        = 'admin/Devmenu/listData';
        $this->apiUrl['edit']         = 'admin/Devmenu/detailData';
        $this->apiUrl['save_data']    = 'admin/Devmenu/saveData';
        $this->apiUrl['quickedit']    = 'admin/Devmenu/quickEditData';
        $this->apiUrl['del']          = 'admin/Devmenu/delData';
        $this->apiUrl['release']      = 'admin/Devmenu/releaseData';
    }

	/**
	 * 后台菜单首页
	 * @return none
	 */
	public function index()
	{
		//参数数据接收
        $param      = request()->param();

        //初始化模板
        $listNode   = $this->tpl->showListTpl($this->getTplData('','菜单列表','list'));
        $listId     = isset($listNode['info']['id']) ? intval($listNode['info']['id']) : 0;
        $listTag    = isset($listNode['tags']) ? $listNode['tags'] : '';

        //参数定义
        $menuid     = isset($param['menuid']) ? $param['menuid'] : 0;
        $page       = isset($param['page']) ? $param['page'] : 1;
        $search     = isset($param['search']) ? $param['search'] : [];
        $isTree     = 1;

        //页面操作功能菜单
        $topMenu    = formatMenuByPidAndPos($menuid,2, $this->menu);
        $rightMenu  = formatMenuByPidAndPos($menuid,3, $this->menu);

		//获取列表数据
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

        cache('DevmenuListData',$listData);

        if ($isTree === 1) {
            if ($this->tpl_name == 'kordeta')
            {
                $newData        = [];
                $nn             = 0;

                foreach ($listData as $key => $value) {
                    $newData[$key]['id']              = $value['id'];
                    $newData[$key]['pid']             = $value['pid'];
                    $newData[$key]['label']           = $value['title'];

                    if ($value['pid'] == 0 && $nn == 0) {
                        $newData[$key]['spread']      = true;
                        $nn ++;
                    }else{
                        $newData[$key]['spread']      = false;
                    }
                }

                $Tree          = new \xnrcms\DataTree($newData);
                $Tree->setConfig('cname','label');
                $Tree->setConfig('childName','children');

                $spreadId      = [];
                $listData      = $Tree->arrayTree();
                $menuData      = $Tree->toFormatTree();
                $lastid        = $this->spreadMenu($listData,$spreadId);
                $firstid       = isset($listData[0]['id']) ? $listData[0]['id'] : 0;
                $listData      = !empty($listData) ? json_encode($listData) : json_encode([]);

                $assignData['lastid']           = $lastid;
                $assignData['firstid']          = $firstid;
                $assignData['spreadId']         = !empty($spreadId) ? implode('|', $spreadId) : '';
                $assignData['menuData']         = $menuData;
            }else{
                $Tree          = new \xnrcms\DataTree($listData);
                $listData      = $Tree->toFormatTree();
            }
        }

		//页面数据
		$pageData						= [];
		$pageData['isback']     		= 0;
        $pageData['title1']     		= '开发 - 功能菜单管理 ';
        $pageData['title2']     		= '系统功能菜单添加/删除/编辑操作';
        $pageData['notice']     		= ['温馨提示：默认显展示所有菜单，点击减号收缩或点击加号展开'];

		//记录当前列表页的cookie
		cookie('__forward__',$_SERVER['REQUEST_URI']);
        cookie('__listtag__',$listTag);

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

		//加载视图模板
		return view();
	}

	/**
	 * 新增菜单
	 */
	public function add()
	{
		//数据提交
		if (request()->isPost()) $this->update();

		//菜单列表
		$Tree           				= new \xnrcms\DataTree(cache('DevmenuListData'));
		$menus 							= $Tree->toFormatTree();

		//数据详情
        $info                           = $this->getDetail(0);
		$info['id']						= 0;
		$info['pid']					= input('pid',0);
		$info['status']					= 1;

		//页面数据
		$pageData						= [];
		$pageData['isback']     		= 1;
        $pageData['title1']     		= '开发 - 功能菜单 ';
        $pageData['title2']     		= '功能菜单索引与管理';
        $pageData['notice']     		= ['星号项是必填项.','添加或者修改菜单时, 请注意选择对应的上级'];

		//渲染数据到页面模板上
		$assignData['pageData'] 		= $pageData;
		$assignData['menus'] 			= $menus;
		$assignData['info'] 			= $info;
		$this->assignData($assignData);

        if(request()->isAjax()) $this->success('请求成功',null,$this->fetch('addedit'));

		//加载视图模板
		return view('addedit');
	}

	/**
	 * 编辑配置
	 */
	public function edit($id = 0)
	{
		//数据提交
		if (request()->isPost()) $this->update();

		//数据详情
        $info                           = $this->getDetail($id);
		if(empty($info)) $this->error('数据获取失败',Cookie('__forward__'));
		
		$Tree           				= new \xnrcms\DataTree(cache('DevmenuListData'));
		$menus 							= $Tree->toFormatTree();
		
		$pageData						= [];
		$pageData['isback']     		= 1;
        $pageData['title1']     		= '开发 - 功能菜单 ';
        $pageData['title2']     		= '功能菜单索引与管理';
        $pageData['notice']     		= ['星号项是必填项.','添加或者修改菜单时, 请注意选择对应的上级'];

		//渲染数据到页面模板上
		$assignData['pageData'] 		= $pageData;
		$assignData['menus'] 			= $menus;
		$assignData['info'] 			= $info;
		$this->assignData($assignData);

        if(request()->isAjax()) $this->success('请求成功',null,$this->fetch('addedit'));

		//加载视图模板
		return view('addedit');
	}

	//提交表单
	protected function update()
	{
		if(request()->isPost())
		{
			//表单数据
            $param                      = request()->param();

	        //请求数据
	        if (!isset($this->apiUrl['save_data']) || empty($this->apiUrl['save_data']))
            $this->error('未设置接口地址');

            //接口参数
            $parame             = [];
            $parame['uid']      = $this->uid;
            $parame['hashid']   = $this->hashid;
            $parame['title']    = $param['title'];
            $parame['url']      = $param['url'];
            $parame['pid']      = $param['pid'];
            $parame['status']   = $param['status'];
            $parame['pos']      = $param['pos'];
            $parame['posttype'] = $param['posttype'];
            $parame['fsize']    = $param['fsize'];
            $parame['icon']     = $param['icon'];
            $parame['sort']     = $param['sort'];
            $parame['id']       = $param['id'];
            $parame['url_type']     = 0;
            $parame['open_type']    = 0;
            $parame['operation']    = "";


	        $res       = $this->apiData($parame,$this->apiUrl['save_data']) ;
	        $data      = $this->getApiData() ;

	        if($res){
	            $this->success($parame['id']  > 0 ? '更新成功' : '新增成功',Cookie('__forward__')) ;
	        }else{

	            $this->error($this->getApiError()) ;
	        }
		}

		$this->error('非法提交！');
	}

	/**
	 * 删除后台菜单
	 */
	public function del()
	{
		$ids			= request()->param();
		$ids 			= (isset($ids['ids']) && !empty($ids['ids'])) ? $ids['ids'] : [];

		//请求地址
        if (!isset($this->apiUrl[request()->action()])||empty($this->apiUrl[request()->action()])) 
        	$this->error('未设置接口地址');

		if ( empty($ids) ) $this->error('请选择要操作的数据!');

		$ids 				= is_array($ids) ? implode(',',$ids) : intval($ids);

		$parame 			= [];
		$parame['uid']		= $this->uid;
        $parame['hashid']	= $this->hashid;
		$parame['id']		= $ids;

        //接口调用
        $res       = $this->apiData($parame,$this->apiUrl[request()->action()]) ;
        $data      = $this->getApiData() ;

        if($res == true){

            $this->success('删除成功',url('index')) ;
        }else{
            
            $this->error($this->getApiError()) ;
        }
	}

	public function quickEdit()
	{
		//请求地址
        if (!isset($this->apiUrl[request()->action()])||empty($this->apiUrl[request()->action()])) 
        	$this->error('未设置接口地址');
        
        //接口调用
        if ($this->questBaseEdit($this->apiUrl[request()->action()])){

        	$this->success('更新成功',Cookie('__forward__'));
        }
        
        $this->error('更新失败');
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
            $res        = $this->apiData($parame,$apiUrl,false);
            $info       = $res ? $this->getApiData() : $this->error($this->getApiError());
        }

        return $info;
    }

    public function release()
    {
    	//请求地址
        if (!isset($this->apiUrl[request()->action()])||empty($this->apiUrl[request()->action()])) 
        $this->error('未设置接口地址');
        
        //请求参数
        $parame             = [];
        $parame['uid']      = $this->uid;
        $parame['hashid']   = $this->hashid;

        //接口调用
        $res       = $this->apiData($parame,$this->apiUrl[request()->action()]) ;
        $data      = $this->getApiData();
        
        if($res == true){
            $this->success('发布成功',url('index')) ;
        }else{
        	$this->error('发布失败');
        }
    }

    private function spreadMenu(&$listData = [],&$spreadId = [])
    {
        if (!empty($listData) && isset($listData[0])) {
            $spreadId[]   = (int)$listData[0]['id'];
            if (isset($listData[0]['children']) && !empty($listData[0]['children'])) {
                return $this->spreadMenu($listData[0]['children'],$spreadId);
            }

            return $listData[0]['id'];
        }
    }
}
?>