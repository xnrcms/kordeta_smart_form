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

class Tpldata extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = '';
    private $formInfo           = [];
	
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
        
        //初始化主表名称
        if (is_array($this->getTplDataTableName($this->postData)))
        return $this->returnData($this->getTplDataTableName($this->postData));

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
        $dbModel            = model($this->mainTable);
 
		/*定义数据模型参数*/
		//主表名称，可以为空，默认当前模型名称
		$modelParame['MainTab']		= $this->mainTable;

		//主表名称，可以为空，默认为main
		$modelParame['MainAlias']	= 'main';

		//主表待查询字段，可以为空，默认全字段
		$modelParame['MainField']	= [];

		//定义关联查询表信息，默认是空数组，为空时为单表查询,格式必须为一下格式
		//Rtype :`INNER`、`LEFT`、`RIGHT`、`FULL`，不区分大小写，默认为`INNER`。
		$RelationTab				      = [];
		$RelationTab['user_center|1']	= array('Ralias'=>'uc1','Ron'=>'uc1.id=main.creator_id','Rtype'=>'LEFT','Rfield'=>array('username as creator_name'));
        $RelationTab['user_center|2']   = array('Ralias'=>'uc2','Ron'=>'uc2.id=main.modifier_id','Rtype'=>'LEFT','Rfield'=>array('username as modifier_name'));

		$modelParame['RelationTab']	= $RelationTab;

        //接口数据
        $modelParame['apiParame']   = $parame;

		//检索条件 需要对应的模型里面定义查询条件 格式为formatWhere...
		$modelParame['whereFun']	= 'formatWhereDefault';

		//排序定义
		$modelParame['order']		= 'main.id desc';		
		
		//数据分页步长定义
		$modelParame['limit']		= isset($parame['limit']) ? $parame['limit'] : 20;

		//数据分页页数定义
		$modelParame['page']		= (isset($parame['page']) && $parame['page'] > 0) ? $parame['page'] : 1;

		//定义缓存KEY
        $modelParame['cacheKey']    = [
            isset($parame['search']) ? $parame['search'] : '',
            $modelParame['limit'],
            $modelParame['order'],
            $modelParame['page'],
        ];

		//列表数据
		$lists 						= $dbModel->getList($modelParame);

		//数据格式化
		$data 						= (isset($lists['lists']) && !empty($lists['lists'])) ? $lists['lists'] : [];

        $tableHead                  = isset($this->formInfo['list_config']) ? $this->formInfo['list_config'] : '';

        foreach ($data as $key => $value)
        {
            $data[$key]['create_time']  = !empty($value['create_time']) ? date('Y-m-d H:i:s',$value['create_time']) : '/';
            $data[$key]['update_time']  = !empty($value['create_time']) ? date('Y-m-d H:i:s',$value['create_time']) : '/';
            foreach ($value as $kk => $vv)
            {
                if ($this->getFieldType($kk) == 'date')
                {
                    $data[$key][$kk]     = !empty($vv) && is_numeric($vv) ? date('Y-m-d',$vv) : $vv;
                }
            }
        }

    	$lists['listData'] 			= !empty($data) ? json_encode($data) : '';
        $lists['tableHead']         = $tableHead;

    	return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$lists];
    }

    /**
     * 接口数据添加/更新
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function saveData($parame)
    {
        //主表数据库模型
    	$dbModel					= model($this->mainTable);

        //处理表单数据
        $formData                   = isset($parame['formData']) ? ($parame['formData']) : '';
        
        if (empty($formData) || !is_json($formData))
        return ['Code' => '203', 'Msg'=>lang('notice_json_format_error')];

        //表单提交的原始数据
        $formData                   = json_decode($formData,true);
        $formField                  = $this->getFormTplField();
        $saveData                   = [];
        $defField                   = ['id','create_time','update_time','creator_id','modifier_id'];

        $saveData['update_time']    = time();
        $saveData['modifier_id']    = $this->getUserId();

        foreach ($formField as $key => $value)
        {
            if (in_array($value, $defField)) continue;

            if ($this->getFieldType($value) == 'date') {
                $saveData[$value]   = isset($formData[$value]) ? strtotime($formData[$value]) : 0;
            }else{
                $saveData[$value]   = isset($formData[$value]) ? $formData[$value] : '';
            }
        }

        if (empty($saveData)) return ['Code' => '203', 'Msg'=>lang('notice_helper_data_error')];

        //数据ID
        $id                         = isset($formData['id']) ? intval($formData['id']) : 0;
		
        //通过ID判断数据是新增还是更新 定义新增条件下数据
    	if ($id <= 0)
        {
            $saveData['create_time']    = time();
            $saveData['creator_id']     = $this->getUserId();
    	}

    	$info                                       = $dbModel->saveData($id,$saveData);

        return !empty($info) ? ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info] : ['Code' => '203', 'Msg'=>lang('100015')];
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
        $info               = $dbModel->getRow($id);

        foreach ($info as $key => $value)
        {
            if ($this->getFieldType($key) == 'date')
            {
                $info[$key]     = !empty($value) && is_numeric($value) ? date('Y-m-d',$value) : $value;
            }
        }

        //数据详情
        $dataInfo           = $id > 0 ? json_encode($info) : '';
        $formInfo           = isset($this->formInfo['form_config']) ? $this->formInfo['form_config'] : '';

        $data               = [];
        $data['dataInfo']   = $dataInfo;
        $data['formInfo']   = $formInfo;

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$data];
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
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

        //根据ID更新数据
        $info               = $dbModel->saveData($id,[$parame['fieldName']=>$parame['updata']]);

        return !empty($info) ? ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info] : ['Code' => '100015', 'Msg'=>lang('100015')];
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

    /*api:4d753ba634975416b970f2887028e304*/
    /**
     * * 数据导出（Excel文件）接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function export($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        $dataType               = isset($parame['dataType']) ? intval($parame['dataType']) : 0;
        $dataType = 2;
        /*if (!in_array($dataType, [1,2]))
        return ['Code' => '203', 'Msg'=>lang('notice_data_type_error')];*/

        //自行书写业务逻辑代码
        $title          = $this->formInfo['title'];
        $form_config    = $this->formInfo['form_config'];
        $formTplData    = !empty($form_config) ? json_decode($form_config,true) : [];

        //表头异常
        if (!isset($formTplData['list']) || empty($formTplData['list']))
        return ['Code' => '203', 'Msg'=>lang('notice_table_head_error')];

        //处理表头
        $tableHead      = $formTplData['list'];

        //创建一个处理对象实例
        $objExcel = new \PHPExcel();

        //设置文档基本属性
        $objProps = $objExcel->getProperties();
        $objProps->setCreator("KorDeta");
        $objProps->setLastModifiedBy("KorDeta");
        $objProps->setTitle($title);
        $objProps->setSubject($title . ($dataType == 1 ? '数据' : '数据模板'));
        $objProps->setDescription($title . ($dataType == 1 ? '数据' : '数据模板'));
        $objProps->setKeywords($dataType == 1 ? 'exportData' : 'exportDataTpl');
        $objProps->setCategory($dataType == 1 ? 'exportData' : 'exportDataTpl');
        
        //设置表格
        $objExcel->setActiveSheetIndex(0);//第一页
        $objActSheet = $objExcel->getActiveSheet();
        
        $objActSheet->setTitle( $title . '表');

        //表头
        foreach ($tableHead as $tkey => $tval)
        {
            //设置单元格内容
            $columns        = $this->getExcelColumnName($tkey);
            $rows           = 1;
            $tableHeadName  = $tval['name'];

            if (empty($columns))
            return ['Code' => '203', 'Msg'=>lang('notice_table_column_error')];//列错误

            $cr             = $columns . $rows;

            $objActSheet->setCellValue($cr, $tableHeadName);
            $objActSheet->getStyle($cr)->getAlignment()->setWrapText(true);
            $objActSheet->getStyle($cr)->getFont()->setSize(10);//设置文字大小
            //$objActSheet->getStyle($cr)->getFont()->setBold(true);
            //$objActSheet->getStyle($cr)->getFont()->setName('微软雅黑');
            $objActSheet->getColumnDimension($columns)->setWidth(50);//设置列宽度
        }
        
        //表体
        if ($dataType == 2) //列表模板
        {
            foreach ($tableHead as $tkey => $tval)
            {
                //设置单元格内容
                $columns        = $this->getExcelColumnName($tkey);
                $rows           = 2;
                $tableHeadName  = $tval['name'];
                $cr             = $columns . $rows;

                if (in_array($tval['type'], ['select','radio','checkbox']))
                {
                    $options    = isset($tval['options']['options']) ? $tval['options']['options'] : [];
                    $opts       = [];
                    foreach ($options as $oval) $opts[] = $oval['value'];

                    $optStr     = !empty($opts) ? implode(',', $opts) : '';

                    $objActSheet->getCell($cr)->getDataValidation()
                    -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
                    -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
                    -> setAllowBlank(false)
                    -> setShowInputMessage(true)
                    -> setShowErrorMessage(true)
                    -> setShowDropDown(true)
                    -> setErrorTitle('输入的值有误')
                    -> setError('您输入的值不在下拉框列表内.')
                    -> setPromptTitle($tableHeadName)
                    -> setFormula1('"'.$optStr.'"');
                }

                if (in_array($tval['type'], ['date']))
                {
                    # code...
                }

                if (in_array($tval['type'], ['input','textarea']))
                {
                    $objActSheet->getStyle($cr)->getAlignment()->setWrapText(true);
                }

                $objActSheet->getStyle($cr)->getFont()->setSize(10);//设置文字大小
                //$objActSheet->getStyle($cr)->getFont()->setBold(true);
                //$objActSheet->getStyle($cr)->getFont()->setName('微软雅黑');
                $objActSheet->getColumnDimension($columns)->setWidth(50);//设置列宽度
            }
        }

        $outputFileName = $title . "-" . date('Y-m-d') . '-' . time() . ".xlsx";//生成的文件名

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$outputFileName\"");
        header('Cache-Control: max-age=0');

        //创建文件格式写入对象实例
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');

        $objWriter->save('php://output'); //文件通过浏览器下载

        //需要返回的数据体
        $Data                   = ['id'=>100];

        return ['Code' => '200', 'Msg'=>lang('200'),'Data'=>$Data];
    }

    /*api:4d753ba634975416b970f2887028e304*/

    /*接口扩展*/

    private function getTplDataTableName($parame = [])
    {
        $menuid                     = isset($parame['menuid']) ? (int)$parame['menuid'] : 0;
        if ($menuid <= 0) return ['Code' => '203', 'Msg'=>lang('notice_menuid_empty')];

        $devform2Model              = model("devform2");

        //获取表单数据
        $formInfo                   = $devform2Model->getFormInfoByMenuId($menuid);
        if (empty($formInfo)) return ['Code' => '203', 'Msg'=>lang('notice_formtpl_not_exists')];

        $tablePrefix    = config("database.prefix");
        $tableName      = "kor_table" . $formInfo['id'];
        $isTable        = $devform2Model->query('SHOW TABLES LIKE "' . $tablePrefix . $tableName . '"');

        if (empty($isTable)) return ['Code' => '203', 'Msg'=>lang('notice_table_not_exists')];

        //检测模板文件是否存在
        $modelName  = formatStringToHump($tableName);

        //检测文件是否存在
        $file       = \Env::get('APP_PATH') .'common/model/'. $modelName .'.php';

        if (!file_exists($file)) return ['Code' => '203', 'Msg'=>lang('notice_model_not_exists')];

        $this->mainTable    = $tableName;
        $this->formInfo     = $formInfo;

        return $this->mainTable;
    }

    private function getFormTplField()
    {
        $formTplData    = (isset($this->formInfo['form_config']) && !empty($this->formInfo['form_config'])) ? json_decode($this->formInfo['form_config'],true) : [];
        $listsData      = isset($formTplData['list']) ? $formTplData['list'] : [];
        $formField      = [];

        foreach ($listsData as $value) $formField[$value['model']] = $value['model'];

        return $formField;
    }

    private function getFieldType($fieldName = '')
    {
        if (empty($fieldName))  return '';

        $field      = explode('_', $fieldName);

        return isset($field[0]) ? $field[0] : '';
    }

    private function getExcelColumnName($index = 0)
    {
        $column = explode(',', 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,AA,AB,AC,AD,AE,AF,AG,AH,AI,AJ,AK,AL,AM,AN,AO,AP,AQ,AR,AS,AT,AU,AV,AW,AX,AY,AZ,BA,BB,BC,BD,BE,BF,BG,BH,BI,BJ,BK,BL,BM,BN,BO,BP,BQ,BR,BS,BT,BU,BV,BW,BX,BY,BZ');
        return isset($column[$index]) ? $column[$index] : '';
    }
}
