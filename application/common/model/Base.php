<?php
/**
 * Model层-模型基础类
 * @author 王远庆 <[562909771@qq.com]>
 */

namespace app\common\model;

use think\Model;
use think\Db;
use think\facade\Cache;

class Base extends Model
{
	//新增数据
	public function addData($data = [])
	{
		return $this->baseAddData($data);
	}

	//删除数据
	public function delData($id)
	{
		return $this->baseDelData($id);
	}

	//通过主键ID获取数据
	public function getOneById($id = 0,$tag='')
	{
		return $this->baseGetOneById($id,$tag);
	}

	//获取列表数据
	public function getPageList($modelParame = [])
	{
		return $this->baseGetPageList( $modelParame );
	}

	//通过主键ID更新数据
	public function updateById($id = 0,$data = [])
	{
		return $this->baseUpdateById($id,$data);
	}

	//通过主键ID批量更新数据
	public function updateByIds($data = [])
	{
		return $this->baseUpdateByIds($data);
	}

	//获取数据条数
	public function getDataCount($modelParame)
	{
		return $this->baseGetDataCount($modelParame);
	}

	public function clearCache($parame)
	{
		$id 	= isset($parame['id']) ? intval($parame['id']) : 0;
		$tag 	= isset($parame['ctag']) ? 'table_' . $this->name . '_' . trim($parame['ctag']) : '';
		$ckey 	= isset($parame['ckey']) ? trim($parame['ckey']) : '';

		if ($id > 0) Cache::rm('table_' . $this->name . '_' . $id);
		if (!empty($tag)) Cache::clear($tag);
		if (!empty($ckey)) Cache::rm($ckey);
	}

	public function setCache($ckey = '',$data = '',$tag = '')
	{
		if (!empty($ckey)) {
			$tag 		= !empty($tag) ? 'table_' . $this->name . '_' . $tag : '';
			$data 		= (is_array($data) && !empty($data)) ? serialize($data) : $data;

			if (empty($tag)) {
				Cache::set($ckey,$data,config('extend.cache_time'));
			}else{
				Cache::tag($tag)->set($ckey,$data,config('extend.cache_time'));
			}
		}
	}

	public function getCache($ckey = '')
	{
		$data 		= Cache::get($ckey,null);
		return !empty($data) ? unserialize($data) : [];
	}

	public function checkValue($value,$id,$field)
    {
        $res    = $this->where('id','not in',[$id])->where($field,'eq',$value)->value($field);
        return !empty($res) ? true : false;
    }

	//新增
	protected function baseAddData($data)
	{
		$info			= null;
		$pk 			= $this->pk;

		if (!empty($data)) {

			foreach ($data as $key => $value) {

				$this->$key 		= $value;
			}

			$isAddSucess			= $this->isUpdate(false)->allowField(true)->save();

			if ($isAddSucess > 0 && $this->$pk > 0) {

				$tag 				= isset($data['ctag']) ? $data['ctag'] : '';
				$info 				= $this->getOneById($this->$pk,$tag);
			}
		}

		return $info;
	}

	//删除
	protected function baseDelData($id,$isDel=false)
	{
		$ids = (is_string($id) || is_numeric($id)) ? explode(',', $id) : $id;

		if (empty($ids))  return 0;

		$isDelSuccess 		= $this->destroy($ids,$isDel);
			
		if ($isDelSuccess > 0) {

			foreach ($ids as $v) {

				Cache::rm('table_' . $this->name . '_' . $v);
			}
		}

		//删除的数据的数量
		return $isDelSuccess;
	}

	//修改	通过主键ID单个修改
	protected function baseUpdateById($id,$data)
	{
		$tag 			= isset($data['ctag']) ? $data['ctag'] : '';
		$info			= $this->getOneById($id,$tag);

		if (!empty($info) && !empty($data)) {

			foreach ($data as $key => $value) {

				$info->$key 	= $value;
			}

			$isUpDdate	= $info->allowField(true)->save($info);

			if ($isUpDdate > 0) {

				$cacheDataKey		= 'table_' . $this->name . '_' . $id;

				Cache::rm($cacheDataKey);

				return $this->getOneById($id,$tag);
			}
		}

		return null;
	}

	//修改	通过主键ID批量
	protected function baseUpdateByIds($data = [])
	{
		if (empty($data))  return null;
		foreach ($data as $key => $value) {
			if (!isset($value[$this->pk])){
				unset($data[$key]); continue;
			}
		}

		$res 	= null;
		if (!empty($data)) {
			$res = $this->saveAll($data);
			foreach ($data as $value) Cache::rm('table_' . $this->name . '_' . $value['id']);
		}

		return $res;
	}

	//查询	通过主键ID
	protected function baseGetOneById($id=0,$tag='')
	{
		if ($id <= 0) return null;

		$cacheDataKey		= 'table_' . $this->name . '_' . $id;

		$info 				= Cache::get($cacheDataKey,null);

		if (empty($info)) {

			$info 			= $this->get($id);

			if ($info) {

				$cacheData 	= serialize($info) ;

				$this->setCache($cacheDataKey,$cacheData,$tag);
			}
		}else{

			$info = unserialize($info) ;
		}

		return $info;
	}

	protected function format_where($model,$where)
	{
		if (!empty( $where)) {
        	foreach ( $where as $key => $value) {
        		$model->where ( $value[0],$value[1] ,$value[2]);
        	}
        }
        return $model;
	}

	protected function baseGetDataCount($modelParame)
	{
		$aliasName 		= (isset($modelParame['MainAlias']) && !empty($modelParame['MainAlias'])) ? $modelParame['MainAlias'] : 'Main';
		$MainTab 		= (isset($modelParame['MainTab']) && !empty($modelParame['MainTab'])) ? $modelParame['MainTab'] : '';
		$relationTab 	= (isset($modelParame['RelationTab']) && !empty($modelParame['RelationTab'])) ? $modelParame['RelationTab'] : '';
		$model 			= $this->getDbModel($MainTab)->alias($aliasName);
		$RelationTab	= $this->getRelationTab($relationTab);
        $tables	  		= $RelationTab['tables'];

		if (!empty($tables)) {
			foreach ($tables as $key => $value) {
				$model->join ( $value[0],$value[1],$value[2]);
			}
		}

		//查询条件
        if (isset($modelParame['whereFun']) && !empty($modelParame['whereFun'])) {
        	$apiParame 	= (isset($modelParame['apiParame']) && !empty($modelParame['apiParame'])) ? $modelParame['apiParame'] : [];
        	$whereFun 	= $modelParame['whereFun'];
        	$model   	= $this->$whereFun($model,$apiParame);
        }

		return $model->count();
	}

	protected function getDbModel($model)
	{
		//数据对象初始化
        if (empty($model)) {
        	$model 				= db($this->name);
        }elseif (is_string($model)) {
        	$model 				= db($model);
        }

        return $model;
	}

	//查询 获取列表数据
	protected function baseGetPageList( $modelParame = [] )
	{
        if ( !isset($modelParame['MainTab']) || empty($modelParame['MainTab'])) {
        	$modelParame['MainTab']		= $this->formatModelName($this->name);
        }

        if ( !isset($modelParame['MainAlias']) || empty($modelParame['MainAlias'])) {
        	$modelParame['MainAlias']		= 'main';
        }

        if ( !isset($modelParame['MainField']) || empty($modelParame['MainField'])) {
			$modelParame['MainField']		= [];
        }

        if ( !isset($modelParame['RelationTab']) || empty($modelParame['RelationTab'])) {
			$modelParame['RelationTab']		= [];
        }

		//数据模型对象
        $model 						= $this->getDbModel($modelParame['MainTab'])->alias($modelParame['MainAlias']);
        $RelationTab				= $this->getRelationTab($modelParame['RelationTab']);

        $tables	  					= $RelationTab['tables'];
		$RelationFields				= $RelationTab['fields'];

		if (!empty($tables)) {
			foreach ($tables as $key => $value) {
				$model->join ( $value[0],$value[1] ,$value[2]);
			}
		}

        //获取主键
        $pk 						= $this->pk;

        //表前缀
		$prefix   					= config('database.prefix');

        //排序
        if ((!isset($modelParame['order']) ||$modelParame['order'] === '' || empty($modelParame['order'])) && !empty($pk)) {
            $modelParame['order'] 	= [$pk => 'desc'];
        }

        //查询字段
        $fields 		= '';
        if (empty($modelParame['MainField'])) {
        	$fields 	.= $this->format_fields_string($this->getTableFields($prefix.$modelParame['MainTab'],'fields'),$modelParame['MainAlias']).',';
        }else{
        	$fields 	.= $this->format_fields_string($modelParame['MainField'],$modelParame['MainAlias']).',';
        }

        $fields 		.= $RelationFields;

		$fields			= trim($fields,',');

        //查询条件
        if (isset($modelParame['whereFun']) && !empty($modelParame['whereFun'])) {

        	$apiParame 				= (isset($modelParame['apiParame']) && !empty($modelParame['apiParame'])) ? $modelParame['apiParame'] : [];
        	$whereFun 				= $modelParame['whereFun'];
        	$model   				= $this->$whereFun($model,$apiParame);
        }

        $total 						= $this->baseGetDataCount($modelParame);

        $modelParame['limit'] 		= (isset($modelParame['limit']) && intval($modelParame['limit']) > 0) ? intval($modelParame['limit']) : 15;

        $modelParame['page'] 		= (isset($modelParame['page']) && intval($modelParame['page']) > 0) ? intval($modelParame['page']) : 1;

        $remainder 					= intval($total - $modelParame['limit'] * $modelParame['page']);
        $remainder 					= $remainder >= 0 ? $remainder : 0;

        $total 						= $total ? $total : 0;

        //是否缓存
        if (isset($modelParame['cacheTime']) && intval($modelParame['cacheTime']) > 0) {
        	$cacheTime 		= intval($modelParame['cacheTime']);
        	$lists 	= $model->field($fields)->limit($modelParame['limit'])->order($modelParame['order'])->cache($cacheTime)->page($modelParame['page'])->select();
        }else{
	        $lists 	= $model->field($fields)->limit($modelParame['limit'])->order($modelParame['order'])->page($modelParame['page'])->select();
        }

        return ['total'=>$total,'page'=>$modelParame['page'],'limit'=>$modelParame['limit'],'remainder'=>$remainder,'lists'=>$lists];
	}

	//定义关联查询表以及字段
	protected function getRelationTab($RelationTab)
	{
		$tables	  		= [];
		$fields 		= '';
		if (!empty($RelationTab))
		{
			$prefix   		= config('database.prefix');
			foreach ($RelationTab as $key=>$val)
			{
				$tab 		= explode('|', $key);
				$Rtables	= isset($tab[0]) ? $tab[0] : '';
				$Ron		= trim($val['Ron']);
				$Rfield		= $val['Rfield'];
				$Ralias		= $val['Ralias'];

				$Rtype		= (!isset($val['Rtype']) || empty($val['Rtype']) || !in_array(strtoupper($val['Rtype']),['INNER','LEFT','RIGHT','FULL'])) ? 'INNER' : strtoupper($val['Rtype']);

				if (empty($Rtables) || empty($Ron) || empty($Ralias)){
					continue;
				}else{
					$tables[] 	= [$prefix.$Rtables . ' ' . $Ralias ,$Ron,$Rtype];
					if ($Rfield === true || empty($Rfield)){
						$fields				.= $this->format_fields_string($this->getTabInfo($prefix.$Rtables,'fields'),$Ralias).',';
					}elseif (is_string($Rfield)){
						$fields				.= $this->format_fields_string(implode(',', $Rfield),$Ralias).',';
					}elseif (is_array($Rfield)){
						$fields				.= $this->format_fields_string($Rfield,$Ralias).',';
					}
				}
			}
		}

		return array('tables'=>$tables,'fields'=>$fields);
	}

	protected function format_fields_string($fields, $prefix = '')
	{
        if ($prefix != '') {
            foreach ($fields as $key => $val) {
                $fields[$key] = $prefix . '.' . $val;
            }
        }
        return implode(',', $fields);
    }

    private function formatModelName($name)
    {
	  $temp_array 		= array();

	  for($i=0;$i<strlen($name);$i++){

	    $ascii_code 	= ord($name[$i]);

	    if($ascii_code >= 65 && $ascii_code <= 90){

	      if($i == 0){

	         $temp_array[] = chr($ascii_code + 32);
	      }else{

	        $temp_array[] = '_'.chr($ascii_code + 32);
	      }
	    }else{

	      $temp_array[] = $name[$i];
	    }
	  }

	  return implode('',$temp_array);
	}
}
