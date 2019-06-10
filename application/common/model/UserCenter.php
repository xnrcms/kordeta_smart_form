<?php
/**
 * Model层-用户中心模型
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;

class UserCenter extends Base
{
	//默认主键为id，如果你没有使用id作为主键名，需要在此设置
	protected $pk = 'id';

	public function formatWhereDefault($model,$parame)
    {
		if (isset($parame['search']) && !empty($parame['search'])) {

			$search 		= json_decode($parame['search'],true);

			if (!empty($search)) {

				foreach ($search as $key => $value) {

					if (!empty($value) && (is_string($value) || is_numeric($value)) ) {
                        if ($key == 'nickname') {
                            $model->where('ud.'.$key,'eq',trim($value));
                        }else{
                           $model->where('main.'.$key,'eq',trim($value));
                        }
					}
				}
			}
		}

        return $model;
    }

    public function loginByUserNameAndUid($username, $uid)
    {   
        $user = $this->where('username|mobile|email','eq',$username)->where('id','eq',$uid)->find();
        if (!empty($user) && $user->getAttr('id') > 0) {
            return true;
        }
        return false;
    }

	public function login($username, $password,$type)
    {
		$map = array();
		switch ($type) {
			case 1: $map['username'] 	= $username; break;
			case 2: $map['email'] 		= $username; break;
			case 3: $map['mobile'] 		= $username; break;
			case 4: $map['id'] 			= $username; break;
			default: return 0; //参数错误
		}

		//用户不存在
		$user = $this->where($map)->find();
		if (empty($user) || $user->getAttr('id') <= 0) {
			return -1; 
		}

		//用户被禁用
		if ($user->getAttr('status') != 1) {
			return -2; 
		}
		
		//密码验证
		if(data_md5($password, config('extend.uc_auth_key')) === $user->getAttr('password')){

			return $user->getAttr('id'); //登录成功，返回用户ID
		} else {
			return -3; //密码错误
		}
	}

    public function register($parame)
    {	
    	$username 	= (isset($parame['username']) && !empty($parame['username'])) ?  $parame['username'] : '';
    	$password 	= (isset($parame['password']) && !empty($parame['password'])) ?  $parame['password'] : '';
    	$mobile 	= (isset($parame['mobile']) && !empty($parame['mobile'])) ?  $parame['mobile'] : '';
    	$email 		= (isset($parame['email']) && !empty($parame['email'])) ?  $parame['email'] : '';
        $status     = (isset($parame['status']) && !empty($parame['status'])) ?  $parame['status'] : 1;

    	if ($this->isExistUsername($username,0) < 0) return $this->isExistUsername($username,0);
    	if ($this->isExistMobile($mobile,0) < 0) return $this->isExistMobile($mobile,0);
    	if ($this->isExistEmail($email,0) < 0) return $this->isExistEmail($email,0);
        if ($this->checkPassword($password) < 0) return $this->checkPassword($password);

        //密码不能为空
    	if (empty($password)) return -10;

        //手机号不为空
    	if (!empty($mobile)) $regData['mobile'] 	= $mobile;

        //邮箱不为空
    	if (!empty($email)) $regData['email'] 		= $email;

    	//注册类型
    	$type		= 1;
		if (Email_check($username)){
			$type	= 2;
		}elseif (Mobile_check($username, array(1))){
			$type	= 3;
		}

        switch (intval($type)) {
        	case 1:  $regData['username']  	= $username; break;
        	case 2:  $regData['email']  	= $username; break;
        	case 3:  $regData['mobile']  	= $username; break;
        	default: $regData['username']  	= $username; break;
        }

        if (isset($regData['username']) && $this->checkUsername($username) < 0)
        return $this->checkUsername($username);

        $regData['password']				= data_md5($password, config('extend.uc_auth_key'));
        $regData['reg_ip']					= get_client_ip();
        $regData['create_time']				= time();
        $regData['update_time']				= time();
        $regData['status']					= $status;
        $regData['sort']					= 1;

        $info 								= $this->addData($regData);

        return !empty($info) ? $info['id'] : 0;

    }

    public function saveData($parame)
    {
        $username      = (isset($parame['username']) && !empty($parame['username'])) ?  $parame['username'] : '';
        $password      = (isset($parame['password']) && !empty($parame['password'])) ?  $parame['password'] : '';
        $mobile        = (isset($parame['mobile']) && !empty($parame['mobile'])) ?  $parame['mobile'] : '';
        $email         = (isset($parame['email']) && !empty($parame['email'])) ?  $parame['email'] : '';
        $status        = isset($parame['status']) ?  intval($parame['status']) : 0;
        $id            = isset($parame['id']) ?  intval($parame['id']) : 0;

        if ($id <= 0)  return -1;
        if (!empty($username) && $this->isExistUsername($username,$id) < 0) return $this->isExistUsername($username,$id);
        if ($this->isExistMobile($mobile,$id) < 0) return $this->isExistMobile($mobile,$id);
        if ($this->isExistEmail($email,$id) < 0) return $this->isExistEmail($email,$id);

        if (!empty($username)) $regData['username']         = $username;
        if (!empty($mobile)) $regData['mobile']             = $mobile;
        if (!empty($email)) $regData['email']               = $email;
        if (!empty($password)) $regData['password']         = data_md5($password, config('extend.uc_auth_key'));
        if (!empty($status)) $regData['status']             = $status;

        $regData['update_time']                             = time();

        $info                                               = $this->updateById($id,$regData);

        $info                                               = $this->getOneById($id) ;

        return !empty($info) ? $info['id'] : 0;
    }

    public function isExistUsername($username,$id){

    	if (empty($username))  return -4;//用户名不能为空

    	$model = $this->where('username|mobile|email','eq',$username);

    	if ($id > 0) $model = $model->where('id','neq',$id);

    	return !empty($model->value('id')) ? -5 : 0;//用户名已经存在
    }

    public function isExistMobile($mobile,$id){

    	//手机号为空不校验
    	if (empty($mobile))   return 0;

    	if (!Mobile_check($mobile))  return -6;//手机号格式错误

    	$model = $this->where('username|mobile|email','eq',$mobile);

    	if ($id > 0) $model = $model->where('id','neq',$id);

    	return !empty($model->value('id')) ? -7 : 0;//手机号已经存在
    }

    public function isExistEmail($email,$id){

    	//邮箱为空不校验
    	if (empty($email))   return 0;

    	if (!Email_check($email))  return -8;//邮箱账号格式错误

    	$model = $this->where('username|mobile|email','eq',$email);

    	if ($id > 0) $model = $model->where('id','neq',$id);

    	return !empty($model->value('id')) ? -9 : 0;//邮箱账号已经存在
    }

    public function updatePassword($pwd='',$account='',$type=0,$oldpwd='')
    {

        if ($this->checkPassword($pwd) < 0) return $this->checkPassword($pwd);

        switch ($type) {
            case 1://通过手机号码

                if (!Mobile_check($account))  return -6;//手机号格式错误

                $uid     = $this->where('mobile','=',$account)->value('id');
                break;
            case 2://通过邮箱账号

                if (!Email_check($account))  return -8;//邮箱账号格式错误

                $uid     = $this->where('email','=',$account)->value('id');
                break;
            case 3://通过ID+原始密码
                $oldpwd  = data_md5($oldpwd, config('extend.uc_auth_key'));
                $id      = $this->where([['id','=',$account],['password','=',$oldpwd]])->value('id');
                $uid     = (int)$id > 0 ? (int)$id : -18;//原始密码错误
                break;
            default: return -1;break;
        }

        if ($uid > 0 && !empty($pwd))
        {
            $this->updateById($uid,['password'=>data_md5($pwd, config('extend.uc_auth_key'))]);
        }

        return $uid;
    }

    private function checkUsername($username='')
    {
        if (empty($username) || preg_match_all("/^[a-zA-Z0-9_]{6,18}$/",$username,$data) < 1)
        return -13;

        return 0;
    }

    private function checkPassword($pass = '')
    {
        /*if (empty($pass) || preg_match_all("/^[a-zA-Z\d_~!@#$%^&*()\-_=+{};:<,.>?]{6,16}$/",$pass,$data) < 1)
        return -11;

        if (preg_match_all("/[`~!@#$%^&*()\-_=+{};:<,.>?]/",$pass,$data) < 1)
        return -12;*/

        return 0;
    }
}
