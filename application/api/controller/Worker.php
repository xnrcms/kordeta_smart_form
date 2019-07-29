<?php
namespace app\api\controller;

use think\facade\Env;
use think\worker\Server;
use GatewayWorker\Lib\Gateway;

class Worker extends Server
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
        var_export($data);
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
    public static function onMessage($client_id, $message)
    {
    	$message_data = json_decode($message, true);
        if(empty($message_data) || !is_array($message_data))
        {
            return ;
        }
        wr($message_data);
        // 根据类型执行不同的业务
        switch($message_data['type'])
        {
            // 客户端回应服务端的心跳
            case 'istong': return;
            case 'doevent':

                if (!isset($message_data['method']) && empty($message_data['method'])) {
                   return;
                }

                if (!isset($message_data['className']) && empty($message_data['className'])) {
                   return;
                }

                $message_data['client_id']              = $client_id;

                //操作方法名
                $method                                 = $message_data['method'];

                //定义类名
                $className                              = ucfirst($message_data['className']);

                //定义命名空间
                $namespace                              = '\app\api\helper';

                //执行操作
                self::helper($message_data,$namespace,$className,$method);
                return;
            default:return;break;
        }
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

    private static function helper($parame,$namespace,$className,$methodName)
    {
        $modelName      = '\\'. trim($namespace,'\\') . '\\' . trim($className,'\\');
        $object         = new $modelName();
        return $object->apiRun($parame,$className,$methodName);
    }
}