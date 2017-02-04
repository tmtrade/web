<?
/**
 *
 * 商标打包页
 * @package Action
 * @author  Dower
 * @since   2016-03-08
 */
class PackageAction extends AppAction
{
	public $ptype = 19;

	/**
	 * 商标详情
	 */
	public function index(){
		//得到打包信息
		$id = $this->input('id','int');
		//缓存2个小时的打包数据
		$all = $this->com('redisHtml')->get('pack_detail_'.$id);
		if ( empty($all) ){
			$all = $this->load('pack')->getAll($id);
			if($all){
				$this->com('redisHtml')->set('pack_detail_'.$id, $all, 7200);
			}
		}
		if(!$all){
			$this->redirect('未找到页面', '/index/error');
		}
		//查看是否被收藏
		$isLook = 0;
		$lookList   = $this->load('usercenter')->existLook(array($id),2);
		if($lookList){
			$isLook = 1;
		}
		$this->set('isLook',$isLook);
		//得到推荐商标
		$tj = $this->load('faq')->getTm(3);
		//得到用户订单的need字段
		$need = "打包商标id:".$id.",打包名:{$all['title']}";
		//得到成功案例
		$case = $this->getBasic(2);
		$this->set('case',$case);
		//加载页面
		$this->set("need", $need);
		$this->set('all',$all);
		$this->set('tj',$tj);
		$this->display();
	}

}