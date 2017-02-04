<?
/**
 * 交易商标
 *
 * 交易商标
 * 
 * @package	Module
 * @author	Xuni
 * @since	2015-11-05
 */
class InternalModule extends AppModule
{

	/**
     * 引用业务模型
     */
    public $models = array(
        'sale'			=> 'sale',
		'member'		=> 'member',
        'contact'       => 'saleContact',
        'tminfo'        => 'saleTminfo',
    );
	
    /**
     * 获取首页特价商标显示数据，暂时为4条记录
     * 
     * @author  Xuni
     * @since   2015-11-18
     *
     * @access  public
     * @return  array   $list       数据列表
     */
    public function getIndexOffprice()
    {
        $r['raw']   = " indexPic != '' ";
        $r['limit'] = 1000;
        $r['col']   = array('saleId', 'indexPic');
        $res        = $this->import('tminfo')->find($r);
        $items      = arrayColumn($res, 'indexPic', 'saleId');
        $ids        = array_keys($items);
		if(!empty($ids)){
			$role['in']     = array('id'=>$ids);
		}
        $role['limit']  = 4;
        $role['col']    = array('id', 'tid', 'number', 'class', 'name');
        $role['order']  = array('isTop'=>'desc','date'=>'desc');
        $role['raw']    = ' ( isSale = 1 AND `status` = 1 AND priceType = 1 AND isOffprice = 1 AND (`salePriceDate` = 0  OR `salePriceDate` > unix_timestamp(now())) )';
        $list = $this->import('sale')->find($role);
        foreach ($list as $k => $v) {
            $_class = current( explode(',', $v['class']) );
            $list[$k]['imgUrl']     = TRADE_URL.$items[$v['id']];
            $list[$k]['viewUrl']    = '/d-'.$v['tid'].'-'.$_class.'.html';
        }
        return $list;
    }
		
    /**
     * 通过条件查询商标信息--首页使用
     * 
     * @author  Jeany
     * @since   2015-11-09
     *
     * @access  public
     * @param   string  $param      查询条件
     * @param   int     $num        查询数量
     *
     * @return  array   $data       查询数据
     */
	public function getSaleList($param, $num , $page=1)
	{
		if($param){
			foreach($param as $key => $val){
				if($key == 'notId'){
					$r['notIn']    = array('id'=>$val);
				}else{
					$r['ft'][$key] = $val;
				}	
			}
		}
        //可出售商标
		$r['eq']      = array(
            'isSale'    => 1,
            'status'    => 1,
            );
		$r['page']        = $page;
        $r['limit']       = $num;
		$r['col']         = array('name,class,id,number,tid');
		//$r['group']       = array('tid'=>'asc');
        $r['order']       = array('isTop' => 'desc','date' => 'desc');
        $data = $this->import('sale')->findAll($r);
		$data['notId'] = array();
        $classTitle = C('CLASSES');
        foreach($data['rows'] as $k => $v){
            $_class = current( explode(',', $v['class']) );
			$data['rows'][$k]['imgUrl']  = $this->getViewImg($v['id']); 
			$data['rows'][$k]['viewUrl'] = "/d-".$v['tid'].'-'.$_class.".html"; 
			$data['rows'][$k]['_name']   = mbSub($v['name'],0,4);
            $data['rows'][$k]['classes'] = $classTitle[$_class];
			
			array_push($data['notId'], $v['id']);
        }
		return $data;
	}
	
    //获取详情图片
	public function getViewImg($id)
	{	
        if ( $id <= 0 ) return '';

        $r['eq']    = array('saleId'=>$id);
        $r['col']   = array('embellish','number');
        $data       = $this->import("tminfo")->find($r);
		if( empty($data['embellish']) ){
            if ( empty($data['number']) ) return '';
			$url = $this->load('trademark')->getImg($data['number']);
		}else{
            $url = TRADE_URL.$data['embellish'];
        }
        return $url;
	}

    //得到图片和描述
    public function getViewImgAndAlt($id)
    {
        if ( $id <= 0 ) return '';

        $r['eq']    = array('saleId'=>$id);
        $r['col']   = array('embellish','number','alt1');
        $data       = $this->import("tminfo")->find($r);
        if( empty($data['embellish']) ){
            if ( empty($data['number']) ) return '';
            $url = $this->load('trademark')->getImg($data['number']);
        }else{
            $url = TRADE_URL.$data['embellish'];
        }
        $data1['imgUrl'] = $url;
        $data1['alt'] = $data['alt1'];
        return $data1;
    }
    //获取商品信息（可选包含的所有联系人与包装信息）
    public function getSaleInfo($saleId, $contact=1, $tminfo=1 ,$flag = true)
    {
        $r['eq'] = array(
            'id' => $saleId,
            );

        $info = $this->import('sale')->setCache($flag)->find($r);
        if ( empty($info) ) return array();
        if ( $contact ) $info['contact']    = $this->getSaleContact($saleId);
        if ( $tminfo ) $info['tminfo']      = $this->getSaleTminfo($saleId);
        return $info;
    }

    //获取商品ID（saleId）下的所有联系人信息（可选，如果传id即查询当前ID下的联系人信息）
    public function getSaleContact($saleId, $id=0)
    {
        if ( $id <= 0 ){
            $r['eq'] = array(
                'saleId' => $saleId,
                );
            $r['limit'] = 100;
        }else{
            $r['eq'] = array(
                'id'        => $id,
                'saleId'    => $saleId,
                );
            $r['limit'] = 1;
        }

        return $this->import('contact')->setCache(false)->find($r);
    }

    //获取商品ID（saleId）下的商标包装信息
    public function getSaleTminfo($saleId)
    {
        $r['eq'] = array(
            'saleId' => $saleId,
            );
        $data = $this->import('tminfo')->find($r);
        if($data){
            $data['intro'] = preg_replace('/src="/','src="'.TRADE_URL,$data['intro']);//添加上域名
        }
        return $data;
    }


    //判断是否出售基础信息是否存在
    public function existSale($number)
    {
        if ( empty($number) ) return false;
        $r['eq']    = array('number'=>$number);
        $r['col']   = array('id');
        $res = $this->import('sale')->setCache(false)->find($r);
        if ( empty($res) ) return false;
        return $res['id'];
    }

    //判断联系人信息是否存在
    public function existContact($number, $userId, $phone)
    {
        if ( empty($number) ) return false;

        $r['eq'] = array('number'=>$number);
        if ( $userId > 0 ){
            $r['eq']['uid'] = $userId;
        }
        if ( !empty($phone) ){
            $r['eq']['phone'] = $phone;
        }
        $count = $this->import('contact')->setCache(false)->count($r);
        return ($count > 0) ? true : false;
    }

	//打包新增出售（走事务）
    public function addAll($data)
    {
        if ( empty($data) || empty($data['sale']) || empty($data['saleTminfo']) || empty($data['saleContact']) ){
            return false;
        }
        $sale       = $data['sale'];
        $tminfo     = $data['saleTminfo'];
        $contact    = $data['saleContact'];

        $this->begin('sale');
        $saleId = $this->addSale($sale);
        if ( $saleId <= 0 ) {
            $this->rollBack('sale');
            return false;
        }
        $tminfoId   = $this->addTminfo($tminfo, $saleId);//添加包装信息
        $contactId  = $this->addContact($contact, $saleId);//添加联系人
        $black      = $this->load('blacklist')->setBlack($sale['number']);//加入黑名单
        if ( $tminfoId && $contactId && $black ) {
            return $this->commit('sale');
        } 
        $this->rollBack('sale');
        return false;
    }

    //添加出售基础信息
    protected function addSale($data)
    {
        if ( $this->existSale($data['number']) ) return false;

        return $this->import('sale')->create($data);
    }

    //添加商标基础信息
    protected function addTminfo($data, $saleId)
    {
        if ( $this->existTminfo($data['number']) ) return false;

        $data['saleId'] = $saleId;
        return $this->import('tminfo')->create($data);
    }

    //添加出售联系人信息（可多个）
    public function addContact($data, $saleId)
    {
        //判断是否二维数组
        if ( is_array(current($data)) ){
            foreach ($data as $k => $v) {
                $res = $this->addContact($v, $saleId);
                if ( !$res ) return false;
            }
            return $res;
        }        
        $data['saleId'] = $saleId;
        return $this->import('contact')->create($data);
    }

    //判断是否商标包装信息是否存在
    public function existTminfo($number)
    {
        if ( empty($number) ) return false;
        $r['eq'] = array('number'=>$number);
        $res = $this->import('tminfo')->find($r);
        if ( empty($res) ) return false;
        return true;
    }

    //随机获取推荐的商标
    public function getReferrer($class, $limit, $notin)
    {
        if ( empty($class) ) return array();
        $r['ft']    = array('class'=>$class);
        $r['eq']    = array('status'=>1);
        $r['notIn'] = array('number' => array($notin));
        $total      = $this->import('sale')->count($r);
        $total      = $total - count($notin);//计算除notin数据外有多少数据
        if ( $total <= 0 ) return array();
        if ( $total > $limit ){
            $rand       = rand(0, $total-$limit);
            $r['index'] = array($rand, $limit);
        }else{
            $r['limit'] = $limit;
        }
        $res = $this->import('sale')->find($r);
        return $res;
    }

    //获取最新商标
    public function getNewSale($class,$notin="",$limit=5)
    {
        if ( empty($class) ) return array();
        $where['ft']    = array('class'=>$class);
        $where['eq']    = array('status'=>1);
        if(!empty($notin)){
            $where['notIn'] = array('number' => array($notin));
        }
        $where['order'] = array('id' => 'desc');
        $where['limit'] = $limit;
        $res = $this->import('sale')->find($where);
        return $res;
    }
    //判断商品是否上架
    public function isSaleUp($saleId)
    {
        if ( empty($saleId) ) return false;
        $r['eq'] = array('id'=>$saleId,'status'=>1);
        $isUp = $this->import('sale')->setCache(false)->count($r);
        if ( $isUp ) return true;
        return false;
    }
    
}
?>