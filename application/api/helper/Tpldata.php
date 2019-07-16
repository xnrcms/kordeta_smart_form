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
        if (!in_array($dataType, [1,2]))
        return ['Code' => '203', 'Msg'=>lang('notice_data_type_error')];

        //自行书写业务逻辑代码
        $title          = $this->formInfo['title'];
        $form_config    = $this->formInfo['form_config'];
        $formTplData    = !empty($form_config) ? json_decode($form_config,true) : [];

        //表头异常
        if (!isset($formTplData['list']) || empty($formTplData['list']))
        return ['Code' => '203', 'Msg'=>lang('notice_table_head_error')];

        $total          = 0;
        $lists          = [];

        //导出数据需要校验是否有数据
        if ($dataType == 1) 
        {
            $parame['search']   = $this->formatSearch($parame);
            $parame['limit']    = 2000;
            $listData  = $this->listData($parame);
            $total     = isset($listData['Data']['total']) ? (int)$listData['Data']['total'] : 0;
            //if ($total <= 0) return ['Code' => '203', 'Msg'=>lang('notice_table_data_empty')];
            
            $lists     = $listData['Data']['lists'];
        }

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

        //合并单元格
        $sc         = $this->getExcelColumnName(0) . '1';
        $ec         = $this->getExcelColumnName(count($tableHead)-1) . '1';
        $objActSheet->mergeCells($sc . ':' . $ec);

        if ($dataType == 1)
        {
            //设置单元格内容
            $objActSheet->setCellValue('A1', $title);
            $objActSheet ->getStyle('A1')->getAlignment()->setWrapText(true);//设置 A1 自动换行
            
            //设置 A1 宽度
            $objStyleA1 = $objActSheet->getStyle('A1');
            $objStyleA1->getFont()->setColor(
                    new \PHPExcel_Style_Color(\PHPExcel_Style_Color::COLOR_BLACK ));
            $objStyleA1->getFont()->setBold(true);
            $objStyleA1->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }else{
            //设置单元格内容
            $objActSheet->setCellValue('A1', '填写要求：
                    1：红色字体为必填项，请务必输入正确的信息，否则将无法导入成功.
                    2：带有下拉框的内容，请选择符合要求的选项即可，请勿手动填写.
                    3：如有多选项的内容，请按照标题栏中的选项进行填写，以英文逗号隔开.
                    4：日期格式为yyyy-mm-dd，如2019-02-03.
                    5：选项类的内容填写了不属于该内容的选项，将会出现无法导入的情况，请务必按照模板给出的选项进行选择或填写.
                    '
            );
            
            $objActSheet ->getStyle('A1')->getAlignment()->setWrapText(true);//设置 A1 自动换行
            $objActSheet->getRowDimension('1')->setRowHeight(120);//设置 A1 行高

            //设置 A1 宽度
            $objStyleA1 = $objActSheet->getStyle('A1');
            $objStyleA1->getFont()->setColor(
                    new \PHPExcel_Style_Color(\PHPExcel_Style_Color::COLOR_RED ));
            $objStyleA1->getFont()->setBold(true);
        }

        //表头
        foreach ($tableHead as $tkey => $tval)
        {
            //设置单元格内容
            $columns        = $this->getExcelColumnName($tkey);
            $rows           = 2;
            $tableHeadName  = $tval['name'];
            $required       = isset($tval['options']['required']) ? (int)$tval['options']['required'] : 0;

            if (empty($columns))
            return ['Code' => '203', 'Msg'=>lang('notice_table_column_error')];//列错误

            $cr             = $columns . $rows;
            $tips           = '';
            if (in_array($tval['type'], ['select','radio']))
            {   
                $optStr         = $this->getTableFieldOptions($tval);
                $tips           = !empty($optStr) ? "(单选项：".$optStr.")" : '';
            }

            if (in_array($tval['type'], ['checkbox']))
            {   
                $optStr         = $this->getTableFieldOptions($tval);
                $tips           = !empty($optStr) ? "(多选项：".$optStr.")" : '';
            }

            if (in_array($tval['type'], ['date']))
            {
                $tips           = '(日期格式：yyyy-mm-dd)';
            }

            $objActSheet->setCellValue($cr, $tableHeadName);
            $objActSheet->getStyle($cr)->getAlignment()->setWrapText(true);
            $objActSheet->getStyle($cr)->getFont()->setSize(10);//设置文字大小
            $objActSheet->getColumnDimension($columns)->setWidth(50);//设置列宽度

            //模板特性
            if ($dataType == 2)
            {
                if ($required === 1) 
                {
                    $objActSheet->getStyle($cr)->getFont()->setColor( new \PHPExcel_Style_Color( \PHPExcel_Style_Color::COLOR_RED ) );
                }

                if (!empty($tips))
                {
                    $objRichText = new \PHPExcel_RichText();
                    $objRichText->createText($tableHeadName);
                    $objPayable  = $objRichText->createTextRun(" " . $tips);
                    $objPayable->getFont()->setColor( new \PHPExcel_Style_Color( \PHPExcel_Style_Color::COLOR_BLUE ) );
                    $objActSheet->setCellValue($cr, $objRichText);
                }
            }
        }

        //表体
        for ($i=0; $i <= $total; $i++)
        {
            $exportData     = isset($lists[$i]) ? $lists[$i] : [];

            foreach ($tableHead as $tkey => $tval)
            {
                //设置单元格内容
                $columns        = $this->getExcelColumnName($tkey);
                $rows           = $i + 3;
                $tableHeadName  = $tval['name'];
                $cr             = $columns . $rows;

                if (in_array($tval['type'], ['select','radio']))
                {
                    $optStr     = $this->getTableFieldOptions($tval);

                    $objActSheet->getCell($cr)->getDataValidation()
                    -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
                    -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
                    -> setAllowBlank(false)
                    -> setShowInputMessage(true)
                    -> setShowErrorMessage(true)
                    -> setShowDropDown(true)
                    -> setErrorTitle('输入的值有误')
                    -> setError('您输入的值不在下拉列表内.')
                    -> setPromptTitle($tableHeadName)
                    -> setFormula1('"'.$optStr.'"');
                }

                if (in_array($tval['type'], ['date']))
                {
                    $objActSheet->getStyle($cr)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
                }

                if (in_array($tval['type'], ['input','textarea']))
                {
                    $objActSheet->getStyle($cr)->getAlignment()->setWrapText(true);
                }

                $texts    = isset($exportData[$tval['model']]) ? $exportData[$tval['model']] :'';
                if (!empty($texts))
                {
                    $texts  = $tval['type'] == 'date' ? date('Y-m-d',$texts) : $texts;
                    $objActSheet->setCellValue($cr, $texts);
                    $objActSheet->getStyle($cr)->getAlignment()->setWrapText(true);
                }

                $objActSheet->getStyle($cr)->getFont()->setSize(10);//设置文字大小
                $objActSheet->getColumnDimension($columns)->setWidth(50);//设置列宽度
            }
        }

        $excelType      = 'Excel2007';
        $excelExt       = ['Excel5'=>'.xls','Excel2007'=>'.xlsx'];
        $excelName      = $excelExt[$excelType];
        $outputFileName = $title . "-" . date('Y-m-d') . '-' . time(); 
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$outputFileName . $excelName . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        //创建文件格式写入对象实例
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, $excelType);

        $objWriter->save('php://output');exit(0);
    }

    /*api:4d753ba634975416b970f2887028e304*/

    /*api:c0a528dae73c02dcdde8fc2d456a5e48*/
    /**
     * * 数据导入（Excel文件）接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function import($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码
        $title          = $this->formInfo['title'];
        $form_config    = $this->formInfo['form_config'];
        $formTplData    = !empty($form_config) ? json_decode($form_config,true) : [];

        //表头异常
        if (!isset($formTplData['list']) || empty($formTplData['list']))
        return ['Code' => '203', 'Msg'=>lang('notice_table_head_error')];

        $tableHead      = $formTplData['list'];
        $total          = count($tableHead);
        $sc             = $this->getExcelColumnName(0);
        $ec             = $this->getExcelColumnName($total-1);

        //文件上传
        $uploads        = $this->uploadExcel($parame);
        if (!(isset($uploads['Code']) && $uploads['Code'] == '200')) return $uploads;
        
        $filePath       = $uploads['Data'];
        $inputFileType  = \PHPExcel_IOFactory::identify($filePath);
        $PHPReader      = \PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel       = $PHPReader->load($filePath);
        $currentSheet   = $PHPExcel->getSheet(0);
        $allColumn      = $currentSheet->getHighestColumn();
        $allRow         = $currentSheet->getHighestRow();
        
        if (!($ec === $allColumn))
        {
            unlink($filePath);
            return ['Code' => '203', 'Msg'=>lang('notice_table_column_error2')];
        }

        //检测是否有数据
        if ($allRow <= 2)
        {
            unlink($filePath);
            return ['Code' => '203', 'Msg'=>lang('notice_import_data_empty')];
        }

        //处理表头跟列对应
        $tableHeadAndColumn     = [];
        for ($c = 0; $c < $total; $c++)
        {
            $currentColumn  = $this->getExcelColumnName($c);
            $address        = $currentColumn . '2';
            $cell           = $currentSheet->getCell($address)->getValue();
            if ($cell instanceof \PHPExcel_RichText)
            {
                $cell = $cell->__toString();
            }

            $cell           = explode('(', $cell);
            $cell           = isset($cell[0]) ? trim($cell[0]) : '';

            foreach ($tableHead as $tkey => $tvalue)
            {
                if ($tvalue['name'] === $cell)
                {
                    $required       = isset($tvalue['options']['required']) ? (int)$tvalue['options']['required'] : 0;
                    $options        = isset($tvalue['options']['options']) ? $tvalue['options']['options'] : [];
                    $tableHeadAndColumn[$currentColumn]     = [
                        $tvalue['type'],$tvalue['model'],$tvalue['name'],$required,$options
                    ];
                    break;
                }
            }
        }

        if (!($total === count($tableHeadAndColumn)))
        {
            unlink($filePath);
            return ['Code' => '203', 'Msg'=>lang('notice_table_column_error3')];
        }

        //数据数据源
        $saveData             = [];
        for ($currentRow = 3; $currentRow <= $allRow; $currentRow++)
        {
            for ($c = 0; $c < $total; $c++)
            {
                //数据坐标
                $currentColumn  = $this->getExcelColumnName($c);
                $address        = $currentColumn . $currentRow;

                if (!isset($tableHeadAndColumn[$currentColumn]))
                {
                    unlink($filePath);
                    return ['Code' => '203', 'Msg'=>lang('notice_table_column_error3')];
                }

                $fieldInfo     = $tableHeadAndColumn[$currentColumn];

                //读取到的数据，保存到数组$data中

                if (in_array($fieldInfo[0], ['date']))
                {
                    $cell       = $currentSheet->getCell($address)->getFormattedValue();

                    //检验日期格式
                    if ($cell <= 0)
                    return ['Code' => '203', 'Msg'=>lang('notice_table_column_date',[$currentColumn,$currentRow])];
                }else{
                    $cell = $currentSheet->getCell($address)->getValue();
                    $cell = ($cell instanceof \PHPExcel_RichText) ? $cell->__toString() : $cell;
                }

                if ($fieldInfo[3] == 1 && empty($cell))
                {
                    unlink($filePath);
                    return ['Code' => '203', 'Msg'=>lang('notice_table_column_required',[$currentColumn,$currentRow])];
                }

                //单选校验
                if (in_array($fieldInfo[0], ['select','radio']))
                {
                    # code...
                }

                //单选校验
                if (in_array($fieldInfo[0], ['checkbox']))
                {
                    # code...
                }

                $saveData[$currentRow - 1][$fieldInfo[1]] = $cell;
            }

            $saveData[$currentRow - 1]['create_time']   = time();
            $saveData[$currentRow - 1]['update_time']   = time();
            $saveData[$currentRow - 1]['creator_id']    = $this->getUserId();
            $saveData[$currentRow - 1]['modifier_id']   = $this->getUserId();
        }

        if (empty($saveData))
        {
            unlink($filePath);
            return ['Code' => '203', 'Msg'=>lang('notice_import_data_empty')];
        }

        $dbModel->saveDataAll($saveData);

        //需要返回的数据体
        $Data                   = ['isok'=>1];

        return ['Code' => '200', 'Msg'=>lang('200'),'Data'=>$Data];
    }

    /*api:c0a528dae73c02dcdde8fc2d456a5e48*/

    /*接口扩展*/

    private function uploadExcel($parame)
    {
        //获取有关图片上传的设置
        $config             = ['size'=> 10*1024*1024,'ext'=>'xlsx,xls'] ;

        //获取表单上传的文件
        $files              = request()->file('fileName') ;
        $re                 = [];

        if(empty($files)) return ['Code'=>'203', 'Msg' => lang('notice_upload_file_empty')] ;

        $fileUploadRoot     = './uploads/excel/';

        //上传文件验证
        $info               = $files->validate($config)->rule('md5')->move($fileUploadRoot) ;

        if($info === false){
            return ['Code' =>'203', 'Msg'=>lang('notice_upload_file_fail',[$files->getError()])] ;
        }else{
            $path                  = $fileUploadRoot . $info->getSaveName();
        }

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$path];
    }

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

    private function getTableFieldOptions($data = [])
    {
        $options    = isset($data['options']['options']) ? $data['options']['options'] : [];
        $opts       = [];

        foreach ($options as $oval) $opts[] = $oval['value'];

        return !empty($opts) ? implode(',', $opts) : '';
    }

    private function formatSearch($parame = [])
    {
        if (!isset($parame['search']) || empty($parame['search'])) return '';

        $search         = explode('kds001', $parame['search']);
        $sch            = [];

        foreach ($search as $key => $value)
        {
            $value      = explode('kds000', $value);
            $sk         = isset($value[0]) ? $value[0] : '';
            $sv         = isset($value[1]) ? $value[1] : '';
            if (!empty($sk) && !empty($sv))
            {
                $svArr          = explode(',', $sv);
                $svCount        = count($svArr);
                $sch[$sk]       = $svCount === 1 ? $svArr[0] : [$svArr[0],$svArr[1]];
            }
        }

        return !empty($sch) ? json_encode($sch) : '';
    }
}
