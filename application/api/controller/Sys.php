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

class Sys extends Base
{
    //接口构造
    public function __construct(){

        parent::__construct();
    }

    /*api:50832e1dd757d4c7a43fbed57ee438af*/
    /**
     * 清理缓存
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function clearCache($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:50832e1dd757d4c7a43fbed57ee438af*/

    /*api:32f1425373f20c820bf8c97645f5d42e*/
    /**
     * 系统配置接口
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function config($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:32f1425373f20c820bf8c97645f5d42e*/

    /*api:edc438abfae19f530dedb76108d9d370*/
    /**
     * 通用字段校验是否存在接口
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function checkField($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:edc438abfae19f530dedb76108d9d370*/

    /*接口扩展*/
}