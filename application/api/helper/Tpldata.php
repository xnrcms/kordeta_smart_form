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

        //自行书写业务逻辑代码

        // 创建一个处理对象实例
        $objExcel = new \PHPExcel();

        //设置文档基本属性
        $objProps = $objExcel->getProperties();
        $objProps->setCreator("Zeal Li");
        $objProps->setLastModifiedBy("Zeal Li");
        $objProps->setTitle("Office XLS Test Document");
        $objProps->setSubject("Office XLS Test Document, Demo");
        $objProps->setDescription("kol document, generated by PHPExcel.");
        $objProps->setKeywords("office excel PHPExcel");
        $objProps->setCategory("Test");
     
        //设置当前的sheet索引，用于后续的内容操作。
        //一般只有在使用多个sheet的时候才需要显示调用。
        //缺省情况下，PHPExcel会自动创建第一个sheet被设置SheetIndex=0
        $objExcel->setActiveSheetIndex(0);
     
        $objActSheet = $objExcel->getActiveSheet();
     
        //设置当前活动sheet的名称
        $objActSheet->setTitle('kol用户表');
     
        //合并单元格
        $objActSheet->mergeCells('A1:P1');

        //设置单元格内容
        $objActSheet->setCellValue('A1', '填写要求：
                1：请填完整每个格子的内容，相同内容请复制填写完整。
                2：没有的数据可以空着不填写。
                3：价格的单位默认为“元/条”，广告频次单位为“次/天”，注册日期，格式为日期格式；
                4：联系方式必须严格要求是QQ:XXXXXXXX 或 Email:XXXXXXXXXX
                      或 Phone:XXXXXXXXXXX 之间用“/”分割,之间空格不限制
                5：字段的格式详细要求如下：字段 "一级分类" "二级类型" "认证类型" "平台分类"'); //字符串内容
        $objActSheet->setCellValue('A2', '一级分类');
        $objActSheet->setCellValue('B2', '二级分类');
        $objActSheet->setCellValue('C2', '账号名称');
        $objActSheet->setCellValue('D2', '账号的地址');
        $objActSheet->setCellValue('E2', '粉丝数');
        $objActSheet->setCellValue('F2', '粉丝级别');
        $objActSheet->setCellValue('G2', '认证类型');
        $objActSheet->setCellValue('H2', '认证信息');
        $objActSheet->setCellValue('I2', '是否精品');
        $objActSheet->setCellValue('J2', '平台分类');
        $objActSheet->setCellValue('K2', '注册日期 ');
        $objActSheet->setCellValue('L2', '最低价格');
        $objActSheet->setCellValue('M2', '最高价格');
        $objActSheet->setCellValue('N2', '硬广报价');
        $objActSheet->setCellValue('O2', '软广报价');
        $objActSheet->setCellValue('P2', '微任务直发原价');
        $objActSheet->setCellValue('Q2', '微任务转发原价');
        $objActSheet->setCellValue('R2', '大客户微任务价格');
        $objActSheet->setCellValue('S2', '税点(%)');
        $objActSheet->setCellValue('T2', '广告频次');
        $objActSheet->setCellValue('U2', '真实姓名');
        $objActSheet->setCellValue('V2', '联系方式');
        $objActSheet->setCellValue('W2', '个性信息 ');
        $objActSheet->setCellValue('X2', '备注');
         
        //A1自动换行
        $objActSheet ->getStyle('A1')->getAlignment()->setWrapText(true);
     
        // 设置行高
        $objActSheet->getRowDimension('1')->setRowHeight(100);
         
        //设置默认宽度以及对齐方式
        $aligment = $objActSheet->getDefaultStyle()->getAlignment();
        $objActSheet->getDefaultColumnDimension()->setWidth(12);
        $aligment->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $aligment->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objActSheet->getDefaultRowDimension()->setRowHeight(25);//默认高度
     
        //设置特定单元格宽度
        $objActSheet->getColumnDimension('D')->setWidth(17);//url
        $objActSheet->getColumnDimension('P')->setWidth(20);//微任务直发原价
        $objActSheet->getColumnDimension('Q')->setWidth(20);//微任务转发原价
        $objActSheet->getColumnDimension('R')->setWidth(20);//大客户微任务价格
        $objActSheet->getColumnDimension('V')->setWidth(20);//联系方式
     
        //设置宽度
        $objStyleA1 = $objActSheet->getStyle('A1');
        $objStyleA1->getFont()->setColor(
                new \PHPExcel_Style_Color(\PHPExcel_Style_Color::COLOR_RED ));
        $objStyleA1->getFont()->setBold(true);
     
        //设置单元格边框样式
        $styleThinBlackBorderOutline = array(
            'borders'=>array(
                'outline'=>array(
                    'style'=>\PHPExcel_Style_Border::BORDER_THICK,//设置border样式
                    'color'=>array('argb'=>'#273039'),//设置border颜色
                ),
            ),
        );
        
        $phpexcel_date_format = array(
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD,
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYMINUS, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_DMMINUS, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_MYMINUS, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME1, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME2, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME5, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME6, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME7, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME8, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX16, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX17, 
    \PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX22
);

        //设置单元格字体和边框
        for ($i=65; $i<89; $i++) {
            $temp = chr($i);
            $style = $objActSheet->getStyle("{$temp}2");
            $style->getFont()->setBold(true);
            $style->getFont()->setName('微软雅黑');
            $style->applyFromArray($styleThinBlackBorderOutline);
        }
     

     foreach ($phpexcel_date_format as $key => $value) {
         //$cell = $worksheet->getCellByColumnAndRow(2, $key + 1);
         //$cell->setValue(time())->setFormatCode($value);
         /*$objActSheet->setCellValue('C'.($key+2), \PHPExcel_Shared_Date::PHPToExcel(time()));*/
         $objActSheet->getStyle('C'.($key+2))->getNumberFormat()->setFormatCode($value);

         $objActSheet->getCell("A".($key+2))->getDataValidation()
           -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
           -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
           -> setAllowBlank(false)
           -> setShowInputMessage(true)
           -> setShowErrorMessage(true)
           -> setShowDropDown(true)
           -> setErrorTitle('输入的值有误')
           -> setError('您输入的值不在下拉框列表内.')
           -> setPromptTitle('设备类型')
           -> setFormula1('"列表项1,列表项2,列表项3"');
    }

        //保护单元格
        /*$objExcel->getSheet(0)->getProtection()->setSheet(true);
        $objExcel->getSheet(0)->protectCells('A1', 'PHPExcel');*/
     
        $outputFileName = "template.xlsx";//生成的文件名

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
}
