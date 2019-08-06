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
namespace app\admin\helper;

use app\common\helper\Base;
use think\facade\Lang;

class Devapi extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'devapi';
    private $defaultAction      = ['listData','saveData','detailData','quickEditData','delData'];
	
	public function __construct($parame=[],$className='',$methodName='',$modelName='')
    {
        parent::__construct($parame,$className,$methodName,$modelName);
        $this->apidoc           = request()->param('apidoc',0);
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
		//$RelationTab['member']		= array('Ralias'=>'me','Ron'=>'me.uid=main.uid','Rtype'=>'LEFT','Rfield'=>array('nickname'));

		$modelParame['RelationTab']	= $RelationTab;

        //接口数据
        $modelParame['apiParame']   = $parame;

		//检索条件 需要对应的模型里面定义查询条件 格式为formatWhere...
		$modelParame['whereFun']	= 'formatWhereDefault';

		//排序定义
		$modelParame['order']		= 'main.id desc';		
		
		//数据分页步长定义
		$modelParame['limit']		= $this->apidoc == 2 ? 1 : 1000;

		//数据分页页数定义
		$modelParame['page']		= (isset($parame['page']) && $parame['page'] > 0) ? $parame['page'] : 1;

		//数据缓存是时间，默认0 不缓存 ,单位秒
		$modelParame['cacheTime']	= 0;

		//列表数据
		$lists 						= $dbModel->getPageList($modelParame);

		//数据格式化
		$data 						= (isset($lists['lists']) && !empty($lists['lists'])) ? $lists['lists'] : [];

    	if (!empty($data)) {

            //自行定义格式化数据输出
    		//foreach($data as $k=>$v){

    		//}
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
        //主表数据库模型
    	$dbModel					= model($this->mainTable);

        //数据ID
        $id                         = isset($parame['id']) ? intval($parame['id']) : 0;

        //自行定义入库数据 为了防止参数未定义报错，先采用isset()判断一下
        $saveData                   = [];
        $saveData['title']          = isset($parame['title']) ? $parame['title'] : '';
        $saveData['user_id']        = isset($parame['user_id']) ? $parame['user_id'] : 0;
        $saveData['apiurl']         = isset($parame['apiurl']) ? $parame['apiurl'] : '';
        $saveData['description']    = isset($parame['description']) ? $parame['description'] : '';
        $saveData['module_id']      = isset($parame['module_id']) ? $parame['module_id'] : 0;
        $saveData['project_id']     = isset($parame['project_id']) ? $parame['project_id'] : 0;
        $saveData['author']         = isset($parame['author']) ? $parame['author'] : '';
        $saveData['api_type']       = isset($parame['api_type']) ? $parame['api_type'] : 0;
        $saveData['api_module_type']= isset($parame['api_module_type']) ? $parame['api_module_type'] : 0;
        $saveData['update_time']    = time();
        $saveData['urlmd5']         = md5(strtolower($saveData['apiurl'].$saveData['project_id']));
        //$saveData['parame']         = isset($parame['parame']) ? $parame['parame'] : '';

        //数据校验
        if(empty($saveData['title'])) return ['Code' => '100008', 'Msg'=>lang('100008',['title'])];
        if(empty($saveData['user_id'])) return ['Code' => '100008', 'Msg'=>lang('100008',['user_id'])];
        if(empty($saveData['apiurl'])) return ['Code' => '100008', 'Msg'=>lang('100008',['apiurl'])];
        if(empty($saveData['description'])) return ['Code' => '100008', 'Msg'=>lang('100008',['description'])];
        if(empty($saveData['module_id'])) return ['Code' => '100008', 'Msg'=>lang('100008',['module_id'])];
        if(empty($saveData['project_id'])) return ['Code' => '100008', 'Msg'=>lang('100008',['project_id'])];

        //规避遗漏定义入库数据
        if (empty($saveData)) return ['Code' => '120021', 'Msg'=>lang('120021')];

        //自行处理数据入库条件
        //判断接口地址是否存在
        if ($dbModel->apiurlCheck($saveData['urlmd5'],$id)) return ['Code' => '200001', 'Msg'=>lang('200001')];
        //...
		
        //通过ID判断数据是新增还是更新
    	if ($id <= 0) {

            $saveData['create_time']                = time();
            $saveData['status']                     = 1;

            //执行新增
    		$info 									= $dbModel->addData($saveData);
    	}else{

            //执行更新
    		$info 									= $dbModel->updateById($id,$saveData);
    	}

    	if (!empty($info)) {

            //新增接口时入库默认参数
            if ($id <= 0) $this->addFixedParame($info,$saveData['api_type']);

    		return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info];
    	}else{

    		return ['Code' => '100015', 'Msg'=>lang('100015')];
    	}
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

    	$info 				= $dbModel->getOneById($parame['id']);

    	if (!empty($info)) {
    		
            //格式为数组
            $info                   = $info->toArray();

            //自行对数据格式化输出
            //...

    		return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$info];
    	}else{

    		return ['Code' => '100015', 'Msg'=>lang('100015')];
    	}
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

    	$info 				= $dbModel->updateById($id,[$parame['fieldName']=>$parame['updata']]);

    	if (!empty($info)) {

    		return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['id'=>$id]];
    	}else{

    		return ['Code' => '100015', 'Msg'=>lang('100015')];
    	}
    }

    /**
     * 接口数据删除
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function delData($parame)
    {
        //主表数据库模型
    	$dbModel			= model($this->mainTable);

        //数据ID
        $id                 = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

        $apiInfo            = $dbModel->getOneById($id);
        if (!empty($apiInfo))
        {
            //接口ID
            $apiid              = $apiInfo['id'];

            //分析接口地址格式是否正确
            $apiUrl             = !empty($apiInfo['apiurl']) ? explode('/',trim($apiInfo['apiurl'],'/')) : [];
            if (empty($apiUrl) || count($apiUrl) < 3) return ['Code' => '200002', 'Msg'=>lang('200002')];

            //URL解析
            $mName          = $apiUrl[0];
            $cName          = $apiUrl[1];
            $aName          = $apiUrl[2];
            $apppath        = \Env::get('APP_PATH');

            /*$methodCode     = md5('apiCode'.$apiid);
            $cpath          = $apppath.strtolower($mName).'/controller/'.formatStringToHump($cName).'.php';
            if (file_exists($cpath)){
                $controllerContent      = file_get_contents($cpath);
                $fileContent            = preg_replace("/\/\*api:".$methodCode."\*\/(.*)\/\*api:".$methodCode."\*\//Usi","",$controllerContent);

                file_put_contents($cpath,$fileContent);
            }

            $hpath          = $apppath.strtolower($mName).'/helper/'. formatStringToHump($cName) . '.php';
            if (file_exists($hpath)){
                $controllerContent      = file_get_contents($hpath);
                $fileContent            = preg_replace("/\/\*api:".$methodCode."\*\/(.*)\/\*api:".$methodCode."\*\//Usi","",$controllerContent);

                file_put_contents($hpath,$fileContent);
            }*/

            //先删除原有的参数文件
            $apiCode       = md5(strtolower($mName.formatStringToHump($cName).$aName));
            $paramePath    = $apppath . 'common/parame/' . $apiCode.'.php';
            if (file_exists($paramePath)) unlink($paramePath);

            //执行删除操作
            $delCount               = $dbModel->delData($id);
            
            //删除接口参数
            model('devapi_parame')->delParameByApiId($id);

            return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['count'=>$delCount]];
        }

        //接口不存在
        return ['Code' => '200001', 'Msg'=>lang('200001')];
    }

    /*api:1f6567eddb5f784c4533ff60fd45f866*/
    /**
     * * 功能接口发布接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function apiRelease($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //如果不是本地开发禁止发布
        if (request()->ip() != '127.0.0.1')  return ['Code' => '200004', 'Msg'=>lang('200004')];
        
        //数据ID
        $id                     = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

        //自行书写业务逻辑代码
        $apiInfo                = $dbModel->getOneById($id);

        if (!empty($apiInfo)) {

            $apiInfo            = $apiInfo->toArray();

            //接口ID
            $apiid              = $apiInfo['id'];

            //分析接口地址格式是否正确
            $apiUrl             = !empty($apiInfo['apiurl']) ? explode('/',trim($apiInfo['apiurl'],'/')) : [];
            if (empty($apiUrl) || count($apiUrl) < 3) return ['Code' => '200002', 'Msg'=>lang('200002')];

            $mName          = $apiUrl[0];
            $cName          = $apiUrl[1];
            $aName          = $apiUrl[2];
            $apiCode        = md5(strtolower($mName.formatStringToHump($cName).$aName));

            //生成接口参数文件
            $this->make_parame_file($apiid,$apiCode);

            //Controller文件,文件没有则创建并生成基础代码
            $cpath          = \Env::get('APP_PATH') . strtolower($mName).'/controller/'. formatStringToHump($cName) . '.php';
            if (!file_exists($cpath))  $this->mark_controller_file($cpath,$mName,$cName,$aName);

            //添加Controller文件内接口方法 系统默认不能修改
            if (!in_array($aName, $this->defaultAction)) add_controller_action($cpath,$aName,$apiInfo);
            
            //Helper文件,文件没有则创建并生成基础代码
            $hpath          = \Env::get('APP_PATH') . strtolower($mName).'/helper/'. formatStringToHump($cName) . '.php';
            if (!file_exists($hpath))  $this->mark_helper_file($hpath,$mName,$cName,$aName);

            //添加Helper文件内接口方法 系统默认不能修改
            if (!in_array($aName, $this->defaultAction)) add_helper_action($hpath,$aName,$apiInfo);

            //创建数据模型文件
            $this->mark_model_file();

            //创建语言包文件
            $this->mark_lang_file($cName);

            //入库语言包
            $this->save_lang_file($cName,$id);

            return ['Code' => '200', 'Msg'=>lang('200003')];
        }

        //接口不存在
        return ['Code' => '200001', 'Msg'=>lang('200001')];
    }

    /*api:1f6567eddb5f784c4533ff60fd45f866*/

    /*api:da0c33cedc0ec6f4033357bd8fa36dd6*/
    /**
     * * 获取接口错误码接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function getErrorCode($parame)
    {
        //加载对应的语言包 没有自动创建
        $commonLangFile = \Env::get('APP_PATH') . 'common/lang/zh-cn/common.php';

        $modelLangFile  = '';
        $allFile        = glob ( \Env::get('APP_PATH') . 'common/lang/zh-cn/' . '*' );
        $filename       = strtolower($parame['filename']);

        if (!empty($allFile)) {

            foreach ($allFile as $file) {

                $prg    = '/'.$filename.'.php';

                if (strpos($file,$prg) != false) {

                    $modelLangFile      = $file;
                }
            }
        }

        if (empty($modelLangFile) || !file_exists($commonLangFile) || !file_exists($modelLangFile))
        return ['Code' => '200005', 'Msg'=>lang('200005')];

        $commonLang     = include $commonLangFile;
        $modelLang      = include $modelLangFile;

        $modelLang      = !empty($modelLang) ? $modelLang : [];

        $lang['common']     = !empty($commonLang) ? $commonLang : '暂无';
        $lang[$filename]    = !empty($modelLang) ? $modelLang : '暂无';

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['error_code'=>json_encode($lang)]];
    }

    /*api:da0c33cedc0ec6f4033357bd8fa36dd6*/

    /*api:133fa7a972d6f258444a5a3673722c78*/
    /**
     * * 基础API一键添加
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function addBaseapi($parame)
    {
        //主表数据库模型
        $dbModel                = model($this->mainTable);

        //自行书写业务逻辑代码

        $api_arr      = ['','listData','saveData','detailData','quickEditData','delData'];

        $api_name     = (isset($parame['api_name']) && !empty($parame['api_name'])) ? explode(',', $parame['api_name']) : [];
        if (empty($api_name)) return ['Code' => '200007', 'Msg'=>lang('200007')];

        $api_url      = (isset($parame['api_url']) && !empty($parame['api_url'])) ? explode('/', $parame['api_url']) : [];
        $cname        = count($api_url) === 3 ? ucfirst(lineToHump(humpToLine($api_url[1]))) : '';
        
        if (empty($cname) || $cname === 'Xxx' || strtolower($api_url[0]) != 'api' || !in_array($api_url[2], $api_arr))
        return ['Code' => '200002', 'Msg'=>lang('200005')];

        $api_title    = (isset($parame['api_title']) && !empty($parame['api_title'])) ? $parame['api_title'] : '';
        $module_id    = (isset($parame['module_id']) && !empty($parame['module_id'])) ? $parame['module_id'] : 0;
        $project_id   = 1;

        $baseapi      = [
            'listData'=>['title'=>'数据列表接口'],
            'saveData'=>['title'=>'数据保存接口'],
            'detailData'=>['title'=>'数据详情接口'],
            'quickEditData'=>['title'=>'数据快捷编辑接口'],
            'delData'=>['title'=>'数据删除接口'],
        ];

        $addData                   = [];
        foreach ($api_name as $key => $value)
        {
            if (isset($api_arr[$value]) && !empty($api_arr[$value]))
            {   
                $aname                      = $api_arr[$value];
                $mname                      = strtolower($api_url[0]);
                $apiurl                     = $mname . '/' . $cname . '/' . $aname;
                $api_type                   = $value;

                $saveData                   = [];
                $saveData['id']             = 0;
                $saveData['title']          = $api_title . $baseapi[$aname]['title'];
                $saveData['user_id']        = $parame['uid'];
                $saveData['apiurl']         = $apiurl;
                $saveData['description']    = $api_title . $baseapi[$aname]['title'];
                $saveData['module_id']      = $module_id;
                $saveData['author']         = '';
                $saveData['api_module_type']= 3;
                $saveData['update_time']    = time();
                $saveData['urlmd5']         = md5(strtolower($saveData['apiurl'].$project_id));
                $saveData['create_time']    = time();
                $saveData['status']         = 1;

                if ($dbModel->apiurlCheck($saveData['urlmd5'],0)){
                    continue;
                }else{
                    $data_id        = $dbModel->saveBaseapi($saveData);
                    if ($data_id > 0){
                        $info       = $dbModel->getOneById($data_id);
                        $this->addFixedParame($info,$api_type);
                    }
                }
            }
        }

        //需要返回的数据体
        $Data                   = ['TEST'];

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$Data];
    }

    /*api:133fa7a972d6f258444a5a3673722c78*/

    /*接口扩展*/

    private function addFixedParame($info,$api_type=0)
    {
        $dbModel        = model('devapi_parame');

        //如果是新增接口，默认生成五个固定参数(Code,Msg,Time,ApiUrl,Data)
        $updata         = [];
        $fixedParame    = [
        'Code'=>'状态码',
        'Msg'=>'接口消息说明',
        'Time'=>'接口请求时间',
        'ApiUrl'=>'接口请求地址',
        'Data'=>'接口数据返回实体'
        ];

        $fixedParameDesc  = [
        'Code'=>'接口状态码只有返回000000时才算获取数据成功',
        'Msg'=>'当接口状态码不为000000时，可以通过消息说明扑捉接口返回错误原因',
        'Time'=>'客户端发起接口调用的时间戳',
        'ApiUrl'=>'接口请求地址，方便排除接口调用错误',
        'Data'=>'接口数据返回实体,一般三种格式，空或字符串、详情模式的一维数组，列表形式的多维数组'
        ];

        $api_id         = $info->getAttr('id');
        $user_id        = $info->getAttr('user_id');

        $updata         = [];
        foreach ($fixedParame as $key => $value) {
            $updata[]   = [
                'api_id'=>$api_id,
                'user_id'=>$user_id,
                'parent_id'=>0,
                'tag'=>$key,
                'title'=>$value,
                'ptype'=>($key == 'Data' && $api_type > 0) ? ($api_type == 1 ? 'array' : 'object') : 'string',
                'method'=>2,
                'is_required'=>0,
                'default_value'=>'/',
                'description'=>$fixedParameDesc[$key],
                'add_time'=>time(),
                'sort'=>1,
                'is_synchro'=>2
            ];
        }

        $dbModel->saveAll($updata);


        if ($api_type == 1) {
            //获取Data字段ID
            $DataId         = $dbModel->getDataIdByTag($api_id,'Data');

            $fixedParame    = [
            'total'=>'数据记录总条数',
            'page'=>'当前页数',
            'limit'=>'数据步长',
            'remainder'=>'剩余数据条数',
            'lists'=>'数据列表'
            ];

            $fixedParameDesc  = [
            'total'=>'符合条件下的总数据条数',
            'page'=>'数据查询的当前页码',
            'limit'=>'每页数据条数',
            'remainder'=>'剩余未查询数据条数',
            'lists'=>'查询实体列表数据'
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>$DataId,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>($key == 'lists') ? 'array' : 'number',
                    'method'=>2,
                    'is_required'=>0,
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);

            $ListId         = $dbModel->getDataIdByTag($api_id,'lists');

            $fixedParame    = [
            'id'=>'ID'
            ];

            $fixedParameDesc  = [
            'id'=>'数据ID'
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>$ListId,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>'number',
                    'method'=>2,
                    'is_required'=>0,
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);

            $fixedParame    = [
            'uid'=>'用户身份ID',
            'hashid'=>'用户身份加密串',
            'page'=>'当前页数',
            'search'=>'筛选条件'
            ];

            $fixedParameDesc  = [
            'uid'=>'用户登录成功后获取的唯一ID，注意保存',
            'hashid'=>'用户登录成功后获取的唯一标识,注意保存',
            'page'=>'数据查询页码',
            'search'=>'筛选条件必须是一个一维数组的json数组'
            ];

            $ptype            =[
            'uid'=>'number',
            'hashid'=>'string',
            'page'=>'number',
            'search'=>'json'
            ];

            $is_required      =[
            'uid'=>1,
            'hashid'=>1,
            'page'=>2,
            'search'=>2
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>0,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>$ptype[$key],
                    'method'=>1,
                    'is_required'=>$is_required[$key],
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);

        }elseif ($api_type == 2) {
            
            //获取Data字段ID
            $DataId         = $dbModel->getDataIdByTag($api_id,'Data');

            $fixedParame    = [
            'id'=>'ID'
            ];

            $fixedParameDesc  = [
            'id'=>'数据ID'
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>$DataId,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>'number',
                    'method'=>2,
                    'is_required'=>0,
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);

            $fixedParame  = [
            'uid'=>'用户身份ID',
            'hashid'=>'用户身份加密串',
            'id'=>'数据ID'
            ];

            $fixedParameDesc  = [
            'uid'=>'用户登录成功后获取的唯一ID，注意保存',
            'hashid'=>'用户登录成功后获取的唯一标识,注意保存',
            'id'=>'被操作的数据ID'
            ];

            $ptype            =[
            'uid'=>'number',
            'hashid'=>'string',
            'id'=>'number'
            ];

            $is_required      =[
            'uid'=>1,
            'hashid'=>1,
            'id'=>2
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>0,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>$ptype[$key],
                    'method'=>1,
                    'is_required'=>$is_required[$key],
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);
        }elseif ($api_type == 3){
            
            //获取Data字段ID
            $DataId         = $dbModel->getDataIdByTag($api_id,'Data');

            $fixedParame    = [
            'id'=>'ID'
            ];

            $fixedParameDesc  = [
            'id'=>'数据ID'
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>$DataId,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>'number',
                    'method'=>2,
                    'is_required'=>0,
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);

            $fixedParame  = [
            'uid'=>'用户身份ID',
            'hashid'=>'用户身份加密串',
            'id'=>'数据ID'
            ];

            $fixedParameDesc  = [
            'uid'=>'用户登录成功后获取的唯一ID，注意保存',
            'hashid'=>'用户登录成功后获取的唯一标识,注意保存',
            'id'=>'被操作的数据ID'
            ];

            $ptype            =[
            'uid'=>'number',
            'hashid'=>'string',
            'id'=>'number'
            ];

            $is_required      =[
            'uid'=>1,
            'hashid'=>1,
            'id'=>1
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>0,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>$ptype[$key],
                    'method'=>1,
                    'is_required'=>$is_required[$key],
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);
        }elseif ($api_type == 5) {
            
            //获取Data字段ID
            $DataId         = $dbModel->getDataIdByTag($api_id,'Data');

            $fixedParame    = [
            'count'=>'删除个数'
            ];

            $fixedParameDesc  = [
            'count'=>'成功删除数据个数'
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>$DataId,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>'number',
                    'method'=>2,
                    'is_required'=>0,
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);

            $fixedParame  = [
            'uid'=>'用户身份ID',
            'hashid'=>'用户身份加密串',
            'id'=>'数据ID'
            ];

            $fixedParameDesc  = [
            'uid'=>'用户登录成功后获取的唯一ID，注意保存',
            'hashid'=>'用户登录成功后获取的唯一标识,注意保存',
            'id'=>'被操作的数据ID'
            ];

            $ptype            =[
            'uid'=>'number',
            'hashid'=>'string',
            'id'=>'number'
            ];

            $is_required      =[
            'uid'=>1,
            'hashid'=>1,
            'id'=>1
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>0,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>$ptype[$key],
                    'method'=>1,
                    'is_required'=>$is_required[$key],
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);
        }elseif ($api_type == 4) {
            
            //获取Data字段ID
            $DataId         = $dbModel->getDataIdByTag($api_id,'Data');

            $fixedParame    = [
            'id'=>'ID'
            ];

            $fixedParameDesc  = [
            'id'=>'数据ID'
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>$DataId,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>'number',
                    'method'=>2,
                    'is_required'=>0,
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);

            $fixedParame  = [
            'uid'=>'用户身份ID',
            'hashid'=>'用户身份加密串',
            'id'=>'数据ID',
            'fieldName'=>'字段名称',
            'updata'=>'数据值',
            ];

            $fixedParameDesc  = [
            'uid'=>'用户登录成功后获取的唯一ID，注意保存',
            'hashid'=>'用户登录成功后获取的唯一标识,注意保存',
            'id'=>'被操作的数据ID',
            'fieldName'=>'需要更新的数据表字段名称',
            'updata'=>'需要更新的数据值',
            ];

            $ptype            =[
            'uid'=>'number',
            'hashid'=>'string',
            'id'=>'number',
            'fieldName'=>'string',
            'updata'=>'string',
            ];

            $is_required      =[
            'uid'=>1,
            'hashid'=>1,
            'id'=>1,
            'fieldName'=>1,
            'updata'=>1,
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>0,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>$ptype[$key],
                    'method'=>1,
                    'is_required'=>$is_required[$key],
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);
        }else{
            $fixedParame    = [
                'uid'=>'用户身份ID',
                'hashid'=>'用户身份加密串',
            ];

            $fixedParameDesc  = [
                'uid'=>'用户登录成功后获取的唯一ID，注意保存',
                'hashid'=>'用户登录成功后获取的唯一标识,注意保存',
            ];

            $ptype            =[
                'uid'=>'number',
                'hashid'=>'string',
            ];

            $is_required      =[
                'uid'=>1,
                'hashid'=>1,
            ];

            $updata           = [];
            foreach ($fixedParame as $key => $value) {
                $updata[]     = [
                    'api_id'=>$api_id,
                    'user_id'=>$user_id,
                    'parent_id'=>0,
                    'tag'=>$key,
                    'title'=>$value,
                    'ptype'=>$ptype[$key],
                    'method'=>1,
                    'is_required'=>$is_required[$key],
                    'default_value'=>'/',
                    'description'=>$fixedParameDesc[$key],
                    'add_time'=>time(),
                    'sort'=>1,
                    'is_synchro'=>2
                ];
            }

            $dbModel->saveAll($updata);
        }
    }

    //生成Controller文件
    private function mark_controller_file($cpath,$mName,$cName,$aName)
    {
        //创建并生成控制器文件，写入基础代码
        $basePath       = \Env::get('APP_PATH') . 'common/tpl/ApiTPLC.php';

        //获取基础代码内容
        $baseContent    = file_get_contents($basePath);

        $replace1       = [
        '{ModelNameTPL}',
        '{ControllerNameTPL}',
        ];

        $replace2       = [strtolower($mName),formatStringToHump($cName)];

        $fileContent    = str_replace($replace1,$replace2, $baseContent);

        file_put_contents($cpath,$fileContent);
    }

    //生成Helper文件
    private function mark_helper_file($cpath,$mName,$cName,$aName)
    {
        //创建并生成控制器文件，写入基础代码
        $basePath       = \Env::get('APP_PATH') . 'common/tpl/ApiTPLH.php';

        //获取基础代码内容
        $baseContent    = file_get_contents($basePath);

        $replace1       = [
        '{ModelNameTPL}',
        '{HelperNameTPL}',
        ];

        $replace2       = [strtolower($mName),formatStringToHump($cName)];

        $fileContent    = str_replace($replace1,$replace2, $baseContent);

        file_put_contents($cpath,$fileContent);
    }

    //生成Model文件
    private function mark_model_file()
    {

        $tabList    = db($this->mainTable)->query('SHOW TABLE STATUS');

        $prefix     = config('database.prefix');
        $prefix     = empty($prefix) ? '' : $prefix;

        $modelNameFile      = [];

        if (!empty($tabList))
        {
            foreach ($tabList as $key => $value)
            {
                if (strpos($value['Name'], 'kor_table') === 7) continue;

                //去除表前缀
                $tname      = str_replace($prefix,'',$value['Name']);

                $modelName  = formatStringToHump($tname);

                if (!empty($modelName)) {
                    //检测文件是否存在
                    $file       = \Env::get('APP_PATH') .'common/model/'. $modelName .'.php';
                    $base       = \Env::get('APP_PATH') .'common/tpl/ApiTPLM.php';

                    if (!file_exists($file) && file_exists($base)) {

                        file_put_contents($file,str_replace('{ModelNameTPL}',$modelName,file_get_contents($base)));
                        
                        $modelNameFile[$modelName] = $modelName .'.php';
                    }
                }
            }
        }

        if (!empty($modelNameFile)) {

            ksort($modelNameFile);

            return ['Code' => '200', 'Msg'=>lang('200000001',[count($modelNameFile),implode($modelNameFile,',')])];
        }

        return ['Code' => '200', 'Msg'=>lang('200000002',[0])];
    }

    //生成语言包文件
    private function mark_lang_file($cName = '')
    {
        $demoFile       = \Env::get('APP_PATH') . 'common/lang/zh-cn/demo.php';
        $langFile       = \Env::get('APP_PATH') . 'common/lang/zh-cn/' . strtolower($cName) .'.php';

        if (!empty($cName) && !file_exists($langFile) && file_exists($demoFile)) {

            file_put_contents($langFile,file_get_contents($demoFile));
            return true;
        }
        
        return false;
    }

    //保存语言包文件到数据库
    private function save_lang_file($cName = '',$id=0)
    {
        $code   = $this->getErrorCode(['filename'=>$cName]);
        if ($code['Code'] != '200' || $id <= 0)  return false;

        if (isset($code['Data']['error_code']) && !empty($code['Data']['error_code'])) {
            
            //主表数据库模型
            $dbModel                = model($this->mainTable);

            $dbModel->updateById($id,['api_code'=>$code['Data']['error_code']]);

            return true;
        }
        
        return false;
    }

    //生成接口参数文件
    private function make_parame_file($apiid=0,$apicode='')
    {
        //获取接口参数数据
        $search['api_id']   = $apiid;
        //获取列表数据
        $parame['page']     = 1 ;
        $parame['search']   = !empty($search) ? json_encode($search) : '' ;

        $parameList         = $this->helper($parame,'admin','DevapiParame','listData');

        if ( isset($parameList['Data']['lists']) && !empty($parameList['Data']['lists']))
        {
            //区分接口参数和返回参数
            $request_parame     = [];
            $back_parame        = [];
            $num                = 300000;
            $defaultParame      = ['Code','Msg','Time','ApiUrl','Data'];

            foreach ($parameList['Data']['lists'] as $key => $value)
            {    
                $tag            = $value['tag'];
                $ptype          = $value['ptype'];
                $required       = $value['is_required'] == 1 ? 1 : 0;
                $title          = $value['title'];
                $description    = $value['description'];

                if ($value['method'] == 1) {

                    if ($required == 1) {
                        $num++;
                        $code   = $num;
                    }else{
                        $code   = 0;
                    }

                    $def     = (isset($value['ptype']) && !empty($value['ptype'])) ? $value['ptype'] : '/';
                    
                    $request_parame[]   = [$tag,$ptype,$required,$code,$def,$title,$description];

                }else if ($value['method'] == 2) {

                    $back_parame[]      = [$tag,$ptype,$title,$description,$value['id'],$value['parent_id'],$value['mock']];
                }else{

                    unset($parameList[$key]);
                }
            }

            $apiParame['request_parame']   = $request_parame;
            $apiParame['back_parame']      = $back_parame;
            $apiParame['api_id']           = $apiid;
            $apiParame['api_release']      = time();

            set_release_data($apiParame,$apicode,'api');
        }
    }
}
