<?php
/**
 * XNRCMS<562909771@qq.com>
 * ============================================================================
 * 版权所有 2018-2028 小能人科技有限公司，并保留所有权利。
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 */
namespace app\api\controller;

use think\worker\Server;
use GatewayWorker\Lib\Gateway;

class Socket extends Server
{
    /**
     * onConnect 事件回调
     * 当客户端连接上gateway进程时(TCP三次握手完毕时)触发
     *
     * @access public
     * @param  int       $client_id
     * @return void
     */
    public static function onConnect($client_id)
    {
        Gateway::sendToCurrentClient("Your client_id is $client_id");
    }

    /**
     * onWebSocketConnect 事件回调
     * 当客户端连接上gateway完成websocket握手时触发
     *
     * @param  integer  $client_id 断开连接的客户端client_id
     * @param  mixed    $data
     * @return void
     */
    public static function onWebSocketConnect($client_id, $data)
    {
        //var_export($data);
    }

    /**
     * onMessage 事件回调
     * 当客户端发来数据(Gateway进程收到数据)后触发
     *
     * @access public
     * @param  int       $client_id
     * @param  mixed     $data
     * @return void
     */
    public static function onMessage($client_id, $parame)
    {
        $parame            = json_decode($parame, true);

        if(empty($parame) || !is_array($parame))
        return Gateway::sendToCurrentClient("Socket communication parameter error");

        //解析接口地址
        $socketUrl         = isset($parame['socketUrl']) ? trim($parame['socketUrl'],'/') : '';
        $socketUrl         = explode('/', $socketUrl);

        if (!(count($socketUrl) === 3))
        return Gateway::sendToCurrentClient(self::returnData(['Msg'=>"SocketUrl Error"]));

        //unset($parame['socketUrl']);
        
        return self::execApi($parame,$socketUrl,$client_id);
    }



    /**
     * onClose 事件回调 当用户断开连接时触发的方法
     *
     * @param  integer $client_id 断开连接的客户端client_id
     * @return void
     */
    public static function onClose($client_id)
    {
        GateWay::sendToAll("client[$client_id] logout\n");
    }

    /**
     * onWorkerStop 事件回调
     * 当businessWorker进程退出时触发。每个进程生命周期内都只会触发一次。
     *
     * @param  \Workerman\Worker    $businessWorker
     * @return void
     */
    public static function onWorkerStop(Worker $businessWorker)
    {
        echo "WorkerStop\n";
    }

    /*api:bb11599b1bc689a4180ae58301262de2*/
    /**
     * 建立Socket通信接口
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function index($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:bb11599b1bc689a4180ae58301262de2*/

    /*api:e9a7f5ae41b8a1ed58f8e7d69366f9c8*/
    /**
     * 手签-Socket通信接口
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function handSign($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:e9a7f5ae41b8a1ed58f8e7d69366f9c8*/

    /*接口扩展*/

    public static function execApi($parame = [],$socketUrl = [],$client_id = '')
    {
        //执行模块名 默认当前model
        $moduleName     = $socketUrl[0];
        //执行方法名 默认当前action
        $actionName     = $socketUrl[2];
        //定义类名
        $controllerName = ucwords(lineToHump($socketUrl[1]));
        //定义类名
        $namespaceName  = '\app\\'.$moduleName.'\helper';
        //操作类名称及路径
        $models         = '\\'. trim($namespaceName,'\\') . '\\' . trim($controllerName,'\\');
        //数据参数
        
        if (!class_exists($models))
        return Gateway::sendToCurrentClient(self::returnData(['Msg'=>"SocketUrl Not Exists"]));

        request()->setModule($moduleName);
        request()->setController($controllerName);
        request()->setAction($actionName);

        //实例化操作类
        $className      = new $models($parame,$controllerName,$actionName,$moduleName);

        $className->setClientId($client_id);
        
        $apiRes         = $className->apiRun();

        //执行操作
        return Gateway::sendToCurrentClient(self::returnData($className->apiRun()));;
    }

    private static function returnData($data = [])
    {
        $domain     = trim(request()->domain(),'/');
        $apitime    = date('Y-m-d H:i:s',time());
        $base       = ['Code' =>'203','Msg'=>lang('100000'),'Time'=>$apitime];

        return json_encode(array_merge($base,$data));
    }
}