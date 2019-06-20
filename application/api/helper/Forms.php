<?php
/**
 * XNRCMS<562909771@qq.com>
 * ============================================================================
 * 版权所有 2018-2028 小能人科技有限公司，并保留所有权利。
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Helper只要处理业务逻辑，默认会初始化数据列表接口、数据详情接口、数据更新接口、数据删除接口、数据快捷编辑接口
 * 如需其他接口自行扩展，默认接口如实在无需要可以自行删除
 */
namespace app\api\helper;

use app\common\helper\Base;
use think\facade\Lang;

class Forms extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'devform2';
	
	public function __construct($parame=[],$className='',$methodName='',$modelName='')
    {
        parent::__construct($parame,$className,$methodName,$modelName);
    }
    
    /**
     * 初始化接口 固定不用动
     * @param  [array]  $parame     接口需要的参数
     * @param  [string] $className  类名
     * @param  [string] $methodName 方法名
     * @return [array]              接口输出数据
     */
	public function apiRun()
    {   
        if (!$this->checkData($this->postData)) return json($this->getReturnData());
        
        //加载验证器
        $this->dataValidate = new \app\api\validate\DataValidate;
        
        //规避没有设置主表名称
        if (empty($this->mainTable)) return $this->returnData(['Code' => '120020', 'Msg'=>lang('120020')]);
        
        //接口执行分发
        $methodName     = $this->actionName;
        $data           = $this->$methodName($this->postData);

        //设置返回数据
        $this->setReturnData($data);

        //接口数据返回
        return json($this->getReturnData());
    }

    //支持内部调用
    public function isInside($parame,$aName)
    {
        return $this->$aName($parame);
    }

    /**
     * 接口列表数据
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function listData($parame)
    {
        //主表数据库模型
		$dbModel					= model($this->mainTable);

		/*定义数据模型参数*/
		//主表名称，可以为空，默认当前模型名称
		$modelParame['MainTab']		= $this->mainTable;

		//主表名称，可以为空，默认为main
		$modelParame['MainAlias']	= 'main';

		//主表待查询字段，可以为空，默认全字段
		$modelParame['MainField']	= [];

		//定义关联查询表信息，默认是空数组，为空时为单表查询,格式必须为一下格式
		//Rtype :`INNER`、`LEFT`、`RIGHT`、`FULL`，不区分大小写，默认为`INNER`。
		$RelationTab				= [];
		$RelationTab['devmenu']		= array('Ralias'=>'dm','Ron'=>'dm.id=main.mid','Rtype'=>'LEFT','Rfield'=>array('title as menu_title'));

		$modelParame['RelationTab']	= $RelationTab;

        //接口数据
        $modelParame['apiParame']   = $parame;

		//检索条件 需要对应的模型里面定义查询条件 格式为formatWhere...
		$modelParame['whereFun']	= 'formatWhereDefault';

		//排序定义
		$modelParame['order']		= 'main.id desc';		
		
		//数据分页步长定义
		$modelParame['limit']		= isset($parame['limit']) ? $parame['limit'] : 10;

		//数据分页页数定义
		$modelParame['page']		= (isset($parame['page']) && $parame['page'] > 0) ? $parame['page'] : 1;

		//数据缓存是时间，默认0 不缓存 ,单位秒
		$modelParame['cacheKey']	= [];

		//列表数据
		$lists 						= $dbModel->getList($modelParame);

		//数据格式化
		$data 						= (isset($lists['lists']) && !empty($lists['lists'])) ? $lists['lists'] : [];

    	if (!empty($data))
        {
            //自行定义格式化数据输出
    		/*foreach($data as $k=>$v)
            {

    		}*/
    	}

    	$lists['lists'] 			= $data;

    	return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$lists];
    }

    /**
     * 接口数据添加/更新
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function saveData($parame)
    {
        /*//权限校验
        $menuid  = (isset($parame['menuid']) && (int)$parame['menuid'] > 0) ? (int)$parame['menuid'] : 0;
        if (!$this->checkUserPower($menuid)) return ['Code' => '202', 'Msg'=>lang('202')];*/

        //主表数据库模型
    	$dbModel					= model($this->mainTable);

        //数据ID
        $id                         = isset($parame['id']) ? intval($parame['id']) : 0;

        //自行定义入库数据 为了防止参数未定义报错，先采用isset()判断一下
        $saveData                   = [];
        $saveData['title']          = isset($parame['title']) ? $parame['title'] : '';
        $saveData['mid']            = isset($parame['mid']) ? (int)$parame['mid'] : 0;
        $saveData['status']         = isset($parame['status']) ? (int)$parame['status'] : 0;
        $saveData['status']         = $saveData['status'] === 1 ? 1 : 2;
        $saveData['form_config']    = isset($parame['form_config']) ? $parame['form_config'] : '';
        $saveData['list_config']    = isset($parame['list_config']) ? $parame['list_config'] : '';
        $saveData['sort']           = isset($parame['sort']) ? (int)$parame['sort'] : 0;
        $saveData['update_time']    = time();
        //$saveData['parame']         = isset($parame['parame']) ? $parame['parame'] : '';

        //规避遗漏定义入库数据
        if (empty($saveData)) return ['Code' => '120021', 'Msg'=>lang('120021')];

        //自行处理数据入库条件
        //...
		
        //检测表单名称是否存在
        if ($dbModel->checkValue($saveData['title'],$id,'title'))
        return ['Code' => '203', 'Msg'=>lang('notice_title_already_exists')];

        //检测表单绑定的菜单ID是否存在
        if ($dbModel->checkValue($saveData['mid'],$id,'mid'))
        return ['Code' => '203', 'Msg'=>lang('notice_menuid_already_exists')];

        //通过ID判断数据是新增还是更新 定义新增条件下数据
    	if ($id <= 0)
        {
            $saveData['ownerid']            = $this->getOwnerId();
            $saveData['create_time']        = time();
    	}

    	$info                               = $dbModel->saveData($id,$saveData);

        //根据表单数据创建数据表和表字段
        $this->initTableAndField($info);

        return !empty($info) ? ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info] : ['Code' => '100015', 'Msg'=>lang('100015')];
    }

    /**
     * 接口数据详情
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function detailData($parame)
    {
        //主表数据库模型
    	$dbModel			= model($this->mainTable);

        //数据ID
        $id                 = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

        //数据详情
        $info               = $dbModel->getRow($id);

        return !empty($info) ? ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info] : ['Code' => '100015', 'Msg'=>lang('100015')];
    }

    /**
     * 接口数据快捷编辑
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function quickEditData($parame)
    {
        //主表数据库模型
    	$dbModel			= model($this->mainTable);

        //数据ID
        $id                 = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '203', 'Msg'=>lang('120023')];

        if ($parame['fieldName'] == 'status' && $parame['updata'] == 1)
        {
            //需要校验是否还有其他地方被启用
            $info       = $dbModel->getRow($id);
            if ($dbModel->checkFormStatus($info['mid'],$id))
            {
                return ['Code' => '203', 'Msg'=>lang('notice_status_already_exists')];
            }
        }
        //根据ID更新数据
        $info               = $dbModel->saveData($id,[$parame['fieldName']=>$parame['updata']]);

        return !empty($info) ? ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info] : ['Code' => '203', 'Msg'=>lang('100015')];
    }

    /**
     * 接口数据删除
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function delData($parame)
    {
        //主表数据库模型
    	$dbModel				= model($this->mainTable);

        //数据ID
        $id                 = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

        //自行定义删除条件
        //...
        
        //执行删除操作
    	$delCount				= $dbModel->deleteData($id);

    	return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['count'=>$delCount]];
    }

    /*api:fc24913147a441bf30df4639154581af*/
    /**
     * * 校验表单状态接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function checkStatus($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);
        $mid                    = isset($parame['mid']) ? (int)$parame['mid'] : 0;
        $status                 = isset($parame['status']) ? (int)$parame['status'] : 0;

        //需要返回的数据体
        $Data                   = ['isok'=>($status == 1 ? (int)$dbModel->checkFormStatus($mid,0) : 0)];

        return ['Code' => '200', 'Msg'=>lang('200'),'Data'=>$Data];
    }

    /*api:fc24913147a441bf30df4639154581af*/

    /*接口扩展*/

    private function initTableAndField($data = [])
    {
        if (empty($data)) return;

        $dbModel        = model($this->mainTable);
        $tablePrefix    = config("database.prefix");
        $database       = config("database.database");
        $tableName      = $tablePrefix . "kor_table" . $data['id'];

        $isTable        = $dbModel->query('SHOW TABLES LIKE "' . $tableName . '"');
        
        //检查表是否存在 不存在创建
        if (empty($isTable))  $this->createTable($tableName,$data['title'],$tablePrefix);

        $this->createTableField($database,$tableName,$data['form_config']);
        
    }

    private function createTableField($database,$tableName,$form_config)
    {
        $dbModel        = model($this->mainTable);
        $add_field      = [];
        $edit_field     = [];
        $del_field      = [];
        $form_field     = [];
        $sqlArr         = [];
        $fieldInfo      = [];
        $sorts          = [];
        $afterField     = 'FIRST';

        //获取表字段
        $fields         = $dbModel->query("SELECT COLUMN_NAME as field FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . $tableName . "' AND table_schema = '".$database."'");
        
        $allField       = [];

        if (!empty($fields))
        {
            foreach ($fields as $key => $value) $allField[] = $value['field'];
        }

        $defField       = ['id','create_time','update_time','ownerid'];

        //处理表单配置信息
        $form_config    = !empty($form_config) ? json_decode($form_config,true) : [];
        $form_list      = isset($form_config['list']) ? $form_config['list'] : [];

        //取出需要保存的字段信息，页面提交过来的字段数据
        foreach ($form_list as $key => $value)
        {
            $form_field[$value['model']]        = $value['model'];
            $fieldInfo[$value['model']]         = [
                'title' => $value['name'],
                'type'  => $value['type']
            ];
        }

        //取出要删除的字段
        foreach ($allField as $key1 => $value1)
        {   
            $sorts[$value1]     = $afterField;
            $afterField         = "AFTER `".$value1."`";

            if (in_array($value1, $defField)) continue;

            if (!in_array($value1, $form_field)){
                $afterField         = $sorts[$value1];
                $del_field[$value1] = $value1;
            }
        }

        foreach ($form_field as $key2=>$value2)
        {
            if (in_array($value2, $defField)) continue;

            if (in_array($value2, $allField)) $edit_field[$value2]  = $value2;
            if (!in_array($value2, $allField)) $add_field[$value2]   = $value2;
        }

        foreach ($del_field as $dvalue)
        {
            $sqlArr[]   = 'DROP COLUMN `' . $dvalue . '`';
        }

    
        foreach ($edit_field as $evalue)
        {
            $type       = isset($fieldInfo[$evalue]['type']) ? $fieldInfo[$evalue]['type'] : '';
            $comment    = isset($fieldInfo[$evalue]['title']) ? $fieldInfo[$evalue]['title'] : '';
            $sqlArr[]   = "MODIFY COLUMN `".$evalue."` ".$this->getTableFieldType($type)." CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '" . $comment . "123' ". $afterField;

            $afterField = "AFTER `".$evalue."`";
        }

        foreach ($add_field as $avalue)
        {
            $type       = isset($fieldInfo[$avalue]['type']) ? $fieldInfo[$avalue]['type'] : '';
            $comment    = isset($fieldInfo[$avalue]['title']) ? $fieldInfo[$avalue]['title'] : '';
            $sqlArr[]   = "ADD COLUMN `" . $avalue ."` ".$this->getTableFieldType($type)." NULL DEFAULT '' COMMENT '" . $comment . "' " . $afterField;

            $afterField = "AFTER `".$avalue."`";
        }

        if (!empty($sqlArr))
        {
            $sqlStr     = "ALTER TABLE `".$tableName."` ";
            $sqlStr     .= implode(',', $sqlArr);

            $dbModel->query($sqlStr);
        }

        /*wr([
            'sqlStr'=>$sqlStr,
            'sorts'=>$sorts,
            'sqlArr'=>$sqlArr,
            'add_field'=>$add_field,
            'edit_field'=>$edit_field,
            'del_field'=>$del_field
        ]);*/
    }

    private function getTableFieldType($type = '')
    {
        $defType       = "varchar(255)";
        /*if (in_array($type, ['input','checkbox'])) {
           $defType       = "varchar(255)";
        }elseif (in_array($type, ['date','select'])) {
            $defType       = "int(10)";
        }*/

        return $defType;
    }

    private function createTable($tableName,$title,$prefix = '')
    {
        model($this->mainTable)->query("CREATE TABLE `".$tableName."` (
`id`  int(10) NOT NULL AUTO_INCREMENT COMMENT '数据ID' ,
`create_time`  int(10) NOT NULL DEFAULT 0 COMMENT '数据新增时间' ,
`update_time`  int(10) NOT NULL DEFAULT 0 COMMENT '数据修改时间' ,
`ownerid`  int(10) NOT NULL DEFAULT 0 COMMENT '拥有者ID' ,
PRIMARY KEY (`id`) ) COMMENT='自动表单（".$title."）表'");

        //生成模型文件
        $tname      = str_replace($prefix,'',$tableName);
        $modelName  = formatStringToHump($tname);

        if (!empty($modelName))
        {
            //检测文件是否存在
            $file       = \Env::get('APP_PATH') .'common/model/'. $modelName .'.php';
            $base       = \Env::get('APP_PATH') .'common/tpl/ApiTPLM.php';

            if (!file_exists($file) && file_exists($base))
            file_put_contents($file,str_replace('{ModelNameTPL}',$modelName,file_get_contents($base)));
        }
    }
}
