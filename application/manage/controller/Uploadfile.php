<?php
/**
 * XNRCMS<562909771@qq.com>
 * ============================================================================
 * 版权所有 2018-2028 小能人科技有限公司，并保留所有权利。
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用TP5助手函数可实现单字母函数M D U等,也可db::name方式,可双向兼容
 * ============================================================================
 * Author: xnrcms<562909771@qq.com>
 * Date: 2018-02-10
 * Description:文件上传功能
 */

namespace app\manage\controller;

use app\manage\controller\Base;

/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class Uploadfile extends Base
{
    private $apiUrl         = [];

    public function __construct()
    {
        parent::__construct();

        $this->apiUrl['index']                 = 'api/Upload/listData';
        $this->apiUrl['delfile']               = 'api/Upload/delData';
        $this->apiUrl['uploaddata']            = 'api/Upload/uploadImgForH5';
    }

    public function index()
    {
        $menuid             = input('menuid',0) ;
        $search             = input('search',[]);
        $page               = input('page',1);
        $tags               = input('tags','xnrcms');

        $search['tags']     = $tags;

        $parame             = [];
        $parame['uid']      = $this->uid;
        $parame['hashid']   = $this->hashid;
        $parame['page']     = $page;
        $parame['search']   = !empty($search) ? json_encode($search) : '' ;

        //请求数据
        if (!isset($this->apiUrl[request()->action()]) || empty($this->apiUrl[request()->action()]))
        $this->error('未设置接口地址');

        $res                = $this->apiData($parame,$this->apiUrl[request()->action()]);
        $data               = $this->getApiData() ;

        if($res){
            $data           = (isset($data['lists']) && !empty($data['lists'])) ? $data['lists'] : [];
            return json(['code'=>1,'msg'=>'获取成功','list'=>$data]);
        }else{
            
            return json(['code'=>0,'msg'=>$this->getApiError()]);
        }
    }

    public function uploadImage()
    {
        //参数数据接收
        $param          = request()->param();

        //解析上传参数
        $uploadParame   = $this->analysisUploadParame(isset($param['uploadParame'])?$param['uploadParame']:'');

        $input          = isset($uploadParame['input']) ? $uploadParame['input'] : '';
        $func           = isset($uploadParame['func']) ? $uploadParame['func'] : '';
        $tags           = isset($uploadParame['tags']) ? $uploadParame['tags'] : 'temp';
        $num            = isset($uploadParame['num']) ? (int)$uploadParame['num'] : 1;
        $config         = isset($uploadParame['config']) ? explode('|', $uploadParame['config']) : [];

        $ext            = isset($config[0]) ? $config[0] : 'jpg,png,gif,jpeg';
        $size           = isset($config[1]) ? $config[1] : '2M';
        $type           = 'fileName';
        $title          = $num <= 1 ? '单个图片' : '多个图片';

        $config         = json_encode(['ext'=>$ext,'size'=>$size,'type'=>$type]);
        $upload         = url('Uploadfile/uploadData',['fileName'=>$type,'tags'=>$tags,'config'=>$config]);
        $fileList       = url('Uploadfile/index',['tags'=>$tags]);
        $delPath        = url('Uploadfile/delFile');

        $info           = [
            'num'       => $num,
            'title'     => $title,
            'upload'    => $upload,
            'fileList'  => $fileList,
            'delPath'   => $delPath,
            'size'      => (intval($size) > 0 ? intval($size) : 2) * pow(1024, 2),
            'ext'       => $ext,
            'type'      => $type,
            'input'     => $input,
            'func'      => $func,
        ];

        $this->assign('info', $info);

        return view('index');
    }

    public function uploadFile()
    {
        //参数数据接收
        $param          = request()->param();

        //解析上传参数
        $uploadParame   = $this->analysisUploadParame(isset($param['uploadParame'])?$param['uploadParame']:'');

        $input          = isset($uploadParame['input']) ? $uploadParame['input'] : '';
        $func           = isset($uploadParame['func']) ? $uploadParame['func'] : '';
        $tags           = isset($uploadParame['tags']) ? $uploadParame['tags'] : 'temp';
        $num            = isset($uploadParame['num']) ? (int)$uploadParame['num'] : 1;
        $config         = isset($uploadParame['config']) ? explode('|', $uploadParame['config']) : [];

        $ext            = isset($config[0]) ? $config[0] : 'txt';
        $size           = isset($config[1]) ? $config[1] : '2M';
        $type           = 'File';
        $title          = '文件';

        $config         = json_encode(['ext'=>$ext,'size'=>$size,'type'=>$type]);
        $upload         = url('Uploadfile/uploadData',['fileName'=>$type,'tags'=>$tags,'config'=>$config]);
        $fileList       = url('Uploadfile/index',['tags'=>$tags]);
        $delPath        = url('Uploadfile/delFile');

        $info           = [
            'num'       => $num,
            'title'     => $title,
            'upload'    => $upload,
            'fileList'  => $fileList,
            'delPath'   => $delPath,
            'size'      => (intval($size) > 0 ? intval($size) : 2) * pow(1024, 2),
            'ext'       => $ext,
            'type'      => $type,
            'input'     => $input,
            'func'      => $func,
        ];

        $this->assign('info', $info);

        return view('index');
    }

    public function uploadData()
    {
        //参数数据接收
        $param          = request()->param();

        //获取列表数据
        $parame             = [];
        $parame['uid']      = $this->uid;
        $parame['hashid']   = $this->hashid;
        $parame['tags']     = isset($param['tags']) ? $param['tags'] : 'tags';

        //请求数据
        if (!isset($this->apiUrl[request()->action()]) || empty($this->apiUrl[request()->action()])) 
        $this->error('未设置接口地址');

        $res                = $this->apiData($parame,$this->apiUrl[request()->action()],false);
        $data               = $this->getApiData() ;

        if($res)
        {
            return json(['code'=>1,'msg'=>'上传成功','data'=>$data]);
        }else{
            
            return json(['code'=>0,'msg'=>$this->getApiError()]);
        }
    }

    public function delFile()
    {
        $ids     = request()->param();
        $ids     = (isset($ids['ids']) && !empty($ids['ids'])) ? $ids['ids'] : $this->error('请选择要操作的数据');;
        $ids     = is_array($ids) ? implode($ids,',') : $ids;

        //请求参数
        $parame['uid']          = $this->uid;
        $parame['hashid']       = $this->hashid;
        $parame['id']           = $ids ;

        //请求地址
        if (!isset($this->apiUrl[request()->action()]) || empty($this->apiUrl[request()->action()]))
        $this->error('未设置接口地址');

        //接口调用
        $res       = $this->apiData($parame,$this->apiUrl[request()->action()]);
        $data      = $this->getApiData() ;

        if($res == true){

            $this->success('删除成功');
        }else{
            
            $this->error($this->getApiError());
        }
    }

    public function preview()
    {    
        // 此页面用来协助 IE6/7 预览图片，因为 IE 6/7 不支持 base64
        $fdir = 'preview';

        if (!file_exists($fdir)) @mkdir($fdir);
        
        $cleanupTargetDir   = true; // Remove old files
        $maxFileAge         = 5 * 3600; // Temp file age in seconds
        
        if ($cleanupTargetDir) {
            if (!is_dir($fdir) || !$dir = opendir($fdir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }
        
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $fdir . DIRECTORY_SEPARATOR . $file;      
                // Remove temp file if it is older than the max age and is not the current file
                if (@filemtime($tmpfilePath) < time() - $maxFileAge) {
                    @unlink($tmpfilePath);
                }
            }

            closedir($dir);
        }
        
        $src            = file_get_contents('php://input');
        if (preg_match("#^data:image/(\w+);base64,(.*)$#", $src, $matches)) {       
            $previewUrl = sprintf(
                "%s://%s%s",
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
                $_SERVER['HTTP_HOST'],$_SERVER['REQUEST_URI']
            );
            
            $previewUrl = str_replace("preview.php", "", $previewUrl);
            $base64 = $matches[2];
            $type = $matches[1];
            if ($type === 'jpeg') {
                $type = 'jpg';
            }
        
            $filename = md5($base64).".$type";
            $filePath = $DIR.DIRECTORY_SEPARATOR.$filename;
        
            if (file_exists($filePath)) {
                die('{"jsonrpc" : "2.0", "result" : "'.$previewUrl.'preview/'.$filename.'", "id" : "id"}');
            } else {
                $data = base64_decode($base64);
                file_put_contents($filePath, $data);
                die('{"jsonrpc" : "2.0", "result" : "'.$previewUrl.'preview/'.$filename.'", "id" : "id"}');
            }
        } else {
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "un recoginized source"}}');
        }
    }

    private function analysisUploadParame($parame = '')
    {
        return (!empty($parame) && is_string($parame)) ? unserialize(string_encryption_decrypt(urlsafe_b64decode($parame),'DECODE')) : [];
    }
}
?>