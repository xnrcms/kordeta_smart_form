<?php
/**
 * 当前模块语言包
 * @author 王远庆 <[562909771@qq.com]>
 */

return [
    //最新状态码
    '200'                      => '请求成功',
    '201'                      => 'Token过期',
    '202'                      => '权限不足',
    '203'                      => '错误警告',
    '204'                      => '请求错误',
    '205'                      => '用户不存在',
    '206'                      => '用户被禁用',

    // 系统错误提示
    '000000'                   => '返回成功',
    '100000'                   => '系统错误',

    '100001'                   => '系统时间【%s】不能为空',
    '100002'                   => '接口签名信息【%s】不能为空',
    '100003'                   => '接口方法名【%s】不能为空',
    '100004'                   => '接口授权ID【%s】不能为空',

    '100005'                   => '【%s】未授权访问',
    '100006'                   => '接口签名【APIKEY】错误',
    '100007'                   => '接口缺失【%s】参数',
    '100008'                   => '接口参数【%s】值不能为空',

    '100009'                   => '用户身份加密串【hashid】不能为空',
    '100010'                   => '用户身份校验失败，【hashid】或【uid】错误',
    '100011'                   => '用户身份ID【uid】不能为空',

    '100012'                   => '需要操作的数据ID不能为空',
    '100013'                   => '需要更新的字段不能为空',
    '100014'                   => '需要更新的数据值不能为空',

    '100015'                   => '接口数据返回失败',
    '100016'                   => '接口未定义返回数据',
    '100017'                   => '定义的【%s】Json数据格式不合法',
    '100018'                   => '终端类型不能为空',
    '120019'                   => '上传名称不能为空',
    '120020'                   => 'Helper层未定义主表名称',
    '120021'                   => 'Helper层未未定义入库数据',
    '120022'                   => '未检测到接口返回参数结构体',
    '120023'                   => '数据ID不能为空或者小于0',
    '120024'                   => '接口ID【APIID】错误',
    '120025'                   => '字段【%s】不允许修改',
    'text_req_success'         => '请求成功',
    'text_token_fail'          => 'Token错误或过期',
    'text_unknown'             => '未知',
    'text_enable'              => '启用',
    'text_prohibit'            => '禁用',

    'notice_undefined_data'    => '未定义入库数据',
    'notice_api_fail'          => '接口数据返回失败',

    'message_save_success'     => '更新成功',
];
