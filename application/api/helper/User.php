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

class User extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'user_center';
	
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
		$modelParame['MainTab']		= 'user_center';

		//主表名称，可以为空，默认为main
		$modelParame['MainAlias']	= 'main';

		//主表待查询字段，可以为空，默认全字段
		$modelParame['MainField']	= ['id','username','email','mobile','status','reg_ip','create_time'];

		//定义关联查询表信息，默认是空数组，为空时为单表查询,格式必须为一下格式
		//Rtype :`INNER`、`LEFT`、`RIGHT`、`FULL`，不区分大小写，默认为`INNER`。
		$RelationTab				= [];
        $RelationTab['user_detail'] = [
            'Ralias'=>'ud','Ron'=>'ud.uid=main.id','Rtype'=>'LEFT','Rfield'=>['uid as ugauid','nickname','face','mark']
        ];
        /*$RelationTab['user_center'] = [
            'Ralias'=>'uc1','Ron'=>'uc1.id=main.ownerid','Rtype'=>'LEFT','Rfield'=>['username as oname']
        ];*/

		$modelParame['RelationTab']	= $RelationTab;

        $parame['ownerid']          = $this->getOwnerId();
        //接口数据
        $modelParame['apiParame']   = $parame;

		//检索条件 需要对应的模型里面定义查询条件 格式为formatWhere...
		$modelParame['whereFun']	= 'formatWhereDefault';

		//排序定义
		$modelParame['order']		= 'main.id desc';
		
		//数据分页步长定义
		$modelParame['limit']		= $this->apidoc == 2 ? 1 : 10;

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
            foreach ($data as $key => $value)
            {
                $data[$key]['gtitle']   = $this->getUserGroupTitle($value['id']);
                $data[$key]['oname']    = !empty($value['oname']) ? $value['oname'] : '系统用户';
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

        //验证两次密码输入是否一致
        if (!empty($parame['password']) && md5($parame['password']) !== md5($parame['repeatpwd']))
        return ['Code' => '200012', 'Msg'=>lang('200012')];

        //是否设置了分组
        $gid                        = isset($parame['group_id']) ? intval($parame['group_id']) : 0;

        //更新
        if ($id <= 0)
        {
            if (empty($parame['password']))
            return ['Code' => '200002', 'Msg'=>lang('200002')];

            if (empty($parame['password']))
            return ['Code' => '200010', 'Msg'=>lang('200010')];

            if (md5($parame['password']) !== md5($parame['repeatpwd']))
            return ['Code' => '200012', 'Msg'=>lang('200012')];
        }
        
        $uid    = $id > 0 ? model('user_center')->saveData($parame) : model('user_center')->register($parame);

        //更新成功
        if ($uid > 0)
        {
            if ($gid > 0 && $gid != 3) model('user_group_access')->setGroupAccess($uid,[$gid]);
            if ($gid == 3) model('user_group_access')->delGroupAccessByUid($uid);

            $data['id']                 = intval($uid);

            //更新详细信息
            $detailData                 = [];
            $detailData['id']           = $id;
            $detailData['uid']          = $uid;
            $detailData['update_time']  = time();
            $detailData['mark']         = isset($parame['mark']) ? trim($parame['mark']) : '';
            
            model('user_detail')->saveData($detailData);

            return ['Code' => '200', 'Msg'=>lang('message_save_success'),'Data'=>$data];
        }

        return $this->userMessage($uid);
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

    	$info 				= $dbModel->getOneById($parame['id']);

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
    	$dbModel			= model('user_center');

        //定义可以快速修改的字段信息
        $allow            = ['nickname','status','mobile'];
        if (!in_array($parame['fieldName'],$allow))
        return ['Code' => '120025', 'Msg'=>lang('120025',[$parame['fieldName']])];

    	$info 				= $dbModel->updateById($parame['id'],[$parame['fieldName']=>$parame['updata']]);

    	if (!empty($info)) {

    		return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['id'=>$parame['id']]];
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

        //超级管理员ID
        $administrator_id       = config('extend.administrator_id');
        if ($parame['id'] == $administrator_id) return ['Code' => '200028', 'Msg'=>lang('200028')];

        //删除用户详细信息
        model('user_detail')->delData($parame['id']);
        
        //删除分组信息
        model("user_group_access")->setGroupAccess($parame['id'],[]);

        //执行删除操作
    	$delCount				= $dbModel->delData($parame['id']);

    	return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['count'=>$delCount]];
    }

    /*api:14d21e95293b34d2358478519fba550f*/
    /**
     * * 登录（账号+密码）
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function passwordLogin($parame)
    {
        //主表数据库模型
        $dbModel    = model($this->mainTable);

        //自行书写业务逻辑代码
        $username   = $parame['username'];
        $password   = $parame['password'];

        //判断登录类型 （1-用户名，2-邮箱，3-手机）
        $usernameType          = 1;
        if (Email_check($parame['username'])){
            $usernameType      = 2;
        }elseif (Mobile_check($parame['username'], array(1))){
            $usernameType      = 3;
        }

        //调用登录模型
        $userModel  = model('user_center');
        $uid        = $userModel->login($username, $password, $usernameType);
        
        if ($uid > 0)
        {
            //根据group_id确定用户是否正确登录
            /*
            $login_type     = !empty($parame['login_type']) ? explode(',',$parame['login_type']) : [-1];
            $guid           = model('user_group_access')->getUserGroupAccessListByUid($uid);
            
            if (empty(array_intersect($login_type, $guid))) return $this->userMessage(-1);
            */
           
            $key                        = config('extend.uc_auth_key');
            $time                       = time();
            $exp                        = $time + (1 * 3600 * 1);
            $token = [
                "iat" => $time,
                "nbf" => $time,
                "exp" => $exp,
                "uid" => intval($uid)
            ];

            $token              = \Firebase\JWT\JWT::encode($token,$key,"HS256");
            $hashid             = base64_encode(string_encryption_decrypt($token,'ENCODE'));

            //数据返回
            $data               = [];
            $data['uid']        = intval($uid);
            $data['hashid']     = $hashid;

            $userDetailModel    = model('user_detail');
            $userDetailInfo     = $userDetailModel->getOneById($uid);

            //维护token
            model('api_token')->saveData(0,[
                'uid'       => $uid,
                'exp'       => $exp,
                'token'     => md5($hashid),
                'hashid'    => $hashid
            ]);

            //极光ID
            if (isset($parame['jpushid']) && !empty($parame['jpushid']))
            {
                $userModel->updateById($userDetailInfo['id'],['jpushid'=>$parame['jpushid']]);
            }

            //日志
            model('Logs')->tips([
                'uid'=>$data['uid'],
                'm'=>$this->controllerName,
                'a'=>$this->actionName,
                'message'=>lang('login_success')
            ]);

            //更新登录信息
            $loginInfo                      = [];
            $loginInfo['last_login_ip']     = get_client_ip();
            $loginInfo['last_login_time']   = time();
            $loginInfo['login']             = isset($userDetailInfo['login']) ? (int)$userDetailInfo['login'] + 1 : 1;

            $userDetailModel->updateById($uid,$loginInfo);

            return ['Code' => '200', 'Msg'=>lang('login_success'),'Data'=>$data];
        }

        return $this->userMessage($uid);
    }

    /*api:14d21e95293b34d2358478519fba550f*/

    /*api:defd702febff8d73420c41546d79bdc9*/
    /**
     * * 用户详情
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function userDetail($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码
        $ucUserInfo             = $dbModel->getOneById($parame['id']);
        $userDetail             = model('user_detail')->getOneById($parame['id']);

        if (empty($ucUserInfo))  return ['Code' => '200004', 'Msg'=>lang('200004')];
        $ucUserInfo             = $ucUserInfo->toArray();

        //需要返回的数据体
        $Data                             = [];
        $Data['id']                       = $ucUserInfo['id'];
        $Data['username']                 = $ucUserInfo['username'];
        $Data['email']                    = $ucUserInfo['email'];
        $Data['mobile']                   = $ucUserInfo['mobile'];
        $Data['create_time']              = $ucUserInfo['create_time'];
        $Data['reg_ip']                   = $ucUserInfo['reg_ip'];
        $Data['nickname']                 = $userDetail['nickname'];
        $Data['face']                     = $userDetail['face'];
        $Data['face_path']                = get_cover($userDetail['face'],'path');
        $Data['account']                  = $userDetail['account'];
        $Data['login']                    = $userDetail['login'];
        $Data['last_login_ip']            = $userDetail['last_login_ip'];
        $Data['last_login_time']          = !empty($userDetail['last_login_time']) ? date('Y-m-d H:i:s',$userDetail['last_login_time']):'/';
        $Data['urules']                   = $userDetail['rules'];
        $Data['sex']                      = $userDetail['sex'];

        $Data['status']                   = $ucUserInfo['status'];
        $Data['detail_id']                = $userDetail['id'];
        $Data['mark']                     = $userDetail['mark'];

        $groupInfo                        = $this->getGroupRules($Data);

        if ($groupInfo['is_super'] == 1) {
            $rules                            = 'all';
            $Data['group_id']                 = 1;
        }
        else{
            $Data['is_super']                 = $groupInfo['is_super'];
            $Data['grules']                   = $groupInfo['rules'];
            $Data['group_name']               = $groupInfo['group_name'];
            $Data['group_id']                 = in_array($groupInfo['group_id'], [1,2]) ? $groupInfo['group_id'] : 3;

            $rules  = !empty($Data['urules']) ? $Data['urules'] .','.$Data['grules'] : $Data['grules'];
            $rules  = trim($rules,',');
            $rules  = explode(',' , $rules);
            $rules  = array_flip(array_flip($rules));

            sort($rules);

            $rules  = !empty($rules) ? implode(',',$rules) : '';
        }

        $Data['rules']                   = $rules;

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$Data];
    }

    /*api:defd702febff8d73420c41546d79bdc9*/

    /*api:f100f8720d7e59ac0f05bfa32482af6c*/
    /**
     * * 用户注册（账号+密码）
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function usernameRegister($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码

        $username               = isset($parame['username']) ? $parame['username'] : '';
        $password               = isset($parame['password']) ? $parame['password'] : '';
        $repeatpwd              = isset($parame['repeatpwd']) ? $parame['repeatpwd'] : '';
        $mobile                 = isset($parame['mobile']) ? $parame['mobile'] : '';
        $sms_code               = isset($parame['sms_code']) ? $parame['sms_code'] : '';
        $invitation_code        = isset($parame['invitation_code']) ? $parame['invitation_code'] : '';
        $iuid                   = 0;

        //验证两次密码输入是否一致
        if ( empty($password) || md5($password) !== md5($repeatpwd))
        return ['Code' => '200012', 'Msg'=>lang('200012')];

        //邀请码不为空判断合法性    
        if (!empty($invitation_code))
        {   
            $iuid       = get_invitation_uid($invitation_code);
            if (empty(model('user_center')->getOneById($iuid)))
            return ['Code' => '200034', 'Msg'=>lang('200034')];
        }
        
        //检验验证码 定义校验验证码参数
        $checkParame                = [];
        $checkParame['scene']       = 1;
        $checkParame['sms_code']    = $parame['sms_code'];
        $checkParame['mobile']      = $parame['mobile'];
        $checkParame['check_type']  = 1;

        //检验验证码
        $checkCode                  = $this->helper($checkParame,'Api','Sms','checkCode');
        if ($checkCode['Code'] !== '200')
        return ['Code' => '200030', 'Msg'=>lang('200030',[$checkCode['Msg']])];

        $uid                        = model('user_center')->register($parame);

        //注册成功
        if ($uid >0)
        {
            //数据返回
            $data                       = [];
            $data['id']                 = intval($uid);
            $data['uid']                = intval($uid);
            $data['hashid']             = md5($uid.config('extend.uc_auth_key'));
            $data['invitation_code']    = $invitation_code;


            //初始化用户详细资料数据
            model('user_detail')->saveData($data);

            //自动分配到会员组里面
            model('user_group_access')->saveData($uid,2);

            //删除验证码
            $this->helper(['id'=>$checkCode['Data']['smsid']],'Api','Sms','delCode');

            //入库邀请人
            if ($iuid > 0)
            model('user_invitation')->addData(['uid'=>$iuid,'create_time'=>time(),'uname'=>$username]);

            //日志
            model('Logs')->addLog(['uid'=>$data['uid'],'log_type'=>1,'info'=>lang('3')]);

            return ['Code' => '200', 'Msg'=>lang('200020'),'Data'=>$data];
        }

        return $this->userMessage($uid);
    }

    /*api:f100f8720d7e59ac0f05bfa32482af6c*/

    /*api:ecb2bdf892632423245c8a89fd211427*/
    /**
     * * 用户资料快捷编辑
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function quickEditUserDetailData($parame)
    {
        //主表数据库模型
        $dbModel          = model($this->mainTable);

        //自行书写业务逻辑代码

        //定义可以快速修改的字段信息
        $allow            = ['nickname','sex'];

        if (!in_array($parame['fieldName'],$allow))
        return ['Code' => '120025', 'Msg'=>lang('120025',[$parame['fieldName']])];

        $info             = $dbModel->updateById($parame['id'],[$parame['fieldName']=>$parame['updata'],'update_time'=>time()]);

        if (!empty($info)) {

            return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['id'=>$parame['id']]];
        }else{

            return ['Code' => '100015', 'Msg'=>lang('100015')];
        }
    }

    /*api:ecb2bdf892632423245c8a89fd211427*/

    /*api:3b1f712d3cbb6874011b78fc67271ef2*/
    /**
     * * 用户资料更新
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function saveUserDetailData($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        $faceid                 = isset($parame['faceid']) ? intval($parame['faceid']) : -1;
        $nickname               = isset($parame['nickname']) ? $parame['nickname'] : 'no';
        $sex                    = isset($parame['sex']) ? intval($parame['sex']) : -1;
        $mark                   = isset($parame['mark']) ? trim($parame['mark']) : 'no';
        $id                     = isset($parame['id']) ? intval($parame['id']) : 0;

        $updata                 = [];
        if ($faceid != -1)      $updata['face']         = $faceid;
        if ($nickname != 'no')  $updata['nickname']     = $nickname;
        if ($sex != -1)         $updata['sex']          = $sex;
        if ($mark != 'no')      $updata['mark']         = $mark;

        if (!empty($updata))
        {
            //$userDetail       = $dbModel->getOneById($parame['uid']);
            $info     = $id > 0 ? $dbModel->updateById($id,$updata) : $dbModel->saveData($updata);
            //$dbModel->delDetailDataCacheByUid($userDetail['uid']);
        }

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['id'=>$id]];
    }

    /*api:3b1f712d3cbb6874011b78fc67271ef2*/

    /*api:7d96300541a7d53e5a8505e1f5db8a18*/
    /**
     * * 密码找回（手机/邮箱+验证码）
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function forgetPasswordByCode($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        $mobile                 = isset($parame['mobile']) ? $parame['mobile'] : '';
        $sms_code               = isset($parame['sms_code']) ? $parame['sms_code'] : '';
        $method                 = isset($parame['method']) ? intval($parame['method']) : 0;
        $password               = isset($parame['password']) ? $parame['password'] : '';
        $repeat_password        = isset($parame['repeat_password']) ? $parame['repeat_password'] : '';

        if (empty($password) || md5($password) !== md5($repeat_password) )
        return ['Code' => '200012', 'Msg'=>lang('200012')];

        //定义校验验证码参数
        $checkParame                = [];
        $checkParame['scene']       = 2;
        $checkParame['sms_code']    = $parame['sms_code'];

        //根据校验方式分发参数
        switch ($parame['method']) {
            case 1:
                $checkParame['mobile']      = $parame['mobile'];
                $checkParame['check_type']  = 1;
                break;
            case 2:
                $checkParame['email']       = $parame['email'];
                $checkParame['check_type']  = 2;
                break;
            default: return ['Code' => '200029', 'Msg'=>lang('200029')];
        }

        //检验验证码
        $checkCode              = $this->helper($checkParame,'Api','Sms','checkCode');
        if ($checkCode['Code'] !== '200')
        return ['Code' => '200030', 'Msg'=>lang('200030',[$checkCode['Msg']])];

        //修改密码
        $uid    = model('user_center')->updatePassword($password,$mobile,1);

        if ($uid > 0)
        {
            //删除验证码
            $this->helper(['id'=>$checkCode['Data']['smsid']],'Api','Sms','delCode');

            //返回数据
            return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['res_status'=>'ok']];
        }

        Lang::load( \Env::get('APP_PATH') . 'common/lang/zh-cn/user.php');

        return $this->userMessage($uid);
    }

    /*api:7d96300541a7d53e5a8505e1f5db8a18*/

    /*api:b7004d3672538f104606ec6f34ba1d00*/
    /**
     * * 用户头像修改接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function updateHeadImage($parame)
    {
        $fileName                  = (isset($parame['fileName'])) ? $parame['fileName'] : '';
        $upload                    = [];
        $upload['fileName']        = $fileName;
        $updata['tags']            = 'face';

        $uploadRes                 = $this->helper($upload,'admin','Upload','uploadImg');
        if ($uploadRes['Code'] != '200')  return $uploadRes;

        $imageData  = (isset($uploadRes['Data']['data']) && !empty($uploadRes['Data']['data'])) ? json_decode($uploadRes['Data']['data']) : '';
        $imageid                    = isset($imageData['lists'][0]['id']) ? $imageData['lists'][0]['id'] : 0;

        model('user_detail')->updateByUid($parame['uid'],['face'=>$imageid]);

        $data['url']       = get_cover($imageid,'path');
        
        return ['Code' => '200', 'Msg'=>lang('200008'),'Data'=>$data];
    }

    /*api:b7004d3672538f104606ec6f34ba1d00*/

    /*api:026ea8a777269ba40b5233d8e5403c67*/
    /**
     * * 用户更换手机号
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function updateMobile($parame)
    {
        //主表数据库模型
        $dbModel                    = model('user_center');
        $userinfo                   = $dbModel->getOneById($parame['uid']);

        //用户不存在
        if (empty($userinfo))  return $this->userMessage(-1);
        
        //用户是否绑定了手机号码
        if (!isset($userinfo['mobile']) || empty($userinfo['mobile']))
        return $this->userMessage(-14);
        
        $old_mobile             = isset($parame['old_mobile']) ? $parame['old_mobile'] : '';
        $sms_code               = isset($parame['sms_code']) ? $parame['sms_code'] : '';
        $new_mobile             = isset($parame['new_mobile']) ? $parame['new_mobile'] : '';

        if (md5($old_mobile) !== md5($userinfo['mobile']))
        return $this->userMessage(-16);

        //检验验证码 定义校验验证码参数
        $checkParame                = [];
        $checkParame['scene']       = 1;
        $checkParame['sms_code']    = $parame['sms_code'];
        $checkParame['mobile']      = $new_mobile;
        $checkParame['check_type']  = 1;

        //检验验证码
        $checkCode                  = $this->helper($checkParame,'Api','Sms','checkCode');
        if ($checkCode['Code'] !== '200')
        return ['Code' => '200030', 'Msg'=>lang('200030',[$checkCode['Msg']])];

        $res    = $dbModel->updateById($parame['uid'],['mobile'=>$new_mobile]);
        if (!empty($res)) {
            //删除验证码
            $this->helper(['id'=>$checkCode['Data']['smsid']],'Api','Sms','delCode');
        }

        //需要返回的数据体
        $Data['id']                   = $userinfo['id'];

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$Data];
    }

    /*api:026ea8a777269ba40b5233d8e5403c67*/

    /*api:2210e99bea736d7033c64a490a033cd2*/
    /**
     * * 用户密码修改（通过原始密码）
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function updatePasswordByOld($parame)
    {
        //主表数据库模型
        $dbModel                = model('user_center');

        $uid                    = $this->getUserId($parame['hashid']);

        //获取用户ID
        $userinfo               = $dbModel->getOneById($uid);

        //用户不存在
        if (empty($userinfo))  return $this->userMessage(-1);

        $oldpwd                 = isset($parame['old_password']) ? $parame['old_password'] : '';
        $newpwd                 = isset($parame['new_password']) ? $parame['new_password'] : '';
        $reppwd                 = isset($parame['confirm_password']) ? $parame['confirm_password'] : '';

        if (empty($newpwd) || md5($newpwd) !== md5($reppwd) )
        return ['Code' => '203', 'Msg'=>lang('notice_confirm_not_same')];

        //修改密码
        $uid    = model('user_center')->updatePassword($newpwd,$parame['uid'],3,$oldpwd);
        if ($uid > 0)
        {
            //返回数据
            return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['res_status'=>'ok']];
        }
        
        Lang::load( \Env::get('APP_PATH') . 'common/lang/zh-cn/user.php');
        return $this->userMessage($uid);
    }

    /*api:2210e99bea736d7033c64a490a033cd2*/

    /*api:8d4fe31070a5465e54248cfca5255ab4*/
    /**
     * * 用户独立权限设置
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function setUserPrivilege($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码
        $menu_id                = isset($parame['menu_id']) ? $parame['menu_id'] : '';
        $group_id               = isset($parame['group_id']) ? explode(',', $parame['group_id']) : [];
        
        model('user_detail')->updateById($parame['id'],['rules'=>$menu_id,'update_time'=>time()]);
        model('user_group_access')->setGroupAccess($parame['id'],$group_id);

        //需要返回的数据体
        $Data                   = ['id'=>$parame['id']];

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$Data];
    }

    /*api:8d4fe31070a5465e54248cfca5255ab4*/

    /*api:f361e06c3640311e8255cb1a4e0628f2*/
    /**
     * * 退出登录
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function logout($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码
        model('api_token')->deleteTokenByToken(md5($parame['hashid']));

        //需要返回的数据体
        return ['Code' => '200', 'Msg'=>lang('logout_success'),'Data'=>''];
    }

    /*api:f361e06c3640311e8255cb1a4e0628f2*/

    /*api:f41838ec0bbc7feb996582f9c9bd3f00*/
    /**
     * * 密码重置（管理员通过用户ID重置密码）
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function resetPwd($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码
        $uid                    = isset($parame['id']) ? (int)$parame['id'] : 0;
        $userInfo               = $dbModel->getOneById($uid);

        if (empty($userInfo)) return ['Code' => '203', 'Msg'=>lang('notice_user_not_exist')];

        $dbModel->resetPwd($uid);

        //需要返回的数据体
        $Data                   = ['id'=>$uid];

        return ['Code' => '200', 'Msg'=>lang('resetpwd_success'),'Data'=>$Data];
    }

    /*api:f41838ec0bbc7feb996582f9c9bd3f00*/

    /*接口扩展*/

    /**
     * 获取用户组权限信息
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function getGroupRules($parame){

        //超级管理员ID
        $administrator_id        = config('extend.administrator_id');

        $groupInfo['is_super']   = 0;
        $groupInfo['group_name'] = '';

        $gRules                  = '';
        $gname                   = '';

        if (isset($parame['id']) && $administrator_id == $parame['id']) {

            $groupInfo['is_super']      = 1;
            $groupInfo['rules']         = 'all';
            $groupInfo['group_name']    = lang('2');
            $groupInfo['group_id']      = '';

        }else{

            //用户组ID集合
            $gids                      = model('user_group_access')->getUserGroupAccessListByUid($parame['id']);
            
            //获取用户组列表
            $glist                     = model('user_group')->getUserGroupListById($gids);

            if (!empty($glist)) {
                
                foreach ($glist as $key => $value) {

                    $gRules .= !empty($value['rules']) ? trim($value['rules'],',').',' : '';
                    $gname  .= !empty($value['title']) ? $value['title'].'/' : '';
                    $gid[]  = $value['id'];
                }
            }

            $gRules = !empty($gRules) ? trim($gRules,',') : '';
            $gname  = !empty($gname) ? trim($gname,'/') : '';

            $groupInfo['rules']         = !empty($gRules) ? $gRules : '';
            $groupInfo['group_name']    = $gname;
            $groupInfo['group_id']      = !empty($gid) ? implode(',',$gid) : '';
        }

        return $groupInfo;
    }

    /**
     * 获取错误信息
     * @param  integer $num 错误编号
     * @return array       错误信息
     */
    private function userMessage($num=0)
    {
        switch ($num) {
            case -1: return ['Code' => '200004', 'Msg'=>lang('200004')];
            case -2: return ['Code' => '200005', 'Msg'=>lang('200005')];
            case -3: return ['Code' => '200006', 'Msg'=>lang('200006')];
            case -4: return ['Code' => '200001', 'Msg'=>lang('200001')];
            case -5: return ['Code' => '200018', 'Msg'=>lang('200018')];
            case -6: return ['Code' => '200016', 'Msg'=>lang('200016')];
            case -7: return ['Code' => '200026', 'Msg'=>lang('200026')];
            case -8: return ['Code' => '200015', 'Msg'=>lang('200015')];
            case -9: return ['Code' => '200027', 'Msg'=>lang('200027')];
            case -10: return ['Code' => '200002', 'Msg'=>lang('200002')];
            case -11: return ['Code' => '200031', 'Msg'=>lang('200031')];
            case -12: return ['Code' => '200032', 'Msg'=>lang('200032')];
            case -13: return ['Code' => '200033', 'Msg'=>lang('200033')];
            case -14: return ['Code' => '200035', 'Msg'=>lang('200035')];
            case -15: return ['Code' => '200036', 'Msg'=>lang('200036')];
            case -16: return ['Code' => '200037', 'Msg'=>lang('200037')];
            case -17: return ['Code' => '200038', 'Msg'=>lang('200038')];
            case -18: return ['Code' => '200025', 'Msg'=>lang('200025')];
            default: return ['Code' => '200007', 'Msg'=>lang('200007')];
        }
    }

    private function getUserGroupTitle($uid = 0)
    {
        $lists                  = model('user_group')->getAllUserGorupTitle();
        $gaccess                = model('user_group_access')->getUserGroupAccessListByUid($uid);
        $gtitle                 = [];

        if ($uid === 1) $gtitle[]           = '超级管理员';

        if (!empty($lists))
        {
            foreach ($lists as $key => $value)
            {
                if (in_array($value['id'], $gaccess)) $gtitle[]       = $value['title'];
            }
        }

        return !empty($gtitle) ? implode(',', $gtitle) : '普通用户';
    }
}
