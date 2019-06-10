<?php
/**
 * Model层-代码示例
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;
use think\Db;

class Region extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    public function formatWhereDefault($model,$parame){

    	$model->where('pid','eq',$parame['pid']);
    	
        return $model;
    }
}
