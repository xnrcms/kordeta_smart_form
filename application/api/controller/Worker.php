<?php
namespace app\api\controller;

use think\facade\Env;
use think\worker\Server;

class Worker extends Server
{
	protected $host = '127.0.0.1';
	protected $port = 2346;
	protected $option = [ 
		'count'		=> 4,
		'pidFile'   => Env::get('runtime_path') . 'worker.pid',
		'name'		=> 'think'
	];

	public function onMessage($connection, $data)
	{
		$connection->send('receive success11111');
	}
}