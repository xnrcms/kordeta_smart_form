<?php
/**
 * Model层-三方授权用户信息
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;
use think\Db;

class UserAuth extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    public function formatWhereDefault($model,$parame){
        return $model;
    }

    public function getValue($map=[],$field='id'){

        if (empty($map))  return '';

        $res = $this->where($map)->value($field);

        return !empty($res) ? $res : '';
    }
}
