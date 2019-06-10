<?php

namespace app\manage\controller;

use app\manage\controller\Base;

/**
 * 后台首页控制器
 */
class Index extends Base
{
	/**
	 * 后台首页
	 */
	public function index()
	{
		if($this->uid)
		{	
			$pageData['topMenu'] = $this->formatMenu($this->menu);

			$this->assignData($pageData);
			return view();
		} else {

			$this->redirect('Login/login');
		}
	}
	public function center()
	{

		$sys_info 					= array(
            'hostname' 				=> gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'hostip' 				=> get_client_ip(0),
            'hostdomain' 			=> $_SERVER['SERVER_NAME'],
            'hostport' 				=> $_SERVER['SERVER_PORT'],
            'hostenv' 				=> $_SERVER["SERVER_SOFTWARE"],
            'hostsys' 				=> PHP_OS,
            'php_ext_time' 			=> ini_get("max_execution_time"),
			'hostlang'				=> $_SERVER['HTTP_ACCEPT_LANGUAGE'],
			'phpv'           		=> phpversion(),
            'php_ext_type' 			=> php_sapi_name(),
            'mysql_version' 		=> '5.6.16-log',
            'hosttime' 				=> date("Y年n月j日 H:i:s"),
            'host_ext_time' 		=> time(),
            'php_ext_time' 			=> ini_get("max_execution_time"),
            'php_ext_time' 			=> ini_get("max_execution_time"),
            'php_ext_time' 			=> ini_get("max_execution_time"),
            'php_upload' 			=> ini_get('upload_max_filesize'),
            'sys_space' 			=> round((@disk_free_space(".") / (1024 * 1024)),2).'M',
            'register_globals' 		=> get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc' 		=> (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'magic_quotes_runtime' 	=> (1 === get_magic_quotes_runtime())?'YES':'NO',
		);

		if(function_exists("gd_info")){
			$gd = gd_info();
			$sys_info['gdinfo'] 	= $gd['GD Version'];
		}else {
			$sys_info['gdinfo'] 	= "未知";
		}

		$sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
		$sys_info['max_ex_time'] 	= @ini_get("max_execution_time").'s'; //脚本最大执行时间
		$sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
		$sys_info['zlib']           = function_exists('gzclose') ? 'YES' : 'NO';//zlib
		$sys_info['safe_mode']      = (boolean) ini_get('safe_mode') ? 'YES' : 'NO';//safe_mode = Off		
		$sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
		$sys_info['curl']			= function_exists('curl_init') ? 'YES' : 'NO';
		$sys_info['memory_limit']   = ini_get('memory_limit');

		$pageData['sys_info'] 		= $sys_info;
		$this->assignData($pageData);
		return view();
	}

	public function cleanCache()
	{
		$parame 			= [];
		$parame['uid']      = $this->uid;
        $parame['hashid']   = $this->hashid;
        //请求数据
        $res 				= $this->apiData($parame,'api/Sys/clearCache');

        $data 			    = $this->getApiData() ;

        if ($res) {

        	$this->success("清理完成!!!",url('admin/admin/index'));
        }
        else{

        	$this->error($this->getApiError());
        }
	}
}
?>