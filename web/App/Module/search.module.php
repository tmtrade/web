<?
/**
 * 快速筛选功能组件
 *
 * 快速筛选出商标信息，主要从出售信息列表中搜索数据，次要从商标库里搜索数据
 * 
 * @package Module
 * @author  Xuni
 * @since   2015-11-09
 */
class SearchModule extends AppModule
{
    private $_service = array();

    /**
     * 引用业务模型
     */
    public $models = array(
        'sale'          => 'sale',
        'second'        => 'secondStatus',
        'group'         => 'group',
        'class'         => 'tmClass',
        'tminfo'        => 'saleTminfo',
        'channel'       => 'channel',
        'channelItems'  => 'channelItems',
    );
    
    //获取查询的TITLE
    public function getSeo($all)
    {
        foreach ($all as $k => $value) {
                if (empty($value) && count($all)==1) return;
                $_arr = array_filter( explode(',', $value) );
                switch ($k) {
                    case 'kw':
                        $S      = C('SBSEARCH');
                        $kt     = intval($all['kt']) <= 0 ? 1 : intval($all['kt']);
                        if(!empty($value)) $kname   = $S[$kt].':'.$value.' ';
                        break;
                    case 'c':
                        list($cArr,) = $this->load('search')->getClassGroup(0, 0);
                        foreach ($_arr as $v) {
                            if(count($all)>=2){
                                $c_str .= $cArr[$v].' '.$v.'类 ';
                                $c_name_str .= $cArr[$v].' ';
                                $c_id_str = '第'.$v.'类'.$cArr[$v].' ';
                                $c_name = $cArr[$v];
                                $c_id = $v;
                            }else{
                                $c_name = $cArr[$v];
                                $c_id = $v;
                            }
                        }
                        break;
                    case 'g':
                        if ( empty($all['c']) ) return $value;
                        list(,$gArr) = $this->load('search')->getClassGroup(0, 1);
                        foreach ($_arr as $v) {
                            if(count($all)>=3){
                                $g_str .= mb_substr($gArr[$all['c']][$v],0,7,'utf-8').' '.$v.' ';
                                $g_name_str .= $gArr[$all['c']][$v].' ';
                                $g_name = $gArr[$all['c']][$v];
                                $g_id = $v;
                            }else{
                                $g_name = $gArr[$all['c']][$v];
                                $g_id = $v;
                            }
                        }
                        if(count($_arr)==1){
                            $g_count =1;
                        }
                        break;
                    case 't':
                        $T = C('TYPES');/*组合类型*/
                        foreach ($_arr as $v) {
                            $t_str .= $T[$v].' ';
                        }
                        break;
                    case 'p':
                        $P = C('PLATFORM_IN'); /*平台入驻*/
                        foreach ($_arr as $v) {
                            $p_str .= $P[$v].' ';
                        }
                        break;
                    case 'sn':
                        $N      = C('SBNUMBER');/*商标字数*/
                        $_hasN  = false;
                        foreach ($_arr as $v) {
                            if ( $v == '1' || $v == '2' ){
                                $_hasN = true;
                            }else{
                                $sn_str .= $N[$v].' ';
                            }
                        }
                        if ( $_hasN ) $sn_str = $N['1,2'].' '.$sn_str;
                        break;
                    case 'l':
                        $l = C('LABEL'); /*特价类型*/
                        foreach ($_arr as $v) {
                            $l_str .= $l[$v].' ';
                        }
                        break;
                }
            }
            if(count($all)<=3 && !empty($c_name) && empty($g_name) && count($_arr)==1){
                $title = '第'.$c_id.'类'.$c_name.' 商标转让交易买卖价格|一只蝉商标转让平台网';
                $description = '第'.$c_id.'类商标转让价格要多少钱？了解'.$c_name.'商标转让价格到一只蝉'.$c_name.'商标交易平台第一时间获取'.$c_id.'类商标交易价格动态变化；一只蝉商标转让平台-独家签订交易损失赔付保障协议商标交易平台';
            }
            else if(count($all)<=3 && !empty($c_name) && !empty($g_name) && $g_count==1){
                $title = $g_id.' '.$g_name.'_第'.$c_id.'类-商标转让交易买卖价格|一只蝉商标转让平台网';
                $description = $g_name.'商标转让价格要多少钱？了解'.$g_name.'商标转让价格到一只蝉'.$c_name.'商标交易平台第一时间获取'.$g_id.'类商标交易价格动态变化；一只蝉商标转让平台-独家签订交易损失赔付保障协议商标交易平台';
            }else{
                $title = $kname.$l_str.$p_str.$g_str.$c_str.$sn_str.$t_str.'商标转让交易买卖价格|一只蝉商标转让平台网';
                $description =$kname.$l_str.$p_str.$g_name_str.'商标转让价格要多少钱？了解'.$c_name_str.$sn_str.$t_str.'商标转让价格到一只蝉'.$c_id_str.'商标交易平台第一时间获取'.$g_name_str.'商标交易价格动态变化；一只蝉商标转让平台-独家签订交易损失赔付保障协议商标买卖平台';
            }
            return array("title"=>$title,"description"=>$description);
    }

    /**
     * 快速筛选结果
     * 
     * @author  Xuni
     * @since   2015-11-09
     *
     * @access  public
     * @param   int     $class      国际分类(1-45)
     *
     * @return  array   $list       群组号对应群组中文名称的数组
     */
    public function search($params, $page=1, $limit=30, $type=1)
    {
        if ( $type == 1 ){
            return $this->getSaleList($params, $page, $limit);
        }elseif ( $type == 2 ){
            //$list = $this->getTmList($params, $page, $limit);
            //return array('rows'=>$list);

            //2016-07-25关闭其他数据
            return array('rows'=>array());
        }

        $result = array(
            'rows'  => array(),
            'total' => 0,
            );
        return $result;
    }

    /**
     * 获取适用服务筛选条件
     * 
     * @author  Xuni
     * @since   2016-03-04
     *
     * @access  public
     * @param   string      $keyword        关键字
     *
     * @return  array       $list           类别与群组，多类别时，只返回类别
     */
    public function getServiceCondition($keyword)
    {
        if ( empty($keyword) ) return array();

        $name   = $this->_getService($keyword, 1);
        $title  = $this->_getService($keyword, 2);
        $label  = $this->_getService($keyword, 3);

        return $this->_service;
    }

    /**
     * 获取适用服务筛选条件
     * 
     * @author  Xuni
     * @since   2016-03-04
     *
     * @access  public
     * @param   string      $keyword        关键字
     *
     * @return  array       $list           类别与群组，多类别时，只返回类别
     */
    private function _getService($str, $type)
    {
        switch ($type) {
            case '1':
                $r['like'] = array('name'=> $str);
                break;            
            case '2':
                $r['like'] = array('title'=> $str);
                break;
            case '3':
                $r['ft'] = array('label'=> $str);
                break;
        }
        if ( empty($r) ) return array();

        $r['limit'] = 1000;
        $res        = $this->import('class')->find($r);
        if ( empty($res) ) return array();

        $class = $group = array();
        foreach ($res as $k => $v) {
            if ( $v['parent'] == 0 ){
                array_push($class, $v['number']);
            }else{
                array_push($class, $v['parent']);
                array_push($group, $v['number']);
            }
        }
        $class = array_unique($class);
        $group = array_unique($group);

        if ( !empty($class) ){
            $this->_service['class'] = empty($this->_service['class']) ? $class : array_merge($this->_service['class'], $class);
            $this->_service['class'] = array_filter( array_unique($this->_service['class']) );
        }
        if ( !empty($group) ){
            $this->_service['group'] = empty($this->_service['group']) ? $group : array_merge($this->_service['group'], $group);
            $this->_service['group'] = array_filter( array_unique($this->_service['group']) );
        }
    }

    /**
     * 获取出售信息相关数据
     * 
     * @author  Xuni
     * @since   2016-03-04
     *
     * @access  public
     * @param   int     $class      国际分类(1-45)
     *
     * @return  array   $list       群组号对应群组中文名称的数组
     */
    public function getSaleList($params, $page=1, $limit=30, $col=array())
    {
        $result = array(
            'rows'  => array(),
            'total' => 0,
            );
        $r['raw'] = ' 1 ';
        if ( !empty($params['name']) ){
            switch ( $params['_name'] ) {
                case '1'://相同
                    $r['eq']['name'] = $params['name'];
                    break;
                case '2'://前包含
                    $r['lLike']['name'] = $params['name'];
                    break;
                case '9'://近似
                    $_arr = array('keyword'=>$params['name']);
                    if ( !empty($params['class']) ){
                        $_arr['classId'] = $params['class'];
                        unset($params['class']);
                        if ( !empty($params['group']) ){
                            $_arr['groupCode'] = $params['group'];
                            unset($params['group']);
                        }
                    }
                    $_res = $this->searchLike($_arr, 1, 1000);
                    if ( empty($_res['rows']) ) return $result;
                    $numberList = array_unique( arrayColumn($_res['rows'], 'code') );
                    if ( empty($numberList) ) return $result;
                    $r['in']['number'] = $numberList;
                    break;
                default:
                    $r['eq']['name'] = $params['name'];
                    break;
            }
        }
        //注册日期
        if ( !empty($params['date']) ){
            if ( $params['date'] == '5' ){
                $_last = $this->getDateList('last');
                $_time = strtotime($_last.'-01-01');
                $r['raw'] .= " AND regDate < $_time ";
            }elseif ( $params['date'] == '9' ){
                $r['eq']['regDate'] = '0';
            }elseif ( $params['date'] > 2000 ){
                $_start = strtotime($params['date'].'-01-01');
                $_end   = strtotime($params['date'].'-12-31');
                $r['scope']['regDate'] = array($_start, $_end);
            }
        }
        //分类
        if ( !empty($params['class']) && empty($params['group']) ){
            $r['ft']['class'] = $params['class'];
        }
        //群组
        if ( !empty($params['class']) && !empty($params['group']) ){
            $r['ft']['group'] = $params['group'];
        }
        //平台
        if ( !empty($params['platform']) ){
            $r['ft']['platform'] = $params['platform'];
        }
        //组合类型
        if ( !empty($params['type']) ){
            $r['ft']['type'] = $params['type'];
        }
        //商标字数
        if ( !empty($params['length']) ){
            $r['ft']['length'] = $params['length'];
        }
        //特价
        if ( !empty($params['isOffprice']) ){
            $r['raw'] .= ' AND `priceType` = 1 AND `isOffprice` = 1 AND (`salePriceDate` = 0 OR `salePriceDate` > unix_timestamp(now())) ';
        }
        
        //特价标签
        if ( !empty($params['label']) ){
            if($params['label']=='a'){
                $r['raw'] .= " AND offprice!='' ";
            }else{
                $r['ft']['offprice'] = $params['label'];
            }
        }

        //商标号
        if ( !empty($params['number']) ){
            $r['eq']['number'] = $params['number'];
        }

        $r['eq']['status']  = 1;
        $r['eq']['isSale']  = 1;

        if ( empty($col) ){
            $r['col']   = array('id', 'tid', 'number', 'class', 'name', 'group', 'price', 'priceType', 'isOffprice', 'salePrice');
        }else{
            $r['col']   = $col;
        }
        $r['page']      = $page;
        $r['limit']     = $limit;

        $_arrClass = empty($params['class']) ? array() : array_filter(explode(',', $params['class']));
        //判断是否列表筛选只有class
        if ( count($params) <= 2 && count($_arrClass) == 1 ){
            //走另一种排序
            $r['order']     = array('listSort' => 'desc','isTop' => 'desc','id' => 'desc');
        }else{
            //默认排序
            $r['order']     = array('isTop' => 'desc','id' => 'desc');
        }
        $res            = $this->import('sale')->findAll($r);
        $res['rows']    = $this->getListTips($res['rows']);

        return $res;
    }

    /**
     * 获取商标信息相关数据
     * 
     * @author  Xuni
     * @since   2016-03-04
     *
     * @access  public
     * @param   int     $class      国际分类(1-45)
     *
     * @return  array   $list       群组号对应群组中文名称的数组
     */
    public function getTmList($params, $page=1, $limit=30)
    {
        $r['eq'] = array('isShow'=>1);
        //判断类别        
        if ( !empty($params['class']) ){
            $_class = explode(',', $params['class']);
            if ( !empty($params['platform']) ){
                $pItems = C('PLATFORM_ITEMS');
                if ( !empty($pItems[$params['platform']]) )
                foreach ($_class as $k => $v) {
                    if ( !in_array($v, $pItems[$params['platform']]) ) unset($_class[$k]);
                }
                if ( empty($_class) ) return array();
            }
            $r['in']['class_id'] = $_class;
        }elseif ( !empty($params['platform']) ){
            $pItems = C('PLATFORM_ITEMS');
            if ( !empty($pItems[$params['platform']]) ){
                $r['in']['class_id'] = $pItems[$params['platform']];
            }
        }
        //如果有类别条件，则参数类别使用它
        if ( !empty($r['in']['class_id']) ) $params['class'] = implode(',', $r['in']['class_id']);

        if ( !empty($params['name']) ){
            switch ( $params['_name'] ) {
                case '1'://相同
                    $r['eq']['trademark'] = $params['name'];
                    break;
                case '2'://前包含
                    $r['lLike']['trademark'] = $params['name'];
                    break;
                case '9'://近似
                    $_arr = array('keyword'=>$params['name']);
                    if ( !empty($params['class']) ){
                        $_arr['classId'] = $params['class'];
                        unset($params['class'],$r['in']['class_id']);
                        if ( !empty($params['group']) ){
                            $_arr['groupCode'] = $params['group'];
                            unset($params['group']);
                        }
                    }
                    $_res = $this->searchLike($_arr, 1, 1000);
                    if ( empty($_res['rows']) ) return $result;
                    $numberList = array_unique( arrayColumn($_res['rows'], 'id') );
                    if ( empty($numberList) ) return $result;
                    $r['in']['tid'] = $numberList;
                    break;
                default:
                    $r['eq']['trademark'] = $params['name'];
                    break;
            }
        }

        //注册日期
        if ( !empty($params['date']) ){
            if ( $params['date'] == '5' ){
                $_last = $this->getDateList('last');
                $_time = strtotime($_last.'-01-01');
                $r['raw'] .= " AND regDate < $_time ";
            }elseif ( $params['date'] == '9' ){
                $r['eq']['regDate'] = '0';
            }elseif ( $params['date'] > 2000 ){
                $_start = strtotime($params['date'].'-01-01');
                $_end   = strtotime($params['date'].'-12-31');
                $r['scope']['regDate'] = array($_start, $_end);
            }
        }

        //组合类型
        if ( !empty($params['type']) ){
            $r['in']['type'] = explode(',', $params['type']);
        }
        //商标字数
        if ( !empty($params['length']) ){
            $r['in']['nums'] = explode(',', $params['length']);
        }

        //$isInGroup = false;
        if ( !empty($params['group']) ){
            $r['ft']['group'] = $params['group'];
            //$isInGroup = true;
            //$r['page']  = 1;
            //$r['limit'] = 10000;
        }

        $r['index'] = array(($page - 1) * $limit, $limit);
        //$r['page']  = $page;
        $r['limit'] = $limit;

        $r['col']   = array('tid', 'trademark_id as `number`', 'class_id as `class`', 'trademark as `name`', 'group');

        $list   = $this->import('second')->find($r);
        $list   = $this->getListTips($list);
        //处理MySQL不走索引而效率慢的问题
        //if ( $isInGroup && !empty($list['rows']) ){
        //    $list['total']  = count($list['rows']);
        //    $start          = ($page - 1) * $limit;
        //    $items          = $this->makeInGroup($list['rows'], $params['group']);
        //    $list['rows']   = array_splice($items, $start, $limit);
        //}
        //$list['rows']   = $this->getListTips($list['rows']);

        return $list;
    }

    public function getNumberInfo($number)
    {
        if ( empty($number) ) return '3';

        $sale = $this->load('sale')->getSaleInfo($number);
        //debug($sale);
        if ( empty($sale) ){
            $r['eq']    = array('trademark_id'=>$number);
            $r['order'] = array('isShow'=>'desc');
            $r['col']   = array('isShow','tid', 'class_id');
            $info = $this->import('second')->find($r);
            if ( empty($info) || empty($info['tid']) ){
                return '3';
            }elseif ( $info['isShow'] != 1 ){
                return '2';
            }else{
                return array('code'=>1,'tid'=>$info['tid'], 'class'=>$info['class_id']);
            }
        }elseif ($sale['status'] == '1'){
            $_class = current( explode(',', $sale['class']) );
            return array('code'=>1,'tid'=>$sale['tid'],'class'=>$_class);
        }else{
            return '2';
        }
    }

    /**
     * 处理带有IN和match条件的数据
     *
     * 把带有IN条件的数据进行PHP字符串匹配处理，完成MySQL不走索引而效率慢的问题。 
     * @author  Xuni
     * @since   2016-03-04
     *
     * @access  public
     * @param   array       $data       原始数据
     * @param   array       $group      群组
     *
     * @return  array       $rows       原始数据匹配$group后的数据
     */
    public function makeInGroup($data, $group)
    {
        $rows = array();
        foreach ($data as $k => $v) {
            $_group = array_filter( explode(',', $group) );
            if ( empty($_group) ) continue;
            foreach ($_group as $g) {
                if ( strpos($v['group'], $g) !== false ){
                    $rows[] = $v;
                    break;
                }
            }
        }
        return $rows;
    }

    /**
     * 近似查询调用
     *
     * @author  Xuni
     * @since   2016-03-04
     *
     * @return  array
     */
    public function searchLike($params, $page=1, $number=1000)
    {
        if ( empty($params) ) return array();

        $params['page']     = $page;
        $params['num']      = $number;

        $res = $this->importBi('trademark')->search($params);
        return $res;
    }

    public function getChannelTop($topList)
    {
        if ( empty($topList) ) return array();        
    }

    /**
     * 处理列表数据
     *
     * @author  Xuni
     * @since   2016-03-04
     *
     * @return  array
     */
    public function getListTips($data)
    {
        if ( empty($data) ) return array();
        if ( !is_array(current($data)) ){
            $_tmp = array($data);
        }else{
            $_tmp = $data;
        }
        if ( !empty($this->loginId) ){
            $numbers    = arrayColumn($_tmp, 'number');
            $lookList   = $this->load('usercenter')->existLook($numbers);
        }        
        foreach ($_tmp as $k => $v) {
            if ( isset($lookList[$v['number']]) ){
                $v['isLook'] = $lookList[$v['number']];                
            }else{
                $v['isLook'] = 2;
            }
            $data[$k] = $this->getTips($v);
        }
        return $data;
    }

    /**
     * 处理列表数据
     *
     * @author  Xuni
     * @since   2016-03-04
     *
     * @return  array
     */
    public function getTips($data)
    {
        if ( empty($data['tid']) ) return $data;

        if ( $data['id'] ){
            //$data['imgUrl'] = $this->load('internal')->getViewImg($data['id']);
            $rst = $this->load('internal')->getViewImgAndAlt($data['id']);//得到图片和描述
            $data = array_merge($data,$rst);
        }else{
            $data['imgUrl'] = $this->load('trademark')->getImg($data['number']);
            $data['alt'] = '';
        }
        $_class = current( explode(',', $data['class']) );
        $data['viewUrl'] = '/d-'.$data['tid'].'-'.$_class.'.html';
        $data['safeUrl'] = CHECK_URL.'?n='.$data['number'].'&c='.$data['class'];

        //显示商品数据
        $_info = $this->load('trademark')->getInfo($data['tid'], array('goods'));
        $data['goods']  = empty($_info['goods']) ? '' : $_info['goods'];

        if ( $data['isLook'] == '2' && !empty($_COOKIE['uc_ukey']) ){
            $lookList = $this->load('usercenter')->existLook(array($data['number']));
            $data['isLook'] = isset($lookList[$data['number']]) ? $lookList[$data['number']] : 0;
        }elseif( !isset($data['isLook']) ){
            $data['isLook'] = '2';
        }

        return $data;
    }

    /**
     * 获取商标分类与群组相关标题
     *
     * 获取商标分类与群组相关标题
     * 
     * @author  Xuni
     * @since   2016-03-08
     *
     * @return  array
     */
    public function getClassGroup($class=0, $group=1)
    {
        if ( $class == 0 && $group != 1 ){
            $r['eq'] = array('parent'=>0);
        }elseif ( $class != 0 && $group == 1 ){
            //$r['eq'] = array('parent'=>$class);
            $r['raw'] = " (`parent` = '$class' OR `number` = '$class') ";
        }elseif ( $class != 0 ){
            $r['eq'] = array('number'=>$class);
        }
        //$r['order'] = array('parent'=>'asc','number'=>'asc');
        $r['limit'] = 1000;

        $_class = $_group = array();
        $res    = $this->import('class')->find($r);
        if ( empty($res) ) return array();

        foreach ($res as $k => $v) {
            if ( $v['parent'] == '0' ){
                $_class[$v['number']] = empty($v['title']) ? $v['name'] : $v['title'];
            }elseif ( $v['parent'] != 0 ){
                $_group[$v['parent']][$v['number']] = empty($v['title']) ? $v['name'] : $v['title'];
            }
        }
        ksort($_class);
        return array($_class, $_group);
    }
        
    /**
     * 生成年份条件
     * 
     * @author  Xuni
     * @since   2016-03-08
     *
     * @return  string
     */
    public function getDateList($type='')
    {
        $year = date('Y');//去年
        $_arr = array();
        for ($i=0; $i < 6; $i++) {
            $_year = $year - $i;
            $_arr[$_year] = $_year.'年';
        }
        if ( $type == 'last' ) return $_year; 
        $_arr['5'] = '5年以前';
        $_arr['9'] = '申请中';
        return $_arr;
    }

    //获取频道页设置
    public function getChannel($index)
    {
        if ( empty($index) ) return array();

        $r['eq'] = array('index'=>$index);
        $channel = $this->import('channel')->find($r);
        
        if ( empty($channel) ) return array();

        if ( $channel['isBanner'] != '1' ){
            unset($channel['banner']);
        }
        if ( $channel['isAd'] == '1' ){
            $channel['adList'] = $this->getChannelItems($channel['id'], 1);
        }
        if($index== 'offprice'){
            $channel['goodsList'] = $this->getChannelItems($channel['id'], 3, 1);
            $channel['lowList'] = $this->getChannelItems($channel['id'], 4, 1);
        }
        
        return $channel;
    }
    
    /**
     * 获取频道页设置Items
     * @param type $cid  频道ＩＤ
     * @param type $type 分类
     * @param type $saleinfo　是否获取详情数据
     * @return type
     * 
     */
    public function getChannelItems($cid, $type=1, $saleinfo="")
    {   
        if ( empty($cid) || empty($cid) ) return array();
        $r['eq'] = array(
            'channelId' => $cid,
            'type'      => $type,
            );
        $r['limit'] = 1000;
        $r['order'] = array('sort'=>'asc');
        $items = $this->import('channelItems')->find($r);
        if(!empty($saleinfo)){
            foreach($items as $k=>$v){
                $sale = $this->load('sale')->getSaleInfo($v['pic'],array('id','tid','salePrice','class','price','priceType','isOffprice','hits'));
                $_class = current( explode(',', $sale['class']) );
                $items[$k]['imgUrl']    = $this->load('internal')->getViewImg($sale['id']);
                $items[$k]['viewUrl']   = '/d-'.$sale['tid'].'-'.$_class.'.html';
                $items[$k]['salePrice'] = $sale['salePrice'];
                $items[$k]['hits']      = $sale['hits'];
                $items[$k]['info']      = $sale;
            }
            
        }
        return $items;
    }

    /**
     * 获取商标的额外信息
     * @param $id
     * @return string
     */
    public function getExtSaleInfo($id,$number){

        $r['eq'] = array('saleId'=>$id);
        $r['col'] = array('value','number','alt1');
        $data = $this->import('tminfo')->find($r);
        //得到图片
        //得到商标价值
        if($id<=0){
            $data['imgUrl'] = $this->load('trademark')->getImg($number);
        }else{
            $data['imgUrl'] = $this->load('internal')->getViewImg($id);
        }
        //查看商标是否被收藏
        $data['isLook'] = 0;
        $lookList   = $this->load('usercenter')->existLook(array($data['number']));
        if($lookList){
            $data['isLook'] = 1;
        }
        return $data;
    }

    /**
     * 得到分类描述
     * @param $class
     * @return mixed
     */
    public function getClassInfo($class){
        $r['eq']['id'] = $class;
        $r['col'] = array('label','title','name');
        $res = $this->import('class')->find($r);
        if($res){
            return $res;
        }
        return '';
    }

    /**
     * 根据群组号得到群组名,'3001-我是群组名'
     * @param $number
     * @return array
     */
    public function handleGroup($number){
        $number = explode(',',$number);
        $data = array();
        foreach($number as $item){
            $r['eq']['number'] = $item;
            $r['col'] = array('name');
            $rst = $this->import('class')->find($r);
            if($rst){
                $data[] = $item.'-'.$rst['name'];
            }else{
                $dadt[] = $item;
            }
        }
        return $data;
    }
    
    /**
     * 根据群组号得到群组名,'我是群组名'
     * @param $number
     * @return string
     */
    public function getGroupName($number){
        $number = explode(',',$number);
        $data = "";
        foreach($number as $item){
            $r['eq']['number'] = $item;
            $r['col'] = array('name');
            $rst = $this->import('class')->find($r);
            if($rst){
                $data[] = $rst['name'];
            }
        }
        return implode(";", $data);
    }
}
?>