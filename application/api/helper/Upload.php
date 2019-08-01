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
use Qiniu\Auth ;
use Qiniu\Storage\UploadManager ;
use Qiniu\Storage\BucketManager ;

class Upload extends Base
{
    private $dataValidate       = null;
    private $mainTable          = 'picture';
    private $imgUploadRoot      = './uploads/picture/' ;
    private $fileUploadRoot     = './uploads/file/' ;
    private $upload_method      = 1;
    private $upload_size        = 1;
    private $upload_itype       = '';
    private $upload_ftype       = '';
    private $upload_manager     = null;
    private $bucket_manager     = null;
    private $oss_key_id         = '' ;
    private $oss_key_secret     = '' ;
    private $oss_token          = '';
    private $oss_endpoint       = '';
    private $upload_error       = [];
	
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
        
        //初始化配置
        $this->init_config();
        
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
		$modelParame['limit']		= isset($parame['limit']) ? $parame['limit'] : 10;

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
        //$saveData['parame']         = isset($parame['parame']) ? $parame['parame'] : '';

        //规避遗漏定义入库数据
        if (empty($saveData)) return ['Code' => '120021', 'Msg'=>lang('120021')];

        //自行处理数据入库条件
        //...
		
        //通过ID判断数据是新增还是更新
    	if ($id <= 0) {

            //执行新增
    		$info 									= $dbModel->addData($saveData);
    	}else{

            //执行更新
    		$info 									= $dbModel->updateById($id,$saveData);
    	}

    	if (!empty($info)) {

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

        //数据ID
        $id                 = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

        //数据详情
    	$info 				= $dbModel->getOneById($id);

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

        //根据ID更新数据
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
    	$dbModel				= model($this->mainTable);

        //数据ID
        $id                 = isset($parame['id']) ? intval($parame['id']) : 0;
        if ($id <= 0) return ['Code' => '120023', 'Msg'=>lang('120023')];

        //自行定义删除条件
        //获取文件信息
        $finfo              = $dbModel->getOneById($id);
        $finfo              = !empty($finfo) ? $finfo->toArray() : [];
        
        if (!empty($finfo)) {
            //根据地址类型相应删除物理文件
            if ($finfo['img_type'] == 1) {

                $path       = '.'.trim($finfo['path'],'.');
                if (file_exists($path)) unlink($path);
            }elseif ($finfo['img_type'] == 2) {
                # code...
            }elseif ($finfo['img_type'] == 3) {
                $this->bucket_manager->delete($this->oss_bucket,$finfo['path']);
            }
        }

        //...
        //执行删除操作
        $delCount               = $dbModel->delData($id);

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>['count'=>$delCount]];
    }

    /*api:f4a1c26f65b071cd7abb7537fc335e0c*/
    /**
     * * H5单图上传
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function uploadImgForH5($parame)
    {
        if(!empty($this->upload_error)) return $this->upload_error;
        
        //主表数据库模型
        $dbModel          = model($this->mainTable);

        //自行书写业务逻辑代码
        //获取有关图片上传的设置
        $config           = ['size'=> $this->upload_size*1024*1024,'ext'=>$this->upload_itype] ;

        //获取表单上传的文件
        $files            = request()->file('fileName') ;
        $re               = [];
       /* wr("=========================1\n");
        wr($files);
        wr("=========================2\n");
        wr("=========================3\n");
        wr($_FILES);
        wr("=========================4\n");*/
        if(empty($files)) return ['Code'=>'203' , 'Msg' => lang('notice_upload_file_empty')] ;

        foreach ($files as $file)
        {
            //上传文件验证
            $ruleName   = 'formatUploadFileName';
            $movePath   = './uploads/picture/';
            $info       = $files->validate($config)->rule($ruleName)->move($movePath);

            if($info === false)
            return ['Code' =>'203','Msg'=>lang('notice_upload_file_fail',[$files->getError()])];

            $path                  = trim($this->imgUploadRoot,'.') . $info->getSaveName();
            $url                   = trim($this->imgUploadRoot,'.') . $info->getSaveName();

            if ($this->upload_method == 2 && !empty($this->upload_manager)) 
            {
                $file_path      = '.'.$path;
                $cfile          = file_get_contents($file_path);
                $res            = $this->upload_manager->putObject($this->oss_bucket, $file_path, $cfile);
            }
            else if ($this->upload_method == 3 && !empty($this->upload_manager))
            {   
                $file_name         = 'admin/'.$info->getSaveName() ;
                $file_path         = '.'.$path;
                $oss_upload_info   = $this->upload_manager->putFile($this->oss_token,$file_name,$file_path);
                $url               = $this->oss_endpoint.'/'.$oss_upload_info[0]['key'];
                $path              = $oss_upload_info[0]['key'];

                if (file_exists($file_path)) unlink($file_path);
            }
            
            $finfo                      = $info->getInfo();
            unset($finfo['tmp_name']);
            unset($finfo['error']);

            $saveData                   = array() ;
            $saveData['path']           = $path;
            $saveData['imgurl']         = $url;
            $saveData['tags']           = isset($parame['tags']) ? $parame['tags'] : '';
            $saveData['img_type']       = $this->upload_method;
            $saveData['infos']          = json_encode($finfo);
            $saveData['create_time']    = time();

            $Picture                    = model($this->mainTable);
            $res                        = $Picture->addData($saveData);
            $re[]                       = $Picture -> getOneById($Picture->id) -> toArray() ;
        }

        $data                           = [];
        
        if (!empty($re))
        {    
            $data['total'] = count($re) ;

            foreach ($re as $index => $item)
            {
                $itype            = (int)$item['img_type'];
                $domain           = request()->domain();
                $url              = $itype == 1 ? $domain . $item['path'] : $item['path'];
                $data['lists'][]  = ['id'=>$item['id'],'path'=>$url];
            }

            return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$data];
        }

        return ['Code' =>'203','Msg'=>lang('notice_upload_file_fail',[''])];
    }

    /*api:f4a1c26f65b071cd7abb7537fc335e0c*/

    /*接口扩展*/

    private function setError($error=[]){
        $this->upload_error     = $error;
    }

    private function init_config()
    {
        $this->setError([]);

        //上传配置
        $config                 = config('upload_config.');
        
        if (empty($config))
        return $this->setError(['Code' => '203', 'Msg'=>lang('notice_upload_oss_empty')]);

        $this->upload_size      = isset($config['file_size']) ? $config['file_size'] : 1;
        $this->upload_itype     = isset($config['upload_imgs_type']) ? $config['upload_imgs_type'] : 'jpg,png';
        $this->upload_ftype     = isset($config['upload_files_type']) ? $config['upload_files_type'] : '.txt';
        $this->upload_method    = isset($config['three_party_type']) ? $config['three_party_type'] : 1;
        $this->oss_key_id       = isset($config['oss_key_id']) ? $config['oss_key_id'] : '';
        $this->oss_key_secret   = isset($config['oss_key_secret']) ? $config['oss_key_secret'] : '';
        $this->oss_endpoint     = isset($config['oss_endpoint']) ? $config['oss_endpoint'] : '';
        $this->oss_bucket       = isset($config['oss_bucket']) ? $config['oss_bucket'] : '';

        if ( in_array($this->upload_method, [2,3]))
        {
            if (empty($this->oss_key_id))
            return $this->setError(['Code' => '203', 'Msg'=>lang('notice_upload_oss_error',['AccessKeyId'])]);

            if (empty($this->oss_key_secret))
            return $this->setError(['Code' => '203', 'Msg'=>lang('notice_upload_oss_error',['AccessKeySecret'])]);
            
            if (empty($this->oss_endpoint))
            return $this->setError(['Code' => '203', 'Msg'=>lang('notice_upload_oss_error',['Endpoint'])]);

            if (empty($this->oss_bucket))
            return $this->setError(['Code' => '203', 'Msg'=>lang('notice_upload_oss_error',['Bucket'])]);

            if ($this->upload_method == 2)
            {
                $this->upload_manager = new \OSS\OssClient($this->oss_key_id, $this->oss_key_secret, $this->oss_endpoint);
            }else if ($this->upload_method == 3) {
                
                $auth                   = new Auth($this->oss_key_id, $this->oss_key_secret);
                // 生成上传Token
                $this->oss_token        = $auth->uploadToken($this->oss_bucket);
                // 构建 UploadManager 对象
                $this->upload_manager   = new UploadManager();
                // 构建 BucketManager 对象
                $this->bucket_manager   = new BucketManager($auth);
            }
        }
    }
}
