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

class Index extends Base
{
    //接口构造
    public function __construct(){

        parent::__construct();
    }

    /*api:2dcf1a0ebcd7e6de04c2685efbb72b05*/
    /**
     * 首页综合接口
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function index($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:2dcf1a0ebcd7e6de04c2685efbb72b05*/

    /*接口扩展*/
}