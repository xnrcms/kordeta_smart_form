<?php
/**
 * Model层-会员资料表模型
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;
use think\Db;

class UserDetail extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    public function formatWhereDefault($model,$parame)
    {
    	if (isset($parame['search']) && !empty($parame['search']))
        {
          $search     = json_decode($parame['search'],true);

            if (!empty($search))
            {
                foreach ($search as $key => $value)
                {
                    if (!empty($value) && (is_string($value) || is_numeric($value)) )
                    {
                        $model->where('main.'.$key,'eq',$value);
                    }
                }
            }
        }

        return $model;
    }

    public function getOneByUid($uid)
    {
    	if ($uid <= 0) return null;

		$cacheDataKey		= 'table_byuid_user_detail_' . $uid;
		$info 				= cache($cacheDataKey);

		if (empty($info))
        {
			$info 			= $this->where('uid','=',$uid)->find();

			if ($info)
            {
				$cacheData = serialize($info) ;
				cache($cacheDataKey, $cacheData, config('extend.cache_time'));
			}
		}else{

			$info = unserialize($info) ;
		}

		return $info;
    }

    public function addUserDetailData($parame)
    {
    	$info			= null;

    	if (empty($parame) || !isset($parame['uid']) || empty($parame['uid'])) return $info;

    	foreach ($parame as $key => $value)
        {
			$this->$key 		= $value;
		}

		$isAddSucess			= $this->isUpdate(false)->allowField(true)->save();

		if ($isAddSucess > 0)
        {
			$info 				= $this->getOneByUid($parame['uid']);
		}

		return $info;
    }

    public function delDataByUid($uid)
    {	
    	return $this->where('uid','=',$uid)->delete();
    }

    public function delDetailDataCacheByUid($uid)
    {
    	return cache('table_byuid_user_detail_' . $uid, NULL);
    }

    public function updateDetailByUid($id,$updata)
    {
        $this->delDetailDataCacheByUid($id);
        return $this->save($updata,['uid'=>$id]);
    }
}
