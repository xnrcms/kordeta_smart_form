<?php
/**
 * Model层-管理员操作日志模型
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;

class Logs extends Base
{
	//默认主键为id，如果你没有使用id作为主键名，需要在此设置
	protected $pk = 'id';

	public function formatWhereDefault($model,$parame)
	{	
		if (isset($parame['search']) && !empty($parame['search']))
		{
			$search 		= json_decode($parame['search'],true);

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

	public function clearData($parame)
	{
		$count 		= $this->where('id','egt',0)->count();
		
		$this->where('id','egt',0)->delete();

		return $count;
	}

	public function tips($parame)
    {
        return $this->addLog('tips', $parame);
    }

    public function warning($parame)
    {
        return $this->addLog('warning', $parame);
    }

    public function error($parame)
    {
        return $this->addLog('error', $parame);
    }

	private function addLog($level,$parame)
	{
		if (empty($parame)) return false;

		$module 		= (isset($parame['m']) && !empty($parame['m'])) ? $parame['m'] : '';
		$action 		= (isset($parame['a']) && !empty($parame['a'])) ? $parame['a'] : '';
		$message 		= (isset($parame['message']) && !empty($parame['message'])) ? $parame['message'] : '';
		$data 			= (isset($parame['data']) && !empty($parame['data'])) ? $parame['data'] : '';
		$uid 			= isset($parame['uid']) ? intval($parame['uid']) : 0;
		$requestHeader 	= request()->header();
		
		$updata 		= 
    	[
            'module' 			=> $module,
            'action' 			=> $action,
            'message' 			=> $message,
            'url'				=> request()->url(true),
            'data' 				=> empty($data) ? '' : $data,
            'uid' 				=> $uid,
            'ip' 				=> request()->ip(),
            'browser' 			=> \xnrcms\DeviceToolkit::getBrowse(),
            'operating_system' 	=> \xnrcms\DeviceToolkit::getOperatingSystem(),
            'device' 			=> \xnrcms\DeviceToolkit::isMobileClient() ? 'mobile' : 'computer',
            'user_agent' 		=> isset($requestHeader['user-agent']) ? $requestHeader['user-agent'] : '',
            'create_time' 		=> time(),
            'level' 			=> $level,
    	];

	    $this->addData($updata);

	    return true;
	}
}
