<?
/**
 * 专利页面
 *
 * @package Action
 * @author  Martin
 * @since   2015/12/2
 */
class PtAction extends AppAction
{
    
    public $ptype = 10;
    
    //我要买
    public function index()
    {
        //$this->setSeo(4);
        $page   = $this->input('page', 'int', 1);
        $type   = $this->input('t', 'string', '');
        $class  = $this->input('c', 'string', '');
        $keyword  = $this->input('kw', 'string', '');
        $keytype  = $this->input('kt', 'int', 1);//搜索选项:1名称,2专利号

        $_type  = array_filter( array_unique( explode(',', $type) ) );
        $_class = array_filter( array_unique( explode(',', $class) ) );
        //得到当前的url参数字符串的查询参数
        $_whereArr = array();
        $params = array();
        if($type){
            $_whereArr['t'] = $type;
            $params['type'] = count($_type) > 1 ? $_type : current($_type);
        }
        if($class){
            $_whereArr['c'] = $class;
            $params['class'] =implode(',', $_class);
        }
        if($keyword){
            $_whereArr['kw'] = $keyword;
            $params['keyword'] = $keyword;
        }
        $params['keytype'] = $keytype;
        if($keytype==2){
            $_whereArr['kt'] = $keytype;
        }
        $whereStr = http_build_query($_whereArr);
        //查询数据
        $list = $this->load('pt')->getPtList($params, $page, $this->rowNum);
        $ptType     = C('PATENT_TYPE');
        $ptOne      = C('PATENT_ClASS_ONE');
        $ptTwo      = C('PATENT_ClASS_TWO');

        if ( count($_type) == 1 ){
            (current($_type) == 3) ? $this->set('_CLASS', $ptTwo) : $this->set('_CLASS', $ptOne);
        }
        
        list($t_title, $c_title) = $this->getWhereTitle($type, $class);
        $this->set('t_title', $t_title);
        $this->set('c_title', $c_title);

        if ( !empty($list['rows']) ){
            $this->set('has', true);
        }
        
         //设置标题
        $title['name'] 	= $keyword;
        $title['class']	= $t_title;
        $title['temp']	= $c_title;
        
        //设置SEO
        $seoList = $this->getTitle($title);
        $this->set('title', $seoList['title']);
        $this->set('keywords', $seoList['keywords']);
        $this->set('description', $seoList['description']);
        
        //得到推荐专利
        $tj = $this->load('pdetail')->getRandPT();
        $this->set("tj", $tj);
        $this->set('t', $type);
        $this->set('c', $class);
        if($keyword){
            $kw_title = '专利名称:'.$keyword;
            if($keytype==2) $kw_title = '专利号:'.$keyword;
            $this->set('kw_title',$kw_title);
            $this->set('kw',$keyword);
            $this->set('kt',$keytype);
        }
        $this->set('whereStr', $whereStr);//专利类型
        $this->set('list', $list['rows']);//专利类型
        $this->set('total', $list['total']);//专利类型
        $this->set('_TYPE', $ptType);//专利类型
        $this->display();
    }

    protected function getWhereTitle($type, $class)
    {
        $ptType     = C('PATENT_TYPE');
        $ptOne      = C('PATENT_ClASS_ONE');
        $ptTwo      = C('PATENT_ClASS_TWO');

        $_type      = array_filter( array_unique( explode(',', $type) ) );
        $_class     = array_filter( array_unique( explode(',', $class) ) );
        $_className = $_typeName = array();
        if ( count($_type) == 1 ){
            $_ty = current($_type);
            array_push($_typeName, $ptType[$_ty]);
            foreach ($_class as $k => $v) {
                $_ty == 3 ? array_push($_className, $ptTwo[$v]) : array_push($_className, $ptOne[$v]);
            }
        }else{
            foreach ($_type as $k => $v) {
                array_push($_typeName, $ptType[$v]);
            }
        }

        return array(implode(',', $_typeName), implode(',', $_className));
    }


    /**
     * 获取加载数据
     *
     * 处理数据加载时，返回相应数据
     * 
     * @author  Xuni
     * @since   2016-03-08
     *
     * @return  void
     */
    public function getMore()
    {
        $page   = $this->input('_p', 'int', 2);
        $type   = $this->input('t', 'string', '');
        $class  = $this->input('c', 'string', '');
        $keyword  = $this->input('kw', 'string', '');
        $keyword = urldecode($keyword);
        $keytype  = $this->input('kt', 'int', 1);//搜索选项:1名称,2专利号

        $_type  = array_filter( array_unique( explode(',', $type) ) );
        $_class = array_filter( array_unique( explode(',', $class) ) );

        //得到当前的url参数字符串的查询参数
        $params = array();
        if($type){
            $params['type'] = count($_type) > 1 ? $_type : current($_type);
        }
        if($class){
            $params['class'] =implode(',', $_class);
        }
        if($keyword){
            $params['keyword'] = $keyword;
        }
        $params['keytype'] = $keytype;
        $res = $this->load('pt')->getPtList($params, $page, $this->rowNum);
        $this->set('list', $res['rows']);
        $this->set('page', $page);
        $this->display();
    }
    
    //我要卖
    public function sell()
    {
        $this->pageTitle 	= '专利出售,专利转让,卖专利-- 一只蝉专利转让网';

        $this->pageKey          = '专利出售,专利转让,卖专利,专利买卖,买卖专利信息';

        $this->pageDescription  = '卖专利去哪里？一只蝉专利转让网,是超凡集团旗下知产交易平台,多年买卖专利经验。为你提供最专业的买卖专利服务。';
        $this->set('title', $this->pageTitle);//页面title
        $this->set('keywords', $this->pageKey);//页面keywords
        $this->set('description', $this->pageDescription);//页面description
        $this->set('ptype',13);
        $this->setSeo(5);
        $this->display();
    }
    
    /**
     * 设置标题
     * @param $data
     * @return string
     */
    private function getTitle($data)
    {
        if(!empty($data['name'])){
            $name = "专利名称:".$data['name']." ";
        }
        if(!empty($data['class'])){
            $class = $data['class']."类";
        }
        if(!empty($data['temp'])){
            $temp = $data['temp']."专利";
            $temp_title = "_".$data['temp']."专利";
        }
        $title = $name.$class.'专利转让查询'.$temp_title.'_买卖专利_专利转让交易网 一只蝉商标转让平台网';
        $keywords = $class.'专利,'.$class.'专利申请,'.$temp.','.$temp.'申请,购买'.$temp.$class.',专利交易转让网';
         
        $description ='购买'.$class.$temp.',上一只蝉专利查询申请交易网。为广大的专利人提供'.$class.'专利转移全流程服务,包括'.$temp.'技术申请，专利技术转让，专利技术查询以及专利维权服务，一只蝉努力成为国内最大最专业的专利交易平台！';
        return array("title"=>$title,"keywords"=>$keywords,"description"=>$description);
    }
}
?>