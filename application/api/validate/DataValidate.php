<?php
namespace app\api\validate;

use think\Validate;

class DataValidate extends Validate
{
	//验证器字段和规则
    protected $rule =   [
        'username'                                  => 'require|isUsername',
        'password'                                  => 'number|between:1,120',
        'email'                                     => 'email',
        //.....扩展更多字段
    ];
    
    //验证器字错误信息
    protected $message  =   [
        'username.require'                          => '200001',
        'username.isUsername'                       => '200002',
        'username.checkName'                        => 'hahahahah',
        'age.number'                                => '年龄必须是数字',
        'age.between'                               => '年龄只能在1-120之间',
        'email'                                     => '邮箱格式错误',
        //.....扩展更多字段错误信息
    ];

    //验证器场景
    protected $scene = [
        'edit2'  =>  ['username','age'],
        //.....扩展更多验证器场景
    ];

     /*******************************************/
     /*          自定义验证规则                 */
     /*******************************************/
    
     //用户名是否合法
    protected function isUsername($value,$rule,$data=[],$fileName="",$fileDesc="")
    {
        $RegExp     = '/^[a-zA-Z0-9_]{5,30}$/'; //由大小写字母跟数字组成并且长度在5-30的字符
        return preg_match($RegExp,$value) ? true : false;
    }
    
    //手机号是否合法
    protected function IsMobile($value,$rule,$data=[],$fileName="",$fileDesc="")
    {
        //return Mobile_check($value,array(1)) ? true : false;
    }
   	protected function checkName($value,$rule,$data=[],$fileName="",$fileDesc="")
    {
        return false;
    }
    //.....扩展更多自定义验证规则

}
?>