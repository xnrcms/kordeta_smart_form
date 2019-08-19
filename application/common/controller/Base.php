<?php
/**
 * Controller层 接口基础类
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\controller;

use think\Controller;
use think\facade\Lang;

class Base extends Controller
{

	public function __construct()
	{
		header('Access-Control-Allow-Origin:*');
	}

	public function execApi($parame = [])
	{
    	//执行模块名 默认当前model
    	$moduleName		= request()->module();
    	//执行方法名 默认当前action
    	$actionName		= request()->action();
		//定义类名
		$controllerName	= request()->controller();
		//定义类名
		$namespaceName 	= '\app\\'.$moduleName.'\helper';
		//操作类名称及路径
		$models			= '\\'. trim($namespaceName,'\\') . '\\' . trim($controllerName,'\\');
		//数据参数
		$parame 		= request()->param();
		//实例化操作类
		$className 		= new $models($parame,$controllerName,$actionName,$moduleName);
		//执行操作
		return $className->apiRun();
	}

	public function execInside($parame = [])
	{
    	//执行模块名 默认当前model
    	$moduleName		= request()->module();
    	//执行方法名 默认当前action
    	$actionName		= request()->action();
		//定义类名
		$controllerName	= request()->controller();
		//定义类名
		$namespaceName 	= '\app\\'.$moduleName.'\helper';
		//操作类名称及路径
		$models			= '\\'. trim($namespaceName,'\\') . '\\' . trim($controllerName,'\\');
		//数据参数
		$parame 		= request()->param();
		//实例化操作类
		$className 		= new $models($parame,$controllerName,$actionName,$moduleName);
		//执行操作
		return $className->isInside($parame,$actionName);
	}

	public static function execSocketApi($parame = [],$socketUrl = [])
	{
    	//执行模块名 默认当前model
        $moduleName     = $socketUrl[0];
        //执行方法名 默认当前action
        $actionName     = $socketUrl[2];
        //定义类名
        $controllerName = ucwords(lineToHump($socketUrl[1]));
        //定义类名
        $namespaceName 	= '\\app\\'.$moduleName.'\\helper';
		//操作类名称及路径
		$models			= '\\'. trim($namespaceName,'\\') . '\\' . trim($controllerName,'\\');
        //数据参数
        
        if (!class_exists($models))
        {
        	return;
        }
        
        //实例化操作类
        $className      = new $models($parame,$controllerName,$actionName,$moduleName);
        
        //执行操作
        return $className->apiRun();
	}
}