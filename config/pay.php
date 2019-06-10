<?php

return [
        // 微信支付参数
        'wechat' => [
            'use_sandbox'       => false, // 沙箱模式
            'app_id'            => 'wx477f25bdb289fa61', // 应用ID
            'mch_id'            => '1509176431', // 微信支付商户号
            // 'md5_key'           => '2fbefb4c03e73759f16ce9775c5b0318', // 微信支付密钥
            'md5_key'           => 'lt3HVOLNWlYZ4Mebs7b5VqXr2I9w32RU', // 微信支付密钥
            'app_cert_pem'      => \Env('root_path').'certificate/wx/apiclient_cert.pem' , // 微信证书 cert 文件
            'app_key_pem'       => \Env('root_path').'certificate/wx/apiclient_key.pem' , // 微信证书 key 文件
            'sign_type'         => 'MD5',// MD5  HMAC-SHA256
            'limit_pay'         => [
                //'no_credit',
            ],// 指定不能使用信用卡支付   不传入，则均可使用
            'fee_type'          => 'CNY',// 货币类型  当前仅支持该字段
            'notify_url'        => request()->domain().'/api/Crontab/paySuccess/pay_type/2', // 支付通知URL
            'redirect_url'      => '',// 如果是h5支付，可以设置该值，返回到指定页面
            'return_raw'        => false,// 在处理回调时，是否直接返回原始数据，默认为true

        ],
        // 支付宝支付参数
        'alipay' => [
            'use_sandbox'       => false , //是否使用沙盒模式
            'partner'           => '2017091208692604',
            'app_id'            => '2017091208692604',
            'sign_type'         => 'RSA2',
            'limit_pay'         => [],
            'notify_url'        => request()->domain().'/api/Crontab/paySuccess/pay_type/1',
            'return_url'        => '',
            'return_raw'        => '',
            // 支付宝公钥(1行填写)
            'ali_public_key'    => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsZsg6fC78pRWRbMP7VwhOqKTN40XASDRMLQGRXMZl1dSad73Q4Ljoy98gDw53viP2fJsPKW+7jGjHGtZyOOPqMJ9p3jJzWJ1cmGRWRV92QkF14xMIsaoEGtY+09khHPJa8Y/SUqecAtmuJSmZ4US39hSVvf0IRpmPVy3WtTBtMI96M9flM4BBRMEbl3Gm3cM43AwQRPLYqCux1hSGegCqxO303fouS8UQCy4uAEeJI6wZUR0zNj1tTZukznUDOLELK1ZRWtuyD2rJ01CjjMhjwyQmbGFcVdSXCgRhUkcwsTRfOipJ7DUqRUtLPJvBJtkZ6qpfavACR6GSYMWI6n4uwIDAQAB',
            // 支付宝私钥(1行填写)
            'rsa_private_key'   => 'MIIEpAIBAAKCAQEAv4YpVRiHVFzzAvp3IO6nfwPHCLfEr8fHH1GMTdQnntlgp13OKL33kAXyGhIqGIS3SNuW17c3cTEz0XIIMCaLnOqdv5Bm+c4Zhxxwb7r5hlihTwh/ckOSJoGN1Ajh55nLrhi9mKijgP4N+ZxJGlRKtb43qxp1/aVSwFg++SwU6aJ6KJSoznrkufBVKW4MbpcC+MDcwDWiBH5byOrVNeUp9Fb0y2CXDyZRPiUDUAVto8851QFw0BZ1qvoR0rOMPjfMI5MFkknrJZwUtwPYluxcapaX8eF8mW9jVpoXIA25C46N5jtDhW0IXroVfIXkVY8BlVcP79Kc4PmPh/78DmULawIDAQABAoIBAQC5F4dKXfIrbcjM0BHhGcN11Mi9fBvxZTji44SckrPXqGzoUpeyeCSQY66d04ArQdd/6ffVcZN7KPnTKEkRf67db0AUqhFayfuRv8JJuID9fRonrya73uGXpQzUJeFsWg2lqvNQXWnmd6A54vgjIeMx0SsriN8Oyn82tBHn7NopDtaC/FWmQurlPdJJhWkZFs1Nj8fD6IVPnrdS5ThJ9/voeYF5rdayKOZf36lIx8z/hj5Blhr68o3oWuVBF+OuYf3Smxx/n+5uUDFNCr92nXw0O80Vd1eAai20Ls/S1u2zfpAH7xyqIlxI2L9ezLEY+UOSDNglvMSD1e/nJ0ZLdgYBAoGBAPWrccRgXVbVR6I2w5oy+3t0xhiQr6hntnocDS2S4azVB2sAXlQMD7cYCV1YkzgEUENT4ZGplf4rZxfBxwZPVTO6cYJbeVN4NwBcPNT189okq7HkItdmXHQO1Yhm7vWLkkUoSLaEme/9nq1BAOEkB3Owjq+D5kHTKugTJWz3MzdLAoGBAMeT20yKNnKqhFLRMyZphjoN7zu/UWzKdFz/C1T9lY6EVlIhegfnHqbJ9bbPxB/Mn4ihDegzODnSpAb78yH1dWntDk8UCUjRdFfqIvVELiRdKrD0JkKsncIt0QXXIe4sSLYCeEWRsQUgcrZKzD9560p196Rpo9Fug0PXs+X/UEhhAoGANqGvpQoA98RqL6qaCPp5blTjkKbsSTj3HWSLkazuPq8I/USRtYMRI5hWzMlbw8NBzhcjPG9ICcPBI0lWZxLRUbWOdHy/GE7NfGkGph0j40jwXZjsHpaGzNBXsAOj9DrbhkGVGfGXAgWWedTQy0Bl39ZNhL6CP/Ujv4QyeG7ols0CgYA7OjTVhRrdcp8sWKseViigZ+w8Re2rJHXd905snYjZr4pSe17Uo5EkHEFQTF4+taIOkQUoiLLB7jIBZJnl1QtQMSqS6zZKJuapBHH1aZNr9T4rH3mPRdzXeHNUkWCfZwL5CMialL7874E7ef0dRVg/U7z3TOZxy6Mm6geYVOmFYQKBgQCeADki5KrLBnE//DCeBXDgp8BmO/qFncnpvNYQTlXMvCtSb8kWeB9NCP9OafceUAZaNZ/bhdw+8incUQniFB/MrjTimhmjUOY9NG9mDN2SULr5AWIbu9+ZU1tOW0y3FlWc4LTeP+dsG4J3Zml05n68P05nW9Sa+gpjkEia5WkACg==',
        ],
        'sslpayment'=>[
            'InstNo'        =>'900000000000001',
            'MchtNo'        =>'800000000000049',
            'SignKey'       =>'A0E1D9A5EA5484F84B9EC99F78415C48',
            'ReturnURL'     => request()->domain().'/api/Crontab/paySuccess/pay_type/3',
        ],
        'sandpay'=>[
            'mid'           =>'13010152',
            'ReturnURL'     => request()->domain().'/api/Crontab/paySuccess/pay_type/100',
            'CretPwd'       =>'524023',
            'SignKey'       =>'D9C9F95ED5C25B451DD656F7F8DA1573',
        ]
    ];


//appscret : fb75fdd47078994fcff02654ed8c2471