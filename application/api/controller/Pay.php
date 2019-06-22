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

use app\common\controller\Base;

class Pay extends Base
{
    //接口构造
    public function __construct(){

        parent::__construct();
    }

    /**
     * 数据列表接口头
     * @access public
     * @param  [array] $parame [扩展参数]
     * @return [json]          [接口数据输出]
    */
    public function listData($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /**
     * 接口数据添加/更新头
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
     */
    public function saveData($parame=[])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /**
     * 接口数据详情头
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
     */
    public function detailData($parame=[])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /**
     * 接口数据快捷编辑头
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
     */
    public function quickEditData($parame=[])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /**
     * 接口数据删除头
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
     */
    public function delData($parame=[])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:3134925f23ab1238e4a57211b7f16e53*/
    /**
     * 账户充值
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function recharge($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:3134925f23ab1238e4a57211b7f16e53*/

    /*api:97a96cfbdb3effe2be005c5df98736af*/
    /**
     * 提现代付
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function substitute($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:97a96cfbdb3effe2be005c5df98736af*/

    /*接口扩展*/
}