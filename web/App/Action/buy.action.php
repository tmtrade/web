<?
/**
 * 买商标，求购
 *
 * 求购商标信息添加
 *
 * @package	Action
 * @author	Xuni
 * @since	2015-11-13
 */
class BuyAction extends AppAction
{
        public $fkcookie        = 'fkcookie';
        public $pageTitle 	= '买商标,商标交易,购买商标,哪里可以购买商标,买卖商标哪里好_一只蝉商标交易转让网';
        public $pageKey 	= '买商标,购买商标,商标交易,商标买卖,哪里可以购买商标，买卖商标公司,买卖商标哪里好';
        public $pageDescription = '一只蝉商标交易网是国内领先的商标交易平台，最权威的商标转让机构。买商标为你快速入驻天猫,京东等电商平台。买卖商标哪里好？一只蝉13年专业经验,为买卖商标创造一个快速安全专业的商标交易平台。';
        public $ptype = 12;
	 
    public function index()
    {
		$fkcookie = $this->fkcookie;

		if(Session::get($fkcookie)){
			$fkcookieNum = Session::get($fkcookie);	
		}
		if($fkcookieNum){
			if($fkcookieNum == 1){
				$urlHtml = "zhuanti/zhuanti.peifu.html";
			}else{
				$urlHtml = "zhuanti/zhuanti.safeguard.html";
			}
		}else{
			$num = rand(1,2);
			Session::set($fkcookie, $num, 100000);
			if($num == 1){
				$urlHtml = "zhuanti/zhuanti.peifu.html";
			}else{
				$urlHtml = "zhuanti/zhuanti.safeguard.html";
			}
		}
                $this->setSeo(6);
		$this->display($urlHtml);
    }
	
	
	public function buyold()
    {	
		$urlHtml = "buy/buy.index.html";
		$this->display($urlHtml);
    }

    /**
     * 添加临时求购信息
     *
     * @author  Xuni
     * @since   2015-11-25
     *
     * @access  public
     *
     * @return  json
     */
    public function addTemp()
    {
        if ( !$this->isAjax() ) $this->redirect('', '/');

        $mobile     = $this->input('mobile', 'string', '');
        $content    = $this->input('content', 'string', '');
        $name       = $this->input('name', 'string', '');
        $sid        = $this->input('sid', 'string', '');
        $area       = $this->input('area', 'string', '');

        if ( $this->isLogin ){
            $this->returnAjax(array('code'=>2)); //已登录不添加临时
        }
        $userinfo = $this->load('passport')->get($mobile,2);
        if ( $userinfo ) $this->returnAjax(array('code'=>2)); //已注册不添加临时
        $user = $this->load('temp')->isExist($mobile);
        if ( $user ) $this->returnAjax(array('code'=>2)); //已临时注册不添加临时
        $role = array(
            'eq' => array(
                'mobile'    => $mobile,
                'need'      => $content,
                //'userId'    => empty($userinfo)?'0':$userinfo['id'],
                ),
            );
        $temp = $this->load('buy')->findTemp($role);
        if ( $temp ){
            $this->returnAjax(array('code'=>1));//未添加
        }
        $buy = array(
            'mobile'    => $mobile,
            'need'      => $content,
            'name'      => $name,
            'sid'       => $sid,
            'area'      => $area,
            //'userId'    => empty($userinfo)?'0':$userinfo['id'],
            'ip'        => getClientIp(),
            'date'      => time(),
            );
        $res = $this->load('buy')->addTempBuy($buy);
        if ( $res ) {
            $this->returnAjax(array('code'=>1));//成功
        }
        $this->returnAjax(array('code'=>0)); //未成功
    }

}
?>