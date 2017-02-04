<?
/**
 * 首页左侧导航着落页
 *
 * 显示群组、商品代号为条件的商标数据
 *
 * @package Action
 * @author  Xuni
 * @since   2016-03-15
 */
class GoodsAction extends AppAction
{
    private $_number    = 30;
    private $_searchArr = array();
    private $_rwfix     = '/g-';
    public $ptype       = 14;
    /**
     * 重定向查询URL
     * 
     * @author  Xuni
     * @since   2016-04-13
     *
     * @return  void
     */
    public function rewriteSearch()
    {
        $short  = $this->input('short', 'string', '');
        $params = $this->strToArr($short, '-');
        $this->index($params);
    }
    
    /**
     * 筛选页功能
     *
     * 处理筛选页筛选功能、不含加载数据
     * 
     * @author  Xuni
     * @since   2016-03-03
     *
     * @return  void
     */
	public function index($params)
	{
        if ( isset($params) ){
            $groupGoods = $params;
        }else{
            $_groupGoods    = $this->input('s', 'string', '');
            $groupGoods     = $this->strToArr($_groupGoods);
            $this->rewriteUrl($groupGoods);
        }

        if ( !empty($groupGoods) ){
            $_params    = implode(',', $groupGoods);
            $result     = $this->load('Goods')->search($_params, $this->_number, 1);
        }else{
            $_params    = '';
            $result     = array('rows'=>array(),'total'=>0);
        }
        //特价列表
        $_arr   = array('isOffprice'=>'1');
        $list   = $this->load('search')->getSaleList($_arr, 1, 8);
        $this->set('offpriceList', $list['rows']);

        $_title = array_merge($result['groupName'], $result['goodsName']);
        $_title = implode(',', $_title);
        if ($result['className']) $_title = $result['className'].','.$_title;

        $classGroup = $this->load('search')->getClassGroup();
        list($_class, $_group) = $classGroup;

        $this->set('_CLASS', $_class);//分类
        $this->set('_GROUP', $_group);//群组
        
        $this->set('baoTitle', $_title);
        $this->set('has', empty($result['rows']) ? false : true);
        $this->set('whereStr', http_build_query(array('s'=>$_params)));
        $this->set('list', $result);
        
        $_browseTitle = $_title ? $_title.$this->pageTitle : '导航页'.$this->pageTitle;
        $this->set('title', $_browseTitle);//页面title
        $this->setSeo(13);
        $this->display('goods/goods.index.html');
	}

    public function getMore()
    {
        $page           = $this->input('_p', 'int', 1);
        $_groupGoods    = urldecode( $this->input('s', 'string', '') );
        $groupGoods     = $this->strToArr($_groupGoods);

        if ( !empty($groupGoods) ){
            $_params    = implode(',', $groupGoods);
            $result     = $this->load('Goods')->search($_params, $this->_number, $page);
        }else{
            $result = array('rows'=>array(),'total'=>0);
        }
        if ( !empty($result['rows']) ){            
            $classGroup = $this->load('search')->getClassGroup();
            list($_class, $_group) = $classGroup;
            $this->set('_CLASS', $_class);//分类
            $this->set('_GROUP', $_group);//群组
        }

        $this->set('list', $result);
        $this->set('page', $page);
        $this->display();
    }

    /**
     * 将字符串转换为数据并去空去重
     * @author  Xuni
     * @since   2016-03-03
     *
     * @access  protected
     * @param   string      $str        字符串
     * @param   string      $prefix     分隔符
     * @return  array
     */
    protected function strToArr($str, $prefix=',')
    {
        if ( empty($str) ) return array();

        $arr = explode($prefix, $str);
        $arr = array_unique( array_filter($arr) );
        return $arr;
    }


    /**
     * 处理搜索项重定向
     *
     * 返回或跳转到相应的重定向地址
     * 
     * @author  Xuni
     * @since   2016-04-13
     *
     * @return  void
     */
    protected function rewriteUrl($params, $type=1)
    {
        $url    = '';
        if ( empty($params) ) {
            $url = substr($this->_rwfix,0,-1).'/';
        }else{ 
            $url = $this->_rwfix.implode('-', $params).'/';
        }
        if ( $type == 2 ) return $url;
        Header("HTTP/1.1 301 Moved Permanently");
        Header("Location: {$url}");
//        $this->redirect('', $url);
        exit;
    }

}
?>