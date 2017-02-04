<?
/**
 * 商标购买界面
 *
 * 商标购买界面
 *
 * @package Action
 * @author  Jeany
 * @since   2015-11-14
 */
class SellAction extends AppAction
{
	public $ptype = 13;
	public function index()
	{
		$number	= $this->input("m","string");
		$this->set("number",$number);
                $this->setSeo(7);
		$this->display();
	}

	/**
	 * 获取商标信息
	 * 
	 * @author	JEANY
	 * @since	2015-09-16
	 *
	 * @access	public
	 * @return	void
	 */
	public function getselldata()
	{
		$number	= $this->input("number","string");
		$result = array('status'=>'2');
		if ( empty($number) ) $this->returnAjax($result);

		$info   = $this->load('trademark')->getTmInfo($number);	
		if ( empty($info) ) $this->returnAjax($result);	
		
		//如果是登录状态，要判断用户是否已出售过
//		if($this->isLogin){
//            $this->userId   = $this->load('usercenter')->getUserInfo();
//            $isSale         = $this->load("internal")->existContact($number,$this->userId);
//			if($isSale) $this->returnAjax(array('status'=>'-1'));
//		}

		//不能出售的商标
		$status = array('已无效','冻结中');
		foreach ($status as $s) {
			if( in_array($s, $info['second']) ){
				$result['status'] 		= "0";
				$result['statusValue'] 	= strip_tags($s);
				$this->returnAjax($result);
			}
		}

		$result['status'] 	= "1";
		$result['sbname']	= $info['name'];
		$result['proposer']	= $info['proName'];
		$result['imgurl']	= $info['imgUrl'];

		$this->returnAjax($result);
	}
	
	/**
	 * 出售商标数据处理存储
	 * 
	 * @author	JEANY
	 * @since	2015-11-12
	 *
	 * @access	public
	 * @return	void
	 */
	public function addsell()
	{
		$data = $this->getFormData();	
		$url = '/trademark/sell/';
		$num['old'] 	= 0;
		$num['state'] 	= 1;
		$num['num'] 	= 0;
		$num['error'] 	= 0;
		if ( empty($data['number']) || empty($data['phone']) ){
			$num['state'] = -2;
			$this->returnAjax($num);
		}
		//检查传过来的数据
		$this->checkselldata($data);

		//判断成功后进行处理
		$phone 	= $data['phone'];
		$sale 	= array();
		$userId = 0;
		if ( $this->isLogin ){
                    $userId = $this->load('usercenter')->getUserInfo();
		}

		foreach($data['number'] as $key => $item){
			$item 	= trim($item);
			$isCon = $this->load("internal")->existContact($item, $userId, $phone);
			
			if( $isCon ){
				$num['old'] ++;
				continue;
			}
			$isSale = $this->load("internal")->existSale($item);
			if ( $isSale ){//已存在商品，则添加联系人信息
				$isOk = $this->_addContact($isSale, $item, $data, $key, $userId);
			}else{//创建商品
				$isOk = $this->_addSell($item, $data, $key, $userId);
			}
			//商标信息
			if( $isOk ){ 
				$num['num'] ++;
				/**写入分配系统**/
				//$need = "商标号:".$item." 价格：".$data['price'][$key]." 联系人：".$data['contact']."  联系电话：".$phone;
				//$this->load('temp')->pushTrack($need, $data['contact'], $phone, $data['sid'], $data['area'], 2);
				/**写入分配系统 END**/
			}else{
				$num['error'] ++;
			}
		}
		$num['all'] = $num['num'] + $num['old'] + $num['error'];
		echo json_encode($num);
	}
	
	/**
	 * 检验出售数据的添加
	 * 
	 * @author	JEANY
	 * @since	2015-11-12
	 *
	 * @access	public
	 * @return	void
	 */
	public function checkselldata($data)
	{
		$result = array(
			'state' => '-2',
		);		
		if ( empty($data['phone']) ){
			$result['msg'] = '您还未输入电话，请填写';
			$this->returnAjax($result);
		}elseif(!preg_match("/^1[3,4,5,7,8]{1}[0-9]{9}$/",$data['phone'])){
			$result['msg'] = '请输入正确手机号！';
			$this->returnAjax($result);
		}
		foreach($data['number'] as $key => $item){
			$item 	= trim($item);
	        if ( $data['price'][$key] <= 0 ) {
	        	$num['msg'] = '请输入正确价格！';
				$this->returnAjax($num);
	        }
			if ( empty($item) ){
				$result['msg'] = '您还未输入商标号，请填写';
				$this->returnAjax($result);
			}elseif(!preg_match('/^[0-9a-zA-Z]*$/',$item)){
				$result['msg'] = '请输入正确商标号！';
				$this->returnAjax($result);
			}
			$info   = $this->load('trademark')->getTmInfo($item);
	        if ( empty($info) ) {
	        	$result['msg'] = '无此商标号信息';
				$this->returnAjax($result);
	        }
		}
	}
		
	/**
	 * 出售数据数据库添加
	 * 
	 * @author	JEANY
	 * @since	2015-11-12
	 *
	 * @access	public
	 * @return	void
	 */
	protected function _addSell($number, $data, $key, $userId)
	{
		//商标信息
		$info   = $this->load('trademark')->getTmInfo($number);
        if ( empty($info) ) return false;
        //商标其他信息
        $other  = $this->load('trademark')->getTmOther($number);
        if ( empty($other) ) return false;

		$class      = implode(',', $info['class']);
		$memo 		= count($info['class']) > 1 ? '(该商标为一标多类，必须捆绑出售)' : '';
        $platform   = implode(',', $other['platform']);
        $viewPhone  = $this->load('phone')->getRandPhone();
        $regDate    = strtotime($info['reg_date']) > 0 ? strtotime($info['reg_date']) : 0;
        $endDate    = strtotime($info['valid_end']) > 0 ? strtotime($info['valid_end']) : 0;
        $sale = array(
            'tid'           => intval($info['tid']),
            'number'        => $number,
            'class'         => $class,
            'group'         => trim($info['group']),
            'name'          => trim($info['name']),
            'pid'           => intval($info['pid']),
            'price'         => 0,
            'priceType'     => 2,//议价
            'isOffprice'    => 2,
            'salePrice'     => 0,
            'salePriceDate' => 0,
            'status'        => 3, //待审核
            'isSale'        => 1,//出售
            'isLicense'     => 2,
            'isTop'         => 0,
            'type'          => $other['type'],
            'platform'      => $platform,
            'label'         => '',
            'length'        => $other['length'],
            'regDate'       => $regDate,
            'endDate'       => $endDate,
            'date'          => time(),
            'hits'          => 0,
            'viewPhone' 	=> $viewPhone,
            'memo'          => '一只蝉前台创建商品'.$memo,
        );
        $tminfo = array(
            'number'    => $number,
            'memo'      => '一只蝉前台创建默认包装信息',
            'intro'     => '',
        );
		$contact = array(
            'source'        => 10,
            'uid'           => intval($userId),
            'tid'           => intval($info['tid']),
            'number'        => $number,
            'name'          => trim($data['contact']),
            'phone'         => trim($data['phone']),
            'price'         => $data['price'][$key],
            'saleType'      => 1,
            'isVerify'      => 2,
            'advisor'       => '',
            'department'    => '',
            'date'          => time(),
            'memo'      	=> '一只蝉前台创建联系人',
		);
		$_data = array(
			'sale' 			=> $sale,
			'saleTminfo'	=> $tminfo,
			'saleContact'	=> $contact,
			);
		$saleId = $this->load('internal')->addAll($_data);
		return $saleId;
	}

	//添加联系人
	protected function _addContact($saleId, $number, $data, $key, $userId)
	{
		//商标信息
		$info = $this->load('trademark')->getTmInfo($number);
        if ( empty($info) || empty($info['tid']) ) return false;

		$contact = array(
            'source'        => 10,
            'uid'           => intval($userId),
            'tid'           => intval($info['tid']),
            'number'        => $number,
            'name'          => trim($data['contact']),
            'phone'         => trim($data['phone']),
            'price'         => $data['price'][$key],
            'saleType'      => 1,
            'isVerify'      => 2,
            'advisor'       => '',
            'department'    => '',
            'date'          => time(),
            'memo'      	=> '一只蝉前台添加联系人',
		);
		$res = $this->load('internal')->addContact($contact, $saleId);
		return $res;
	}
}
?>