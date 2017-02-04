<?
/**
 * 专题信息
 *
 * 专题界面
 *
 * @package	Action
 * @author  far
 * @since   2016-3-08
 */
class ZhuantiAction extends AppAction
{
    public $caches      = array('index','view');
    public $cacheId     = 'redisHtml';
    public $expire      = 3600;//1小时
    
    public $seotime		= '一只蝉商标转让平台网';
    public $ptype       = 9;
    /**
     * 专题中心
     * @author  far
     * @since   2016-3-08
     */
    public function index()
    {
        $page 	= $this->input('page', 'int');
        $res 	= $this->load('zhuanti')->getList(0, $page, $this->rowNum);
        $total 	= empty($res['total']) ? 0 : $res['total'];
		$list 	= empty($res['rows']) ? array() : $res['rows'];

		$pager 		= $this->pager($total, $this->rowNum);
        $pageBar 	= empty($list) ? '' : getPageBarNew($pager);
        //debug($pager);
        $this->set('list', $list);
        $this->set("pageBar",$pageBar);
        $this->set("ptype",8);
        //$this->setSeo(9);
        $this->display();
    }
	
    /**
     * 专题详情
     * @author  far
     * @since   2016-3-08
     */
    public function view() 
	{	
        
		$id 	= $this->input('id', 'int', '0');
        $page 	= $this->input('page', 'int');
		$topic  = $topicItems = array();
		if($id){
			$topic 	= $this->load('zhuanti')->getTopicInfo($id);
			$topicItems = $this->load('zhuanti')->getTopicClassList($id);
		}
		$this->set('topic', $topic);
		$this->set('topicItems', $topicItems);
                
        $res 	= $this->load('zhuanti')->getList($id, $page, 6,array('isMore'=>'asc','sort'=>'desc'));
		$list 	= empty($res['rows']) ? array() : $res['rows'];
        $title  = $topic['title'] . ' - '.$this->seotime;
        $this->set('list', $list);
        $this->set('title', $title);
        $this->setSeo(10,$id);
        $this->display();
	}

}
?>