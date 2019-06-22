<?php
/**
 * Model层-代码示例
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;
use think\Db;

class Sms extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    public function formatWhereDefault($model,$parame){
        return $model;
    }

    public function get_create_time($mobile){
        $map        = [];
        $map[]      = ['mobile','=',$mobile];
        $map[]      = ['status','=',1];

    	$res = $this->where($map)->order('id desc')->limit(1)->value('create_time');

    	return !empty($res) ? $res : 0;
    }

    public function get_ip_count($mobile){

        $map        = [];
        $map[]      = ['mobile','=',$mobile];
        $map[]      = ['ip','=',get_client_ip()];
        $map[]      = ['create_time','elt',strtotime(date('Y-m-d',strtotime('+1 day')))];

        $res = $this->where($map)->count('ip');
        
        return !empty($res) ? $res : 0;
    }

    public function getValue($map=[],$field='id'){

        if (empty($map))  return '';

        $res = $this->where($map)->value($field);

        return !empty($res) ? $res : '';
    }

    public function delValidityData($validity = 0){

        return $this->where('validity','lt',time())->delete();
    }
}
