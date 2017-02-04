<?php 
class BuyModule extends AppModule
{

    public $source = array();
    public $deal   = array();
	public $buytype= array();

    /**
	* 引用业务模型
	*/
	public $models = array(
		'buy'			=> 'tbuy',
        'temp'          => 'ttempbuy',
		'sale'			=> 'tsale',
		'saletrademark' => 'tsaletrademark',
		'tmclass'            => 'tmclass',
		'tm'		=> 'trademark',
	);

    /**
     * 基础获取求购信息的方法
     *
     * @author  Xuni
     * @since   2015-11-14
     *
     * @access  public
     * @param   array     $r     条件
     *
     * @return  array
     */
    public function find($r)
    {
        return $this->import('buy')->find($r);
    }

    /**
     * 基础获取临时求购信息的方法
     *
     * @author  Xuni
     * @since   2015-11-25
     *
     * @access  public
     * @param   array     $r     条件
     *
     * @return  array
     */
    public function findTemp($r)
    {
        return $this->import('temp')->find($r);
    }
	
    /**
     * 创建数据
     * @author  Xuni
     * @since   2015-11-25
     *
     * @access  public
     * @param   array   $data   数据
     * @return  void
     */
    public function addTempBuy($data)
    {
        return $this->import("temp")->create($data);
    }

    /**
     * 删除数据
     * @author  Xuni
     * @since   2015-12-8
     *
     * @access  public
     * @param   array     $r     条件
     * @return  void
     */
    public function removeTemp($r)
    {
        return $this->import('temp')->remove($r);
    }

    //判断用户是否购买过
    public function isBuy($name, $userId)
    {
        $r['eq'] = array(
            'name'          => $name,
            'loginUserId'   => $userId,
        );
        $r['raw'] = ' status != 4 ';
        $count = $this->import("buy")->count($r);

        return ($count > 0) ? true : false;
    }

	/**
     * 通过saleId获取数据
     * @author	Jeany
     * @since	2015-07-27
     *
     * @access	public
     * @param	int 	$saleId	    出售编号
     * @return	array
     */	 
    public function getDataBySale( $name ,$class ,$userId)
    {
		$r['limit']         = 1;
		$r['eq']['name']  = $name;
		$r['eq']['class'] = $class;
		$r['eq']['loginUserId']  = $userId;
        $data = $this->import("buy")->find($r);
        return $data;
    }
	
	/**
     * 判断求购信息是否存在
	 * 
	 * @author	martin
	 * @since	2015/11/24
	 *
	 * @access	public
     * @param	array	$data	数组
	 * @return	array
	 */
	public function isExist( $data )
	{
		foreach($data as $key => $val){
			$r['eq'][$key] = $val;
		}
        $r['limit'] = 1;
        $r['raw']   = ' status != 4 ';
        $data       = $this->import('buy')->find($r);
		return $data;
	}

	/**
     * 通过saleId和联系电话获取数据
     * @author	Jeany
     * @since	2015-11-26
     *
     * @access	public
     * @param	int 	$saleId	    出售编号
     * @return	array
     */	 
    public function getDataByContact( $saleId ,$phone)
    {
		$r['limit']         = 1;
		$r['eq']['saleId']  = $saleId;
		$r['eq']['phone']  = $phone;
        $data = $this->import("buy")->find($r);
        return $data;
    }

    /**
     * 创建数据
     * @author	martin
     * @since	2015-07-23
     *
     * @access	public
     * @param	array	$data	数据
     * @return	void
     */
    public function create($data)
    {
        return $this->import("buy")->create($data);
    }
	
	/**
     * 最新购买需求
     * @author	Jeany
     * @since	2015-11-13
     *
     * @access	public
     * @param	array 	$param	   
     * @return	void
     */	
	public function getNewsBuy($num)
	{
        $r['limit']         = $num;
        $r['order']         = array('dealDate' => 'desc', 'id'=>'desc');
		$r['col']           = array("contact","need","phone");
		$r['in']            = array('status' => array("2","3","6") );
		$r['raw']           = " phone != '' and need != ''";
        $data               = $this->import('buy')->find($r);
        foreach($data as $key => $item){
			$star = "****";
			$data[$key]['phone']  = substr(trim($item['phone']),0,3).$star.substr(trim($item['phone']),7);
			$data[$key]['contact'] = trim($item['contact']) ? substr(trim($item['contact']),0,3)."**" : "佚名" ;
			$data[$key]['shortneed']    = mbSub($item['need'],0,12); 
        }
		return $data;
	}
	
	/**
     * 最新交易记录
     * @author	Jeany
     * @since	2015-11-13
     *
     * @access	public
     * @return	void
     */	
	public function getNewsTrade($num)
	{
		$r['limit']         = $num;
        $r['order']         = array('dealDate' => 'desc', 'id'=>'desc');
		$r['in']            = array('status' => array("2","3") );
		$r['col']           = array("contact","name","class","dealDate","phone");
		$r['raw']           = " class > 0";
        $data               = $this->import('buy')->find($r);
		$star = "****";
		foreach($data as $key => $item){
			$data[$key]['contact'] = trim($item['contact']) ? substr(trim($item['contact']),0,3)."**" : "佚名" ;
			$data[$key]['phone']  = substr(trim($item['phone']),0,3).$star.substr(trim($item['phone']),7);
			$data[$key]['name']    = mbSub($item['name'],0,12); 
			$data[$key]['dealDate']    = $this->tradeTime($item['dealDate']);
			$data[$key]['className']    = $this->import('tmclass')->get($item['class'],'name');
        }
		return $data;
	}
	
	//交易时间换算
	public function tradeTime($time)
	{
		$nowTime = time();
		$times = ($nowTime-$time)/60;
		if($times <= 10){
			$strtime = "1分钟之前";
		}elseif($times > 10 && $times <= 30){
			$strtime = "10分钟之前";
		}elseif($times > 30 && $times <= 60){
			$strtime = "30分钟之前";
		}elseif($times > 60){
			$strtime = "1小时之前";
		}
		return $strtime;
	}

	/**
	 * 获得指定数量的最新交易信息----接口
	 * @param $number
	 * @return mixed
	 */
	public function getNewsTradeInfo($number){
		//调用接口,获得数据
		$query['id'] 		= array();
		$query['pttype']	= '';
		$query['notnull']	=  1;//去掉为空的条件
		$query['startdate'] = '';
		$query['enddate'] 	= '';
		//$query['state'] 	= 4;//状态[1：洽谈中 2：已匹配 3：已成交 4：已立案 5：交易关
		$rst = tradeInfo::getNetwork($query,1,$number);
		$rst = json_decode($rst)->data;
		if(!$rst){
			return array();
		}
		//处理数据
		$data = array();
		foreach($rst as $item){
			//得到内容
			$flag = true;//出售数据是否处理标识
			$remarks = empty($item->remarks)?($item->subject):($item->remarks);
			if(($item->pttype)=='出售'){
				//处理出售数据
				//截取出商标号
				$reg = '/商标号:(\w+)/';
				$aaa = preg_match($reg,$remarks,$result);
				if($aaa){
					//得到商标名和分类
					$tm = $result[1];
					$r['eq'] = array('id'=>$tm);
					$r['col'] = array('trademark','class');
					$res = $this->import('tm')->find($r);
					if($res){
						$flag = false;//成功处理
						$remarks = $res['trademark'].' '.$res['class'].'类';
					}
				}
			}
			if($flag){
				//未处理数据
				//截取掉敏感数据
				$result = strstr($remarks,'价格',true);
				$remarks = $result?$result:$remarks;

				$result = strstr($remarks,'联系人',true);
				$remarks = $result?$result:$remarks;

				$result = strstr($remarks,'我的联系电话',true);
				$remarks = $result?$result:$remarks;

				$result = strstr($remarks,'联系电话',true);
				$remarks = $result?$result:$remarks;

				$result = strstr($remarks,'电话号码',true);
				$remarks = $result?$result:$remarks;
			}
			$remarks = mbSub($remarks,0,30);
			//组装结果数据
			$data[] = array(
				'remarks'=>$remarks,
				'pttype'=>$item->pttype,
				'name'=>trim($item->name) ? substr(trim($item->name),0,3)."**" : "佚名",
				'tel'=>substr(trim($item->tel),0,3)."****".substr(trim($item->tel),7),
			);
		}
		return $data;
	}
}
?>
