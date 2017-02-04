<?
/**
 * 成功案列
 *
 * @package Action
 * @author  Far
 * @since   2016-03-21
 */
class CaseAction extends AppAction
{
    public $caches      = array('index','detail');
    public $cacheId     = 'redisHtml';
    public $expire      = 3600;//1小时

    public $pageTitle 	= '商标转让成功案例-- 一只蝉商标转让平台网';

    public $pageKey 	= '商标转让,商标转让网,注册商标转让,转让商标,商标买卖,商标交易,商标交易网';

    public $pageDescription = '一只蝉商标转让成功案例。一只蝉是超凡集团资产交易平台：13年积累约200余万商标转让信息-也是中国独家签订交易损失赔付协议保障风险平台-商标转让-专利转让';
	//得到栏目对应的文章列表
	public function index()
	{
            $page			= $this->input("page","int");
            $page			= $page == 0 ? 1 : $page;
            $limit			= 16;
            $list			= $this->load('case')->getCaseList('', $page, $limit);
            $pager			= $this->pagerNew($list['total'], $limit);
            $pageBar		= empty($list) ? '' : getPageBarNew($pager);

            $this->set("list", $list['rows']);
            $this->set("keywords", $this->keyword);
            $this->set("description", $this->description);
            $this->set("pageBar", $pageBar);
            $this->set("page", $page);
            $this->setSeo(11);
            $this->set("ptype", 5);
            $this->display();
	}

	//得到对应的文章详情
	public function detail()
	{
            $id     = $this->input("id","int");
            $data	= $this->load('case')->getCaseInfo($id);
            $list	= $this->load('case')->getCaseList($id, 1, 4, array("id"=>"desc"));
            $title  = $data['title'].' - '.$this->seotime;
            $this->set("caseInfo", $data);
            $this->set("list", $list['rows']);
            $this->set("title", $title);
            $this->set("id", $id);
            $this->setSeo(12,$id);
            $this->set("ptype", 6);
            $this->display();
	}

}
?>