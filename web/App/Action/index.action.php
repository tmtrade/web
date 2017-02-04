<?
/**
 * 网站首页
 *
 * 网站首页
 *
 * @package Action
 * @author  dower
 * @since   2016-3-03
 */
class IndexAction extends AppAction
{
	public $caches  	= array('index');
	public $cacheId 	= 'redisHtml';
	public $expire  	= 3600;//1小时
    public $ptype = 1;
	protected $onlineName = 'YzCoNlInE';

	public function index()
	{
	    $this->redirect('', '/s/');
		//首页标识
		$this->set('is_index',true);
		//得到首页基本基本配置信息
		$configs = $this->getBasic();
		$this->set('banners',$configs[0]);
		$this->set('ads',$configs[1]);
		$this->set('recommendClasses',$configs[2]);
		$this->set('case',$configs[3]);
		//得到模块信息
		$modules = $this->com('redisHtml')->get('index_modules');
		if(empty($modules)){
			$modules = $this->load('index')->getModule();
			$this->com('redisHtml')->set('index_modules', $modules,86400);
		}
		$this->set('modules',$modules);
		//最新交易记录
//		$_tradeInfo = $this->com('redisHtml')->get('_tradeInfo_tmp');
//		if ( empty($_tradeInfo) ){
//			$tradeInfo = $this->load('buy')->getNewsTradeInfo(10);
//			$this->com('redisHtml')->set('_tradeInfo_tmp', $tradeInfo, 3600);
//		}else{
//			$tradeInfo = $_tradeInfo;
//		}
//		$this->set('tradeInfo',$tradeInfo);

		//最新求购需求
		$last_require = $this->com('redisHtml')->get('_last_require');
		if ( empty($last_require) ){
			$last_require = $this->load('require')->getRequire(10);
			$this->com('redisHtml')->set('_last_require', $last_require, 3600);
		}
		$this->set('last_require',$last_require);
		//得到商标分类信息
		$allClass = $this->load('index')->getAllClass();
		$this->set('allClass',$allClass);
		//得到新闻和问答
		$_news = $this->com('redisHtml')->get('_news_tmp');
		if(empty($_news)){
			$news['news']	= $this->load('wordpress')->getList(0,1,5,false);
			$news['faq']	= $this->load('wordpress')->getList(2,1,5,false);
			$this->com('redisHtml')->set('_news_tmp', $news, 86400);
		}else{
			$news = $_news;
		}
		$this->set('news',$news);
		//得到faq其他信息
		$faq_other = $this->com('redisHtml')->get('faq_other');
		if(empty($faq_other)){
			$faq_other = $this->load('wordpress')->getOther(6);
			if($faq_other) $this->com('redisHtml')->set('faq_other', $faq_other, 86400);
		}
		$this->set('faq_other',$faq_other);
		$this->setSeo(1);
		$this->display();
	}

	public function searchLog()
	{
		$title 	= $this->input('title','string','');

		$prefix = C('SEARCH_HISTORY');
		$log 	= (array)unserialize( Session::get($prefix) );
		//$list 	= array_filter( array_map('unserialize', $log) );
		$list 	= array_slice($log, 0, 10);
		
		$this->set('list',$list);
		$this->display();
	}
        
    //跳转到移动端
    public function jumpWap() {
    	$arr             = explode('.', $_SERVER['HTTP_HOST']);
		$length          = count($arr);
		$domain          = '.'.$arr[$length-2].'.'.$arr[$length-1];
		$domain          = preg_replace("/:\d+/", '', $domain);
        setcookie("jumpwap", 1, 0, '/', $domain);

        $this->redirect('', WAP_URL);
    }
        
	/**
	 * 发送邮件
	 */
	public function sendEmail(){
		//准备数据
		$contact = $this->input('contact','string','');
		$content = $this->input('content','string','无');
		$content .= '<br/>'.'<font color="red">联系方式:</font> '.$contact;
		$email = C('FEEDBACKER');
		$title = '一只蝉反馈意见';
		$name = '';
		$from = '一只蝉';
		//发送邮件
		$res = $this->load('index')->sendEmail($email, $title, $content, $name , $from);
		return $this->returnAjax($res);
	}


	public function getOnlineStatus()
	{
		$online = $this->com('redisQc')->get($this->onlineName);
		$this->returnAjax(array('code'=>1,'msg'=>$online));
	}
	
}
?>