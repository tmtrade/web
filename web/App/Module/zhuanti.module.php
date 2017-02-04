<?
/**
 * 无
 *
 * 无
 * 
 * @package	Module
 * @author	Xuni
 * @since	2016-3-08
 */
class ZhuantiModule extends AppModule
{
    public $models = array(
        'topic'		    => 'topic',
        'topicitems'    => 'topicitems',
    );
    
    //专题列表
    public function getList($id=0, $page, $limit=20,$order=array('sort'=>'desc'))
    {
        $r = array();
        if($id>0){
             $r['notIn']  = array('id' => array($id));
        }
        $r['eq'] = array('isUse'=>1);
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['order'] = $order;
        $res = $this->import('topic')->findAll($r);
        return $res;
    }
    
    //模块基础内容
    public function getTopicInfo($moduleId)
    {
        $r = array();
        $r['eq']['id']  = $moduleId;
        $r['limit'] = 1;
        $res = $this->import('topic')->find($r);
        return $res;
    }
    
     //根据静态链接获取单条信息ID
    public function getTopicInfoByLink($link)
    {
        $r = array();
        $r['eq']['link']  = $link;
        $r['limit'] = 1;
        $r['col'] = array('id');
        $res = $this->import('topic')->find($r);
        return $res['id'];
    }
	
    //首页模块子分类列表信息
    public function getTopicClassList($topicId)
    {
        $r = array();
        $arr = array();
        $r['eq']['topicId'] = $topicId;
        $r['limit'] = 100;
        $r['order'] = array('sort'=>'desc');
        $data = $this->import('topicitems')->findAll($r);
        $_numbers = arrayColumn($data['rows'], 'number');
        $list = $this->load('goods')->getListInfo($_numbers);
        $list = $this->load('search')->getListTips($list);
        return $list;
    }

    /**
     * 根据商标获得其参加的专题信息
     * @param $number
     * @return array|bool
     */
    public function getTopicByNumber($number){
        $r['eq']['number'] = $number;
        $r['col'] = array('topicId');
        $r['limit'] = 6;
        $rst = $this->import('topicitems')->find($r);
        $data = array();
        if($rst){
            $r = array();
            foreach($rst as $item){
                $r['eq']['id'] = $item['topicId'];
                $r['eq']['isUse'] = 1;
                $r['col'] = array('title','id');
                $res = $this->import('topic')->find($r);
                if($res){
                    $res['topicUrl'] = '/zhuanti/view/?id='.$res['id'];
                    $res['thumb_title'] = mb_substr($res['title'],0,4,'utf-8');
                    $data[] = $res;
                }
            }
            return $data;
        }else{
            return false;
        }
    }

    /**
     * 获取搜索列表单类搜索时的专题数据
     * 
     * @author  Xuni
     * @since   2016-04-06
     *
     * @return  void
     */
    public function getSearchTopicByClass($class, $limit=4)
    {
        if ( !in_array($class, range(1,45)) ) return array();

        $r['eq']    = array('isUse'=>1);
        $r['raw']   = " `listPic` != '' ";
        $r['limit'] = intval($limit);
        $r['order'] = array('sort'=>'desc');
        $r['col']   = array('id','listPic', 'alt4');
        $r['ft']    = array('listClass'=>$class);
        $res = $this->import('topic')->find($r);
        if ( empty($res) ) return array();
        foreach ($res as $k => $v) {
            $res[$k]['alt']         = $v['alt4'];
            $res[$k]['listPic']     = TRADE_URL.$v['listPic'];
            $res[$k]['topicUrl']    = "/zhuanti/view/?id=".$v['id'];
        }
        return $res;
    }
}
?>