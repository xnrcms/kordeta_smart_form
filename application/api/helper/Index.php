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

class Index extends Base
{
	private $dataValidate 		= null;
    private $mainTable          = 'null';
	
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

    /*api:2dcf1a0ebcd7e6de04c2685efbb72b05*/
    /**
     * * 首页综合接口
     * @param  [array] $parame 接口参数
     * @return [array]         接口输出数据
     */
    private function index($parame)
    {
        //主表数据库模型
        $adData                = [];
        $catData               = [];
        $search                = [];
        $search['pos_id']      = 1;

        $adParame              = [];
        $adParame['limit']     = 5;
        $adParame['search']    = json_encode($search);

        $adList                = $this->helper($adParame,'Api','Ad','listData');
        if ($adList['Code'] === '200' && isset($adList['Data']['lists']) && !empty($adList['Data']['lists']))
        {
            
            $adData     = $adList['Data']['lists'];

            foreach($adData as $k=>$v){
                $adData[$k]['icon'] = get_cover($v['imgid'],'path');
            }
        }

        $noticeParame              = [];
        $noticeParame['limit']     = 5;
        $noticeParame['page']      = 1;

        $noticeList                = $this->helper($adParame,'Api','Article','notice');
        if ($noticeList['Code'] === '200' && isset($noticeList['Data']['lists']) && !empty($noticeList['Data']['lists'])){
            $notice     = $noticeList['Data']['lists'];
            if (!empty($notice) && isset($notice[0])) {
                $notice[0]['title']     = strip_tags($notice[0]['content']);
            }

            foreach ($notice as $key => $value) {
                if ($key >= 1)  unset($notice[$key]);
            }

            $noticeData     = $notice;
        }

        $catParame              = [];
        $catParame['limit']     = 100;
        $catParame['ctype']     = 1;
        $catParame['isstatus']  = 1;

        $catList                = $this->helper($catParame,'Admin','Category','listData');
        if ($catList['Code'] === '200' && isset($catList['Data']['lists']) && !empty($catList['Data']['lists']))
        {
            
            $catData            = $catList['Data']['lists'];
            $lotteryConfig      = config('lottery.');
            $catDatas           = [];

            foreach($catData as $k=>$v){
                $v['icon']    = get_cover($v['icon'],'path');
                $v['optime']  = 0;
                $v['times']   = 0;
                if ($v['pid'] > 0) {
                    $lottery_id             = $v['id'];
                    $lottery                = new \app\api\lottery\Lottery($lottery_id);
                    $v['times']             = $lottery->getLotteryTime();

                    //获取表名
                    $lottery_table      = '';
                    if (isset($lotteryConfig['lottery_tag'][$lottery_id])) {
                        $lottery_table  = $lotteryConfig['lottery_tag'][$lottery_id];
                    }else{
                        unset($catData[$k]); continue;
                    }

                    $map     = [];
                    $map[]   = ['lotterid','=',$lottery_id];
                    $optime  = model($lottery_table)->where($map)->limit(1)->order('id desc')->value('opentimestamp');
                    $v['optime']  = !empty($optime) ? $optime : 0;
                    /*if ($lottery_id == 100) {
                        $v['optime']  = time() + 86400*1;
                    }*/
                    
                    $catDatas[]     = $v;
                }
            }
        }

        $newData['id']          = 1;
        $newData['title']       = '热门彩种';
        $newData['ctype']       = 1;
        $newData['pid']         = 0;
        $newData['sort']        = 1;
        $newData['status']      = 1;
        $newData['icon']        = '';
        $newData['isrecommend'] = 1;
        $newData['update_time'] = 1526270846;
        $newData['create_time'] = 1526270846;
        $newData['description'] = '';
        $newData['optime']      = 0;
        $newData['times']       = 0;
        $newData['_child']      = $catDatas;
        

       /* $Tree                  = new \xnrcms\DataTree($catData);
        $catData               = $Tree->arrayTree();*/

        //自行书写业务逻辑代码

        //需要返回的数据体
        $Data                   = [];
        $Data['notice_list']    = $noticeData;
        $Data['ad_list']        = $adData;
        $Data['cat_list']       = [$newData];

        return ['Code' => '200', 'Msg'=>lang('text_req_success'),'Data'=>$Data];
    }

    /*api:2dcf1a0ebcd7e6de04c2685efbb72b05*/

    /*接口扩展*/
}
