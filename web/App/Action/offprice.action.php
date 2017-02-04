<?
/**
 * 特价商标
 *
 * 特价商标列表
 *
 * @package Action
 * @author  Xuni
 * @since   2016-03-10
 */
class OffpriceAction extends AppAction
{
    public $pageTitle 	= '特价商标,买卖特价商标- 一只蝉商标交易网';
	
    public $pageKey 	= '特价商标交易,特价商标转让,买特价商标,特价商标买卖,买便宜商标，便宜商标转让';
	
    public $pageDescription = '特价商标买卖,特价商标转让交易上一只蝉商标转让交易平台,一只蝉是超凡集团资产交易平台：13年积累约200余万商标转让信息。也是中国独家签订交易损失赔付协议保障风险平台。买特价商标，买便宜商标上一只蝉。一只蝉商标交易网,为用户精心挑选的众多特价商标,方便不同需求用户。提供特价商标交易,特价商标交易。';

    private $_number    = 20;
    public $ptype = 3;

    /**
     * 特价页功能
     *
     * 显示相关特价商品列表
     * 
     * @author  Xuni
     * @since   2016-03-10
     *
     * @return  void
     */
    public function index()
    {
        $res = $this->load('search')->getSaleList($params, $page, $this->_number, $this->_col);
        

        //频道设置
        $channel = $this->load('search')->getChannel($this->mod);
        $this->set('channel', $channel);
        
        $classGroup = $this->load('search')->getClassGroup();
        list($_class, $_group) = $classGroup;
        $this->set('_CLASS', $_class);//分类
        
        //获取下周时间
        $monday_date = date('Y/m/d',strtotime('+1 week last monday'));
        $this->set('monday_date', $monday_date);
        
        $this->setSeo(2);
        $this->display();
    }

    public function checkAjax()
	{
		$number     = $this->input('number', 'string', '');
		$res = $this->load('sale')->click($number);
		$this->returnAjax($res);
	}

}
