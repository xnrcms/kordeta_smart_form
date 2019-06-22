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

class User extends Base
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

    /*api:14d21e95293b34d2358478519fba550f*/
    /**
     * 登录（账号+密码）
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function passwordLogin($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:14d21e95293b34d2358478519fba550f*/

    /*api:defd702febff8d73420c41546d79bdc9*/
    /**
     * 用户详情
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function userDetail($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:defd702febff8d73420c41546d79bdc9*/

    /*api:f100f8720d7e59ac0f05bfa32482af6c*/
    /**
     * 用户注册（账号+密码）
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function usernameRegister($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:f100f8720d7e59ac0f05bfa32482af6c*/

    /*api:ecb2bdf892632423245c8a89fd211427*/
    /**
     * 用户资料快捷编辑
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function quickEditUserDetailData($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:ecb2bdf892632423245c8a89fd211427*/

    /*api:3b1f712d3cbb6874011b78fc67271ef2*/
    /**
     * 用户资料更新
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function saveUserDetailData($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:3b1f712d3cbb6874011b78fc67271ef2*/

    /*api:7d96300541a7d53e5a8505e1f5db8a18*/
    /**
     * 密码找回（手机/邮箱+验证码）
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function forgetPasswordByCode($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:7d96300541a7d53e5a8505e1f5db8a18*/

    /*api:b7004d3672538f104606ec6f34ba1d00*/
    /**
     * 用户头像修改接口
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function updateHeadImage($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:b7004d3672538f104606ec6f34ba1d00*/

    /*api:026ea8a777269ba40b5233d8e5403c67*/
    /**
     * 用户更换手机号
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function updateMobile($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:026ea8a777269ba40b5233d8e5403c67*/

    /*api:2210e99bea736d7033c64a490a033cd2*/
    /**
     * 用户密码修改（通过原始密码）
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function updatePasswordByOld($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:2210e99bea736d7033c64a490a033cd2*/

    /*api:8d4fe31070a5465e54248cfca5255ab4*/
    /**
     * 用户独立权限设置
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function setUserPrivilege($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:8d4fe31070a5465e54248cfca5255ab4*/

    /*api:f361e06c3640311e8255cb1a4e0628f2*/
    /**
     * 退出登录
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function logout($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:f361e06c3640311e8255cb1a4e0628f2*/

    /*api:f41838ec0bbc7feb996582f9c9bd3f00*/
    /**
     * 密码重置（管理员通过用户ID重置密码）
     * @access public
     * @param  [array] $parame 扩展参数
     * @return [json]          接口数据输出
    */
    public function resetPwd($parame = [])
    {
        //执行接口调用
        return $this->execApi($parame);
    }

    /*api:f41838ec0bbc7feb996582f9c9bd3f00*/

    /*接口扩展*/
}