<?php

namespace app\manage\controller;

use think\Controller;
use think\facade\Lang;
use think\captcha\Captcha;

/**
 * 用户登录
 */
class Login extends Controller
{
	/**
	 * 后台用户登录
	 */
	public function index($username = null, $password = null, $verify = null,$remember  = 0)
	{

		$is_verify 			= config('extend.is_verify');
		//接口调用实现

		if(request()->isPost()){
			
			if (empty($username)) $this->error('请输入登录账号！');
			if (empty($password)) $this->error('请输入登录密码！');

			/* 检测验证码 TODO: */
			if( $is_verify == 1 ){
				if (empty($verify)) $this->error('请输入验证码！');
				if (!captcha_check($verify)) $this->error('验证码输入错误！');
			}

			$parame['username']		= $username;
			$parame['password']		= $password;
			$parame['login_type'] 	= '1,3';
			$parame['jpushid'] 		= '';

			$requestRes 			= apiReq($parame,'api/User/passwordLogin');

			$backData 				= !empty($requestRes[0]) ? $requestRes[0] : [];
			$errorInfo				= !empty($requestRes[1]) ? $requestRes[1] : [];

			if(empty($errorInfo)){

				if (empty($backData)) $this->error('登录数据错误！');

				if ($backData['Code'] == '000000') {

					if ($remember == 1){
	                    //指定cookie保存30天时间
	                    cookie(md5('admin_username'.config('extend.uc_auth_key')),string_encryption_decrypt($username,'ENCODE'),2592000);
	                    cookie(md5('admin_password'.config('extend.uc_auth_key')),string_encryption_decrypt($password,'ENCODE'),2592000);
					}

					/* 记录登录SESSION和COOKIES */
					$auth = [
			            'uid'             => $backData['Data']['uid'],
			            'user_hash'		  => $backData['Data']['hashid'],
					];

					session('user_auth', $auth);
					session('user_auth_sign', data_auth_sign($auth));

					$this->success($backData['Msg'], url('Index/index'));
				}else{

					session('[destroy]');
					cookie(null);
					$this->error($backData['Msg']);

				}

				$this->success($backData);
			}else{

				$this->error($errorInfo['Msg']);
			}
		} else {

			//执行登录
			if(is_login()) $this->redirect('Index/index');

			$assignData 						= [];
			$assignData['is_verify']           	= $is_verify;
			$assignData['tplName']           	= config('template.tpl_name');
			$this->assign($assignData);

			//视图渲染
			return view();
		}
	}

	/* 退出登录 */
	public function logout()
	{
		session('user_auth', null);
		session('user_auth_sign', null);
		session('apidoc_user_auth', null);
		session('apidoc_user_auth_sign', null);
		session('[destroy]');
		cookie(null);
		$this->success('退出成功！', url('login/index'));
	}
	
	/* 验证码*/
	public function verify()
	{
		$config['imageH']	= '75';
		$config['imageW']	= '300';
		$config['length']	= 5;
		$config['fontSize']	= 30;
		/*$config['codeSet']	= '';
		$config['zhSet']	= '';*/
		$config['expire'] 	= 180;
		$config['useZh'] 	= false;
		$config['useNoise'] = true;
		$config['useCurve'] = true;

		$captcha 			= new Captcha($config);

        return $captcha->entry();  
	}
}
?>