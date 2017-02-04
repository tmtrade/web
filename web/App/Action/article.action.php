<?
/**
 * 文章控制器
 * @package Action
 * @author  dower
 * @since   2016-11-24
 */
class ArticleAction extends AppAction
{
    public $ptype = 16;
    public $list_num = 20;

    /**
     * 文章列表
     */
    public function lists()
    {
        //解析参数
        $page = 1;
        $c = 0;
        $tag = $this->input('short', 'string', '');
        if ( $tag ){
            if ( strpos($tag, '-') === false ){
                $c = $tag;
            }else{
                list($c, $page) = explode('-', $tag);
            }
        }
        $this->oldRedirect($c);//旧链接重定向
        $this->getLeftData();//得到左菜单数据
        $page = $page?$page:1;
        //得到列表数据
        //缓存一天的列表数据
        $article_list = $this->com('redisHtml')->get('article_list_'.$c.'_'.$page);
        if(empty($article_list)){
            $article_list = $this->load('wordpress')->getList($c,$page,$this->list_num);
            if($article_list['rows']) $this->com('redisHtml')->set('article_list_'.$c.'_'.$page, $article_list, 86400);
        }
        //判断数据有效
        if(empty($article_list['rows'])){
            Header("Location: /index/error");
            exit;
        }
        //解析文章列表相关信息
        $list = $article_list['rows'];
        $total = $article_list['total'];
        $seo = $article_list['seo'];

        //得到分页信息
        $pageTool = new myPage(array('suffix'=>'/'));
        $pager = $pageTool->get($total, $this->list_num);
        $pageBar = empty($list)?'':getPageBar($pager);
        //设置seo信息
        $this->set("title", $seo['title']);
        $this->set("keywords", $seo['keywords']);
        $this->set("description", $seo['desc']);

        $this->getLunmu($c,0);
        $this->set("nav", $c);
        $this->set("category", $this->category[$c]);
        $this->set("list", $list);
        $this->set("pageBar", $pageBar);
        $this->display('article/article.lists.html');
    }

    /**
     * 得到文章信息
     */
    public function view()
    {
        //处理参
        $tag = $this->input('short', 'string', '');
        if ( $tag ){
            if ( strpos($tag, '-') === false ) {
                Header("Location: /index/error");
                exit;
            }
            list($c, $id) = explode('-', $tag);
        }else{
            Header("Location: /index/error");
            exit;
        }
        $this->oldRedirect($c);//旧链接重定向
        $this->getLeftData();//得到左菜单数据
        //得到文章信息
        //缓存一天的文章数据
        $article_detail = $this->com('redisHtml')->get('article_detail'.$c.'_'.$id);
        if(empty($article_detail)){
            $article_detail = $this->load('wordpress')->getArticle($id,$c);
            if($article_detail) $this->com('redisHtml')->set('article_detail'.$c.'_'.$id, $article_detail, 86400);
        }
        $data = $article_detail;
        if(empty($data)){
            Header("Location: /index/error");
            exit;
        }
        //设置seo信息
        $seo = $data[3];
        $this->set("title", $seo['title']);
        $this->set("keywords", $seo['keywords']);
        $this->set("description", $seo['desc']);

        $this->set("list", $data[1]);
        $this->set('data', $data);//文章信息
        $this->set("id", $id);
        $this->set("ptype", 17);
        $this->getLunmu(-1,$id);
        $this->display();
    }

    /**
     * 得到左菜单的数据
     */
    public function getLeftData(){
        $faq_left = $this->com('redisHtml')->get('faq_left');
        if(empty($faq_left)){
            //得到新闻及faq
            $faq_left['news'] = $this->load('wordpress')->getList(0,1,5,false);
            $faq_left['faq'] = $this->load('wordpress')->getList(2,1,5,false);
            if($faq_left['news'] && $faq_left['faq']) $this->com('redisHtml')->set('faq_left', $faq_left, 86400);
        }
        //得到推荐商标
        $tj = $this->load('wordpress')->getTm(3);
        $this->set('news',$faq_left['news']);
        $this->set('faq',$faq_left['faq']);
        $this->set('tj',$tj);
    }

    /**
     * 荣誉资质
     */
    public function ryzz()
    {
        $this->getLeftData();//得到左菜单数据
        $pageTitle   = '荣誉资质-一只蝉商标转让平台网';
        $this->set("title", $pageTitle);
        $this->set("keywords", '一只蝉荣誉资质,荣誉资质');
        $this->set("description", '一只蝉荣誉资质,荣誉资质。一只蝉是超凡集团资产交易平台：13年积累约200余万商标转让信息-也是中国独家签订交易损失赔付协议保障风险平台');
        $this->getLunmu(-1,-1);
        $this->set('id',-1);
        $this->display();
    }

    /**
     * 营业执照
     */
    public function yyzz()
    {
        $this->getLeftData();//得到左菜单数据
        $pageTitle   = '营业执照-一只蝉商标转让平台网';
        $this->set("title", $pageTitle);
        $this->set("keywords", '一只蝉营业执照,营业执照');
        $this->set("description", '一只蝉营业执照,营业执照。一只蝉是超凡集团资产交易平台：13年积累约200余万商标转让信息-也是中国独家签订交易损失赔付协议保障风险平台');
        $this->display();
    }

    /**
     * 得到状态栏
     * @param int $c
     * @param int $id
     * @return int
     */
    public function getLunmu($c=-1,$id=0){
        if($c==-1){
            switch($id){
                case -1:
                    $type = 0;break;
                case 28872:
                    $type = 0;break;
                case 28874:
                    $type = 1;break;
                case 28878:
                    $type = 1;break;
                case 28880:
                    $type = 1;break;
                default:
                    $type = 0;break;
            }
        }else{
            if($c==2){
                $type = 1;
            }else{
                $type = 2;
            }
        }
        $this->set('lanmu',$type);
    }

    /**
     * 重定向
     * @param $c
     * @return bool
     */
    public function oldRedirect($c){
        $now = array(0,1,2,3);
        if(in_array($c,$now)){
            return true;
        }
        $old = array(45,50,51,52,53);
        $reflect = array(
            50=>0,//新闻
            51=>1,//法律
            45=>2,//问答
            52=>3,//求购信息
            53=>1,//百科,现无,跳到法律
        );
        if(in_array($c,$old)){
            $vv = $reflect[$c];
        }else{
            $vv = 0;//默认新闻
        }
        //重定向
        Header("Location: /n-{$vv}/");
        exit;
    }

}
?>