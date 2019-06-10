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
 * Date: 2018-02-15
 * Description:开发配置模块
 */

namespace app\manage\controller;

use app\manage\controller\Base;

class Devconfig extends Base
{
    //预定义
    private $apiUrl         = [];
    private $configType     = '';

    public function __construct()
    {
        parent::__construct();

        $this->tpl                      = new \xnrcms\DevTpl();
        $this->apiUrl['config_list']    = 'api/Config/detailData';
        $this->apiUrl['config_save']    = 'api/Config/saveData';
    }

    //开发设置
    public function setDev()
    {
        $this->configType               = 'dev_config';
        //页面头信息设置
        $pageData                       = [];
        $pageData['isback']             = 0;
        $pageData['tpl_title']          = '开发配置表单';
        $pageData['title1']             = '设置';
        $pageData['title2']             = '开发基本设置';
        $pageData['notice']             = ['开发基本设置，配置系统用到的常规配置，请谨慎修改各选项值。',];
        return $this->index($pageData);
    }

    //配置详情
    private function index($pageData = [])
    {
        //数据提交
        if (request()->isPost()) $this->update();

        //参数数据接收
        $param      = request()->param();

        //初始化表单模板 默认当前路由为唯一标识，自己可以自定义标识
        $formNode   = $this->tpl->showFormTpl($this->getTplData($this->configType,$pageData['tpl_title'],'form'),0);
        $formId     = isset($formNode['info']['id']) ? intval($formNode['info']['id']) : 0;
        $formTag    = isset($formNode['tags']) ? $formNode['tags'] : '';
        $formList   = isset($formNode['list']) ? $formNode['list'] : [];

        //数据详情
        $info                           = $this->getDetail(0);

        //记录当前列表页的cookie
        cookie('__forward__',$_SERVER['REQUEST_URI']);
        cookie('__formtag__',$formTag);

        //渲染数据到页面模板上
        $assignData['formId']           = $formId;
        $assignData['formTag']          = $formTag;
        $assignData['formFieldList']    = $formList;
        $assignData['info']             = $info;
        $assignData['defaultData']      = $this->getDefaultParameData();
        $assignData['pageData']         = $pageData;
        $this->assignData($assignData);

        //加载视图模板
        return view('addedit');
    }

    //配置更新
    private function update()
    {
        //表单数据
        $postData                   = request()->param();

        //表单模板
        if(!$this->tpl->checkFormTpl($postData)) $this->error('表单模板数据不存在');

        //表单中不允许提交至接口的参数
        $notAllow                   = ['formId'];

        //过滤不允许字段
        if(!empty($notAllow)){
            foreach ($notAllow as $key => $value) unset($postData[$value]);
        }

        $config                     = !empty($postData) ? json_encode($postData) : '';

        //用户信息
        $postData                   = [];
        $postData['uid']            = $this->uid;
        $postData['hashid']         = $this->hashid;
        $postData['config']         = $config;
        $postData['config_type']    = $this->configType;

        //请求数据
        if (!isset($this->apiUrl['config_save'])||empty($this->apiUrl['config_save'])) 
        $this->error('未设置接口地址');

        $res       = $this->apiData($postData,$this->apiUrl['config_save']) ;
        $data      = $this->getApiData() ;

        if($res){

            $this->success('设置成功',Cookie('__forward__')) ;
        }else{

            $this->error($this->getApiError()) ;
        }
    }

    //获取数据详情
    private function getDetail($id = 0)
    {
        $info           = [];

        //请求参数
        $parame                 = [];
        $parame['uid']          = $this->uid;
        $parame['hashid']       = $this->hashid;
        $parame['config_type']  = $this->configType ;

        //请求数据
        $apiUrl     = (isset($this->apiUrl['config_list']) && !empty($this->apiUrl['config_list'])) ? $this->apiUrl['config_list'] : $this->error('未设置接口地址');
        $res        = $this->apiData($parame,$apiUrl,false);
        $info       = $res ? $this->getApiData() : $this->error($this->getApiError());
        $info       = (isset($info['config']) && !empty($info)) ? json_decode($info['config'],true) : [];

        return $info;
    }
}
?>