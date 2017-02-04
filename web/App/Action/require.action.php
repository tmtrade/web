<?
/**
 * 需求大厅
 *
 * @package Action
 * @author  Dower
 * @since   2016-12-06
 */
class RequireAction extends AppAction
{
	public $ptype = 20;
	private $size = 6;

	/**
	 * 列表页
	 */
	public function index(){
		//获得数据
		$page = $this->input('page','int',1);
		$keyword = $this->input('keyword','string');
		$param = array(
			'page'=>$page,
			'keyword'=>$keyword,
			'limit'=>$this->size,
		);
		//得到分页数据
		$res = $this->load('require')->getList($param);
		$pager			= $this->pagerNew($res['total'], $param['limit']);
		$pageBar		= empty($res['rows']) ? '' : getPageBarNew($pager);
		//设置seo

		//渲染页面
		$this->set("list", $res['rows']);
		$this->set("count", $res['total']);
		$this->set("keyword", $keyword);
		$this->set("pageBar", $pageBar);
		$this->set("page", $page);
		$this->display();
	}

	/**
	 * 详情页
	 */
	public function detail(){
		$id = $this->input('id','int');
		//得到需求详情
		$res = $this->load('require')->getView($id);
		//得到上一条与下一条
		$rst = $this->load('require')->getPrevAndNext($id);
		//设置SEO

		//渲染页面
		$this->set('prev',$rst['prev']);
		$this->set('next',$rst['next']);
		$this->set('require',$res);
		$this->set("ptype", 21);
		$this->display();
	}

	/**
	 * 添加需求竞标信息
	 */
	public function addRequireBid(){
		//获取参数
		$require_id = $this->input('require_id','int');
		$number = $this->input('number','array');
		$price = $this->input('price','array');
		$name = $this->input('name','string');
		$mobile = $this->input('mobile','string');
		//判断数量
		if(count($number)>10){
			$this->returnAjax(array('code'=>1,'msg'=>'一次提交商标数不能超过10个'));
		}
		$data = array(
			'require_id'=>$require_id,
			'number'=>$number,
			'price'=>$price,
			'name'=>$name,
			'mobile'=>$mobile,
		);
		//保存数据
		$res = $this->load('require')->addRequireBid($data);
		if($res){
			$this->returnAjax(array('code'=>0));
		}else{
			$this->returnAjax(array('code'=>1,'msg'=>'保存信息失败, 请稍后再试'));
		}
	}

	/**
	 * 检测商标的有效性
	 */
	public function checkTms(){
		//得到商标号
		$number = $this->input('number','string');
		$number = array_filter(array_unique(explode(' ',$number)));
		if(count($number)>10){
			$this->returnAjax(array('code'=>1,'msg'=>'一次提交商标数不能超过10个'));
		}
		//检测商标状态
		$code = 1;//默认失败--成功一个就认为成功
		$code1 = 0;//是否有失败商标
		$msg = '';
		$data = array();
		foreach($number as $item){
			$res = $this->checkTm($item);
			if($res['code']!=0){
				$code1 = 1;
				$msg .= (' 错误商标:'.$item.'-'.$res['msg']."<br/>");
			}else{
				$code = 0;
				$data[] = $res['data'];
			}
		}
		//返回结果
		$this->returnAjax(array('code'=>$code,'code1'=>$code1,'msg'=>$msg,'data'=>$data));
	}

	/**
	 * 检测商标是否能出售
	 * @param $number string 商标号
	 * @return array
	 */
	private function checkTm($number){
		if ( empty($number) ) return array('code'=>1,'msg'=>'商标号不能为空');
		$info   = $this->load('trademark')->getTmInfo($number);
		if ( empty($info) ) return array('code'=>2,'msg'=>'商标不存在');
		//不能出售的商标
		$status = array('已无效','冻结中');
		foreach ($status as $s) {
			if( in_array($s, $info['second']) ){
				return array('code'=>3,'msg'=>strip_tags($s).'的商标不能出售');
			}
		}
		//返回商标信息
		return array('code'=>0,'data'=>array('name'=>$info['name'],'number'=>$number,'img'=>$info['imgUrl']));
	}
}
?>