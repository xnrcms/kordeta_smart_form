<?php

namespace app\manage\controller;

use think\Controller;
use think\facade\Lang;

/**
 * 后台管理基类
 */
class Base extends Controller
{
	/**
	 * 后台控制器初始化
	 */
	public function __construct()
    {
        parent::__construct();

        $this->baseData         = [];

        //当前用户ID
        $this->uid              = is_login();

        //超级管理员ID
        $this->administratorid  = config('extend.administrator_id');

        //当前用户身份秘钥
        $this->hashid           = session('user_auth.user_hash');

        //是否需要自动登录
        $this->autoLogin();
        $this->delete_dev_file();
        
        //这里定义允许访问的IP
		/*if(false){
			// 检查IP地址访问
			if(!in_array(get_client_ip(),explode(',',C('ADMIN_ALLOW_IP')))) $this->error('403:禁止访问');
		}*/

		//菜单ID
        $this->menuid       = input('menuid',0);

		//当前登录用户详细资料
		$this->userInfo		= $this->userInfo();
        $this->groupId      = !empty($this->userInfo['group_id']) ? explode(',',$this->userInfo['group_id']):[0];

        //权限过滤
        if(!$this->checkMenu()) $this->error('未授权访问！');

		//当前用户拥有的所有菜单权限
		$this->menu 			= $this->ininMenu();
        $this->menu             = $this->formatAuthMenu($this->menu,$this->userInfo['rules'],$this->uid,$this->menuid);

        /*$Tree          = new \xnrcms\DataTree($this->menu);
        $listData      = $Tree->arrayTree();
        print_r($listData);exit();*/

        $this->extends_param    = '';
        $this->isdev            = config('extend.is_dev');
        $this->tpl_name         = config('template.tpl_name');
	}

    public function ininMenu()
    {
        $devMenu                = get_release_data('menu.data.project_id=0','menu',1);
        $sysMenu                = get_release_data('menu.data.project_id=1','menu',1);

        return array_merge($devMenu,$sysMenu);
    }

    /**
     * 显示分类树，仅支持内部调
     * @param  array $tree 分类树
     */
    public function tree($tree = null,$leve=1,$type='auth')
    {
        $leve ++ ;
        $view   = 'public/extend/'.(!empty($type) ? $type : 'auth').'_tree';
        return $this->fetch($view, ['level'=>$leve,'tree'=>$tree]);
    }

    /**
     * 根据用户权限过滤菜单
     * @param  array           $arr    需要处理的菜单数据
     * @param  array/string    $menuid 用户拥有的菜单ID
     * @param  integer         $uid    用户ID
     * @return array                   处理后的菜单数据
     */
    private function formatAuthMenu($arr=[],$menuid=[],$uid=0)
    {
        if (empty($arr) || $uid <= 0) return [];

        foreach ($arr as $key => $value) {

            if ($value['status'] != 1) {
                unset($arr[$key]);
                continue;
            }

            //格式化fsize
            $fwidth         = 800;
            $fheight        = 550;
            if(!empty($value['fsize'])){

                $fsizeArr       = explode('*',$value['fsize']);

                $fwidth         = intval($fsizeArr[0]);
                $fheight        = intval($fsizeArr[1]);
            }

            $arr[$key]['fwidth']    = $fwidth;
            $arr[$key]['fheight']   = $fheight;

        }

        if ($uid === 1) {
            
            return $arr;
        }

        $menuid             = (!empty($menuid) && is_string($menuid)) ? explode(',',$menuid) : $menuid;

        foreach ($arr as $key => $value) {

            if (!in_array($value['id'],$menuid)) {

                unset($arr[$key]);
            }
            else{

                //格式化fsize
                $fwidth         = 800;
                $fheight        = 550;
                if(!empty($value['fsize'])){

                    $fsizeArr       = explode('*',$value['fsize']);

                    $fwidth         = intval($fsizeArr[0]);
                    $fheight        = intval($fsizeArr[1]);
                }

                $arr[$key]['fwidth']    = $fwidth;
                $arr[$key]['fheight']   = $fheight;
            }
        }

        return $arr;
    }

    /**
     * 获取指定级别的菜单项
     * @param  array    $arr 需要处理的菜单数据
     * @param  integer  $pid 菜单上级ID
     * @return array         处理后的菜单数据
     */
    public function formatMenu($arr=[],$pid=0)
    {
        $menu       = [];

        if (!empty($arr))
        {
            foreach ($arr as $key => $value)
            {
                if ($value['pid'] == $pid) $menu[]     = $value;
            }
        }

        return $menu;
    }

    /**
     * 检测是否有权限执行
     * @return bool 是否有权限
     */
    private function checkMenu()
    {
        if ( isset($this->userInfo['rules']) && !empty($this->userInfo['rules']) )
        {
            if (request()->controller() === 'Index') return true;
            if ($this->userInfo['rules'] === 'all' || $this->userInfo['is_super'] == 1)  return true;
            if (!is_string($this->userInfo['rules'])) return false;

            $rules          = explode(',',$this->userInfo['rules']);
            if ($this->menuid >0 && in_array($this->menuid,$rules)) return true;
        }

        return false;
    }

	/**
	 * 获取用户信息
	 */
	final protected function userInfo()
    {
		$parame 			= [];
		$parame['uid']		= $this->uid;
        $parame['hashid']	= $this->hashid;
        $parame['id']       = $this->uid;

        $res 				= $this->apiData($parame,'api/User/userDetail');

        $userInfo			= $res  ? $this->getApiData() : [];

        if (is_string($this->getApiError()) && $this->getApiError() == 'Token过期')
        {
            session('user_auth', null);
            session('user_auth_sign', null);
            session('apidoc_user_auth', null);
            session('apidoc_user_auth_sign', null);
            session('api_uid',null);
            session('api_hashid',null);
            session('[destroy]');
            cookie(null);
            $this->goLogin();
        }

        if (!empty($userInfo))
        {	
			$userInfo['nickname']	 = empty($userInfo['nickname'])?$userInfo['username']:$userInfo['nickname'];
			$userInfo['last_time']	 = !empty($userInfo['last_login_time']) ? $userInfo['last_login_time'] : '/';
			$userInfo['reg_time']	 = !empty($userInfo['create_time']) ? $userInfo['create_time'] : '/';
			$userInfo['update_time'] = !empty($userInfo['update_time']) ? $userInfo['update_time'] : '/';
        }

		return $userInfo;
	}

	/**
	 * 执行登录跳转
	 */
	protected function goLogin()
	{
		if (request()->isAjax()) $this->error('您还没有登录!',url('Login/index'));
        if (isset($_SERVER['HTTP_REFERER'])) exit('<script>top.location.href="'.url('Login/index').'"</script>');
        header("Location:".url('Login/index'));exit();
    }

	//执行自动登录
	protected function autoLogin()
    {
        //用户信息存在不用登录
        if (!empty($this->uid) && $this->uid > 0 && !empty($this->hashid)) return true;

		$cookie_username	= cookie(md5('admin_username' . config('extends.uc_auth_key')));
		$cookie_password	= cookie(md5('admin_password' . config('extends.uc_auth_key')));

		if($cookie_username && $cookie_password){

			$username	= string_encryption_decrypt($cookie_username,'DECODE');
			$password	= string_encryption_decrypt($cookie_password,'DECODE');
			$username 	= string_safe_filter($username);

			/* 调用登录接口登录 */
			$parame['username']		= $username;
			$parame['password']		= $password;
			$parame['utype']		= 1;
			$parame['jpushid']		= '';

			$requestRes 			= apiReq($parame,'api/User/ulogin');

			$backData 				= $requestRes[0];
			$errorInfo				= $requestRes[1];

			if(empty($errorInfo)){

				$backData		= json_decode($backData,true);

				if ($backData['Code'] == '200') {
                    //指定cookie保存30天时间
                    cookie(md5('admin_username'.config('extend.uc_auth_key')),string_encryption_decrypt($username,'ENCODE'),2592000);
                    cookie(md5('admin_password'.config('extend.uc_auth_key')),string_encryption_decrypt($password,'ENCODE'),2592000);
					
					/* 记录登录SESSION和COOKIES */
					$auth = [
			            'uid'             => $backData['Data']['uid'],
                        'user_hash'       => $backData['Data']['hashid'],
					];

					session('user_auth', $auth);
					session('user_auth_sign', data_auth_sign($auth));

                    return true;
				}

			}
		}

        session('[destroy]');
        cookie(null);

        $this->goLogin();
	}

    public function pageData($data = [])
    {
        if (isset($data['total']) && isset($data['limit']))
        {
            $page           = new \xnrcms\Page($data['total'], $data['limit']);

            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $page->setConfig('header','');

            return trim($page->show());
        }

        return '';
    }

    protected function apiData($parame,$apiUrl)
    {
    	$this->setApiData([]);
        $this->setApiError([]);

        //请求参数或则接口地址为空，直接返回FALSE
    	if (empty($parame) || empty($apiUrl)) return false;

    	$requestRes 			= apiReq($parame,$apiUrl);

    	$backData 				= $requestRes[0];
        $errorInfo				= $requestRes[1];

        if(empty($errorInfo)){

            $backData			= is_array($backData) ? $backData : json_decode($backData,true);
            $backData           = is_array($backData) ? $backData : json_decode($backData,true);

            if (!isset($backData['Code'])){

        		$this->setApiError('接口报错'); 
        		return false;
        	}

            if ($backData['Code'] === '200'  && isset($backData['Data'])) {
            	
            	$this->setApiData($backData['Data']);
            	return true;
            }else{

            	$this->setApiError($backData['Msg']);
       			return false;
            }
        }

        $this->setApiError($errorInfo['Msg']);
        return false;
    }

    private function setApiData($data=[])
    {
    	$this->apiData 		= $data;
    }

    private function setApiError($msg='')
    {
    	$this->apiError 	= $msg;
    }

    protected function getApiError()
    {
    	return $this->apiError;
    }

    protected function getApiData()
    {

    	return $this->apiData;
    }

    /**
     * [quickEdit 快捷编辑]
     * @return [json] [反馈信息]
     */
    protected function questBaseEdit($apiUrl='')
    {
        $fieldName      = input('post.fieldName');
        $dataId         = intval(input('post.dataId'));
        $value          = trim(input('post.value'));

        if (empty($apiUrl)) $this->error('更新失败[apiUrl]！');
        if (empty($fieldName)) $this->error('更新失败[fieldName]！');
        if ($dataId == 0) $this->error('更新失败[dataId]！');

        $parame                 = [];
        $parame['uid']          = $this->uid;
        $parame['hashid']       = $this->hashid;
        $parame['id']           = $dataId;
        $parame['fieldName']    = $fieldName;
        $parame['updata']       = $value;

        $res                    = $this->apiData($parame,$apiUrl);

        $data                   = $res  ? $this->getApiData() : $this->error($this->getApiError());

        return (isset($data['id']) && $data['id'] > 0) ? $data['id'] : 0;
    }

    public function assignData($data = [])
    {
        $baseData['userInfo']       = $this->userInfo;
        $baseData['uid']            = $this->uid;
        $baseData['hashid']         = $this->hashid;
        $baseData['menuid']         = $this->menuid;
        $baseData['menu']           = $this->menu;
        $baseData['extends_param']  = $this->extends_param;
        $baseData['isdev']          = $this->isdev;
        $baseData['thisObj']        = $this;
        $baseData['listId']         = 0;
        $baseData['formId']         = 0;
        $baseData['isTree']         = 0;
        $baseData['pageData']       = ['isback'=>0,'title1'=>'','title2'=>'','notice'=>''];
        $backData['defaultData']    = [];
        $baseData['threePartyplug'] = ['bdmap'=>0,'gdmap'=>0,'editor'=>0,'image'=>0,'images'=>0,'file'=>0];
        $baseData['tplName']        = config('template.tpl_name');

        $assignData                 = !empty($data) ? array_merge($baseData,$data) : $baseData;
        $this->assign($assignData);
    }

    public function getSearchParame($param = [])
    {
        $search             = [];
        if (!empty($param))
        {
            foreach ($param as $key => $value)
            {
                if (strpos('#'.$key, 'data_search_') === 1)
                {
                    $search[str_replace('data_search_', '', $key)]   = $value;
                }
            }
        }

        return $search;
    }

    /**
     * 扩展枚举，布尔，单选，复选等数据选项数据
     * @return array 默认数据
     */
    protected function getDefaultParameData()
    {
        return [];
    }

    protected function getTplData($tag = '', $title = '', $type = '')
    {
        if (empty($title) || empty($type) || !in_array($type, ['form','list']))  return [];

        $cname      = get_devtpl_tag($tag);
        $data       = get_release_data($cname,$type);

        if (is_dev())
        {
            if (!empty($data))
            {
                foreach ($data as $value)
                {
                    if (!empty($value))
                    {
                        foreach ($value as $v2)
                        {
                            if ($v2['pid'] == 0 && $title == $v2['title']) return $data;
                        }
                    }
                }
            }

            //初始化数据
            $signData                   = [];
            $signData['uid']            = $this->uid;
            $signData['hashid']         = $this->hashid;
            $signData['title']          = $title;
            $signData['cname']          = $cname;

            $apiUrl                     = [
                'list'  => 'admin/Devlist/initListData',
                'form'  => 'admin/Devform/initFormData'
            ];

            if (isset($apiUrl[$type]) && !empty($apiUrl[$type]))
            {
                //请求数据
                $res        = $this->apiData($signData,$apiUrl[$type]) ;
                $devtpl     = $this->getApiData() ;
                
                if ($res)
                {
                    $data   = get_release_data($cname,$type);
                }
            }
        }

        return $data;
    }

    private function delete_dev_file()
    {
        if (is_dev())  return;
        
        $path       = 
        [
            'admin',
            'common/model/Devapi.php',
            'common/model/DevapiModule.php',
            'common/model/DevapiParame.php',
            'common/model/Devform.php',
            'common/model/Devlist.php',
            'common/release/menu/7e9dad3162161c91019bf776f7643718.php',
            'common/tpl/ApiTPLC.php',
            'common/tpl/ApiTPLH.php',
            'common/tpl/ApiTPLM.php',
            'manage/controller/Devapi.php',
            'manage/controller/Devconfig.php',
            'manage/controller/Devfile.php',
            'manage/controller/Devform.php',
            'manage/controller/Devlist.php',
            'manage/controller/Devmenu.php',
            'manage/controller/Devmenu.php',
            'manage/tpl',
            'manage/view/devapi',
            'manage/view/devconfig',
            'manage/view/devfile',
            'manage/view/devform',
            'manage/view/devlist',
            'manage/view/devproject',
        ];
        
        foreach ($path as $value)
        {
            if (is_dir(\Env::get('APP_PATH') . $value) || is_file(\Env::get('APP_PATH') . $value)) {
                //delFile(\Env::get('APP_PATH') . $value,false);
                //delFile(\Env::get('APP_PATH') . $value,true);
            }
        }
    }
}