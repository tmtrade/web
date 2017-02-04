<?php
/**
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/5/6 0006
 * Time: 下午 13:43
 */
class PatentAction extends AppAction{

    public $ptype       = 11;

    /**
     * 设置标题
     * @param $data
     * @return string
     */
    private function getTitle($data)
    {
        $title = $data['name']."_".$data['class']."类_"."专利_".$data['temp']."专利转让|申请|交易|查询 – 一只蝉商标转让平台网";
        $keywords = $data['name'].'专利转让,'.$data['number'].','.$data['class'].'类专利转让,'.$data['temp'].'专利转让申请交易,专利交易平台网';
         
        $description =$data['name'].'专利,专利号：'.$data['number'].','.$data['class'].'类专利。'.$data['temp'].'专利转让查询申请交易平台。购买'.$data['class'].'类专利上一只蝉专利交易平台第一时间获取专利转让价格信息,一只蝉专利转让交易平台网-独家签订交易损失赔付保障协议专利交易买卖平台';
        return array("title"=>$title,"keywords"=>$keywords,"description"=>$description);
    }

    /**
     * 商标详情
     */
    public function view(){
        //获取参数
        $number = $this->input('short', 'string', '');
        $number = ltrim($number,'-');
        $number = strtolower($number);
        //获得专利基本信息
        $info = $this->load('pdetail')->getPatentInfo($number,false);
        //得到专利包装信息
        $isSale = true;
        if($info){
            $tminfo = $this->load('pdetail')->getSaleTminfo($info['id']);
            if($info['status']!=1){ //下架
                $isSale = false;
            }
        }else{
            //包装信息为空
            $tminfo = array();
            //获得万象云的原始数据
            $info = $this->load('pdetail')->getOrginalInfo($number,2);
        }
        if ( empty($info)){
            $this->redirect('未找到相关专利', '/index/error');
        }
        //设置专利信息相关
        $info['viewPhone'] = empty($info['viewPhone']) ? '18602868321' : $info['viewPhone'];
        $info['thum_title'] = mbSub($info['title'],0,30);
        if($info['type']!=3){ //转换字符为ascii
            $classArr = explode(",", $info['class']);
            $_class = array_map('chr', $classArr);
            $info['class'] = implode(',', $_class);
        }
        //得到相关参数
        $patentType 	= C("PATENT_TYPE");//专利类别
        $patentClassOne	= C("PATENT_ClASS_ONE");//行业分类
        $patentClassTwo	= C("PATENT_ClASS_TWO");//行业分类
        
        //获取行业分类
        $classList = explode(",", $info['class']);
        if($info['type']==1 || $info['type']==2){
            $classArr = $patentClassOne;
        }else{
            $classArr = $patentClassTwo;
        }
        $temp = '';
        foreach($classList as $v){
            $temp .= $classArr[$v]."/";
        }
        $temp = trim($temp,'/');
        
        //设置标题
        $title['name'] 	= $info['title'];
        $title['class']	= $patentType[$info['type']];
        $title['temp']	= $temp;
        $title['number']= $number;
        
        //设置SEO
        $seoList = $this->getTitle($title);
        $this->set('title', $seoList['title']);
        $this->set('keywords', $seoList['keywords']);
        $this->set('description', $seoList['description']);
        //得到用户订单的need字段
        $need = "专利号:".$number;
        //得到推荐专利
        $tj = $this->load('pdetail')->getRandPT();
        
        
        
        //分配数据
        $this->set('patentType', $patentType);
        $this->set('patentClassOne', $patentClassOne);
        $this->set('patentClassTwo', $patentClassTwo);
        $this->set("info", $info);
        $this->set("isSale", $isSale);
        $this->set("tj", $tj);
        $this->set("tminfo", $tminfo);
        $this->set("need", $need);
        $this->set("number", $number);
        $this->set("temp", $temp);
        $this->display();
    }

    
        /**
        * 出售验证专利信息
        * @author	Far
        * @since	2016-05-10
        * @access	public
        * @return	void
        */
       public function getselldata()
       {
                $number	= $this->input("number","string");
                $result = array('status'=>'2');
                if ( empty($number) ) $this->returnAjax($result);

                $info = $this->load('patent')->getPatentInfoByWanxiang($number,2);
                if ( empty($info['id']) ) $this->returnAjax($result);	

               //如果是登录状态，要判断用户是否已出售过
//                if($this->isLogin){
//                        $this->userId   = $this->load('usercenter')->getUserInfo();
//                        $isSale         = $this->load("patent")->existContact($number,$this->userId);
//			if($isSale) $this->returnAjax(array('status'=>'-1'));
//		}
                
                // $patentId = $this->load('patent')->existSale($number);
                //if ( $patentId ) $this->returnAjax(array('status'=>'0'));//在出售中
                $pinfo = $this->load('pdetail')->handleOrginal($info,1);
                $result['status'] 	= "1";
                $result['sbname']	= $pinfo['title'];
                $result['proposer']	= $pinfo['proName'];
                $result['imgurl']	= $pinfo['imgUrl'];

               $this->returnAjax($result);
       }
       
       /**
	 * 出售专利数据处理存储
	 * 
	 * @author	Far
	 * @since	2015-05-11
	 * @access	public
	 * @return	void
	 */
	public function addsell()
	{
		$data = $this->getFormData();
		$num['old'] 	= 0;
		$num['state'] 	= 1;
		$num['num'] 	= 0;
		$num['error'] 	= 0;
		if ( empty($data['number']) || empty($data['phone']) ){
			$num['state'] = -2;
                        $num['msg'] = $data['number'];
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
                        $isOk   = FALSE;
			$isCon = $this->load("patent")->existContact($item, $userId, $phone);
			
			if( $isCon ){
				$num['old'] ++;
				continue;
			}
			$isSale = $this->load("patent")->existSale($item);
                        $code   = (strpos($item, '.') !== false) ? strstr($item, '.', true) : $item;
                        $dataContat['source']       = 10;
                        $dataContat['phone']        = $phone;
                        $dataContat['name']         = $data['contact'];
                        $dataContat['saleType']     = 1;
                        $dataContat['number']       = $item;
                        $dataContat['code']         = $code;
                        $dataContat['price']        = $data['price'][$key];
                        $dataContat['uid']          = $userId;
                        $dataContat['isVerify']     = 2;
                        $dataContat['date']         = time();
			if ( $isSale ){//已存在商品，则添加联系人信息
				$saleBContact = $this->load('patent')->getSaleContactByPhone($item,$phone);
                                //如果没有这个联系人，就写入这个联系人信息
                                if(!$saleBContact){
                                        $dataContat['patentId']     = $isSale;
                                        $isOk = $this->load('patent')->addContact($dataContat,$isSale);
                                }
			}else{//创建商品
                                $info = $this->load('patent')->getPatentInfoByWanxiang($item);
				$isOk = $this->load('patent')->addDefault($item,$info,$dataContat);
			}
			//商标信息
			if( $isOk ){ 
				$num['num'] ++;
                                /**写入分配系统**/
				//$need = "专利号:".$item." 价格：".$data['price'][$key]." 联系人：".$data['contact']."  联系电话：".$phone;
				//$this->load('temp')->pushTrack($need, $data['contact'], $phone, $data['sid'], $data['area'], 2, 1);
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
                        $result['msg'] = '您还未输入专利号，请填写';
                        $this->returnAjax($result);
                }
                $info   =  $this->load('patent')->getPatentInfoByWanxiang($item,2);
                if ( empty($info['id']) ) {
                        $result['msg'] = '无此专利号信息';
                        $this->returnAjax($result);
                }
            }
	}

}