<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\facade\Cache;

class Config extends Base
{
    //默认主键为id，如果你没有使用id作为主键名，需要在此设置
    protected $pk = 'id';

    public function formatWhereDefault($model,$parame){
        return $model;
    }

    public function addConfigData($data = [],$config_type = '')
    {
        if (empty($data) || empty($config_type))  return false;

    	$addData 	= [];
    	foreach ($data as $key => $value) {
            $value          = is_array($value) ? implode(',', $value) : $value;
    		$addData[] 		= ['keys'=>$key,'value'=>$value,'config_type'=>$config_type];
    	}

    	if (!empty($addData)) {
    		
    		$this->where('config_type','eq',$config_type)->delete(true);
    		$this->saveAll($addData);
    	}

    	$cacheDataKey  = "TableNameForConfig_0";

    	Cache::rm($cacheDataKey);
    	return true;
    }

    public function getConfigData($config_type='')
    {
    	$cacheDataKey  	= "TableNameForConfig_0";

    	$config 		= Cache::get($cacheDataKey);

    	if (!empty($config)) {
    		
    		$config 	= unserialize($config);

    	}else{

    		$list 		= $this->select()->toArray();

    		$config 	= [];

    		if (!empty($list)) {
    			
    			foreach ($list as $key => $value) {
    				
    				$config[$value['config_type']][$value['keys']] 		= $value['value'];
    			}

    			Cache::set($cacheDataKey,serialize($config),config('extend.cache_time'));
    		}
    	}

        return !empty($config_type) ? (isset($config[$config_type]) ? $config[$config_type] : []) : $config;
    }
}
