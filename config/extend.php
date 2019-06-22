<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    //数据加密KEY
    'uc_auth_key'   			=> '&17@:iY$0?(twB]kru)46J^!9l;.,Z5oE[bI_QmA',
    //系统缓存过期时间
    'cache_time'				=> 3600*24,
    //管理员ID标识
    'administrator_id'			=> 1,
    //是否需要验证码登录 0不需要,1需要
    'is_verify'			        => 0,
    //接口授权配置
    'api_auth_url'              => '',//'http://api3.com/',
    'api_auth_id'				=> 'b542da5132138477af8ab448c6ddd38c',
    'api_auth_key'				=> '9b12d0f61a382e19ffa87ed306ff3c3b',
    
    //项目开发必须在本地
    'project_url'               =>'http://dev.smart_form.com/',
    
    //站点配置
    'xnrcms_name'               =>'应用快速开发服务',
    'xnrcms_var'                =>'',
    'is_dev'                    => 1,
    'form_type_list'            => [
        'hidden'        => '隐藏域',
        'number'        => '数字',
        'password'      => '密码',
        'string'        => '字符串',
        'price'         => '价格',
        'textarea'      => '文本域',
        'date'          => '时间(Y-m-d)',
        'time'          => '时间(H:i)',
        'datetime'      => '时间(Y-m-d H:i:s)',
        'bool'          => '布尔',
        'select'        => '枚举',
        'radio'         => '单选',
        'checkbox'      => '多选',
        'image'         => '单图上传',
        'images'        => '多图上传',
        'file'          => '文件上传',
        'editor'        => '富文本编辑器',
        'address'       => '城市选择',
        'bdmap'         => '百度地图',
        'gdmap'         => '高德地图',
        'expand'        => '自定义拓展',
    ],
    'list_type_list'            => [
        'string'        => '字符串',
        'number'        => '数字',
        'price'         => '价格',
        'select'        => '枚举',
        'bool'          => '布尔',
        'datetime'      => '时间(Y-m-d H:i:s)',
        'image'         => '单图上传',
        'done'          => '操作',
        'id'            => 'ID'
    ],
];
