<?php
/**
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/5/6 0006
 * Time: 下午 13:47
 */
class PdetailModule extends AppModule{

    static $token = '';

    public $models = array(
        'patent'                => 'patent',
        'contact'               => 'patentContact',
        'tminfo'                => 'patentInfo',
        'userPatentHistory'     => 'userPatentHistory',
        'ptlist'                => 'patentList',
    );

    /**
     * 得到销售中的专利信息
     * @param $number
     * @param bool $flag 是否字段缓存 默认缓存
     * @return array
     */
    public function getPatentInfo($number,$flag=true){
        $r['eq'] = array(
            'number' => $number,
        );
        $info = $this->import('patent')->setCache($flag)->find($r);
        if ( empty($info) ) return array();
        //获得申请人.描述.默认图片
        $info += $this->getOrginalInfo($number);
        return $info;
    }

    /**
     * 得到商标的包装信息
     * @param $patentId
     * @return array
     */
    public function getSaleTminfo($patentId)
    {
        $r['eq'] = array(
            'patentId' => $patentId,
        );
        $data = $this->import('tminfo')->find($r);
        if($data){
            $data['intro'] = preg_replace('/src="/','src="'.TRADE_URL,$data['intro']);//添加上域名
        }
        return $data;
    }

    /**
     * 通过专利号获得原始数据
     * @param $number
     * @param $type 1为获得申请人.描述.原始图片 2为获取原始数据
     * @return array|mixed
     */
    public function getOrginalInfo($number,$type=1){
        if ( empty($number) ) return array();
        //从list表中获取数据
        $eq = (strpos($number, '.') !== false) ? array('number'=>$number) : array('code'=>$number);
        $r['eq']    = $eq;
        $r['limit'] = 1;
        $info = $this->import('ptlist')->find($r);
        if ( !empty($info['data']) ){
            $data = unserialize($info['data']);
        }else{
            //从万象云获得数据并保存在list表中
            $code   = (strpos($number, '.') !== false) ? strstr($number, '.', true) : $number;
            $url    = 'http://wanxiang.chaofan.wang/detail.php?id=%s&t=json';
            $url    = sprintf($url, $code);
            $data   = $this->requests($url);
            //保存到list表中
            if ( !empty($data) && !empty($data['id']) ){
                $_data = array(
                    'code'      => $code,
                    'number'    => $number,
                    'data'      => serialize($data),
                );
                $this->import('ptlist')->create($_data);
            }
        }
        //结果为有效数据
        if(!empty($data) && !empty($data['id'])){
            $data = $this->handleOrginal($data,$type);
        }else{
            return false;
        }
        //处理结果
        return $data;
    }

    /**
     * 得到类变量--图片token(所有图片可以共用token)
     * @return mixed|string
     */
    private function getToken(){
        //初始化类变量
        if(self::$token==''){
            self::$token = $this->requests('http://wanxiang.chaofan.wang/?t=accessToken','GET',array(),false);
        }
        return self::$token;
    }
    /**
     * 处理原始的专利数据
     * @param $data
     * @param $type 1为获得申请人.描述.原始图片 2为获取原始数据
     * @return array
     */
    function handleOrginal($data,$type){
        $rst = array();
        //专利标题
        $rst['title']  = $data['title']['original'] ? $data['title']['original'] : $data['title']['zh-cn'];
        if(!$rst['title']){
            $rst['title'] = $data['title']['en'];
        }
        //仅仅获得申请人和描述
        if($type==1){
            //申请人
            $rst['proName'] = $data['applicants'][0]['name']['original'];
            //专利介绍
            $rst['intro'] = empty($data['abstract']['original'])?$data['abstract']['en']:$data['abstract']['original'];
            //图片
            if(empty($data['figures'][0])){
                $rst['imgUrl'] = '';
            }else{
                $token = $this->getToken();
                $rst['imgUrl'] = 'https://user.wanxiangyun.net/client/figure/'.$data['figures'][0].'?access_token='.$token;
            }
            return $rst;
        }
        //专利类型
        $rst['type']   = 0;
        if ( strpos($data['typeName'], '发明') !== false ){
            $rst['type'] = 1;
        }elseif ( strpos($data['typeName'], '新型') !== false || strpos($data['typeName'], '实用') !== false ){
            $rst['type'] = 2;
        }elseif ( strpos($data['typeName'], '外观') !== false ){
            $rst['type'] = 3;
        }
        //处理分类
        $_class = array();
        foreach ($data['ipcs'] as $ky => $val) {
            array_push($_class, current($val['ancestors']));
        }
        $_class = array_unique(array_filter($_class));
        if ( $rst['type'] == 3 ){
            $rst['class']  = implode(',', $_class);
        }else{
            if ( empty($_class) ){
                //得到群组信息
                $_group = array();
                foreach ($data['ipcs'] as $ky => $val) {
                    array_push($_group, $val['id']);
                    $_group = array_merge($_group, (array)$val['ancestors']);
                }
                $group = implode(',', array_unique(array_filter($_group)));
                //由群组信息得到分类信息
                $_class = array();
                foreach (explode(',', $group) as $ky => $val) {
                    $_class[] = strtolower( substr($val,0,1) );
                }
            }
            $_class = array_map('strtolower', $_class);
            $rst['class']  = empty($_class) ? '' : implode(',', array_map('ord', $_class));
        }
        //申请日
        $rst['applyDate']  = (int)strtotime($data['application_date']);
        //最早公开日
        $rst['publicDate'] = (int)strtotime($data['earliest_publication_date']);
        //申请人
        $rst['proName'] = $data['applicants'][0]['name']['original'];
        //专利介绍
        $rst['intro'] = empty($data['abstract']['original'])?$data['abstract']['en']:$data['abstract']['original'];
        //图片
        if(empty($data['figures'][0])){
            $rst['imgUrl'] = '';
        }else{
            $token = $this->getToken();
            $rst['imgUrl'] = 'https://user.wanxiangyun.net/client/figure/'.$data['figures'][0].'?access_token='.$token;
        }
        return $rst;
    }

    /**
     * 得到图片地址
     * @param $number
     * @return string
     */
    public function getPTImg($number){
        //查看是否有美化图
        $r['eq'] = array(
            'number' => $number,
        );
        $r['col'] = array('embellish');
        $rst = $this->import('tminfo')->find($r);
        if(!empty($rst['embellish'])){
            return TRADE_URL.$rst['embellish'];
        }
        //获取原始数据
        $rst = $this->getOrginalInfo($number);
        if(empty($rst['imgUrl'])){
            //默认图片
            return '/Static/images/img1.png';
        }else{
            return $rst['imgUrl'];
        }
    }
    /**
     * 得到随机的推荐专利
     * @param int $number
     * @return mixed
     */
    public function getRandPT($number=10){
        //得到随机推荐的指定数量的专利数据
        $r['eq'] = array('status'=>1);
        $total = $this->import('patent')->count($r);
        $index = rand(1,$total-3*$number);
        $r['index'] = array($index,$number);
        $r['col'] = array('title','number');
        $data = $this->import('patent')->find($r);
        //处理结果
        if($data){
            foreach($data as $k=>$item){
                $data[$k]['url'] = '/pt-'.$item['number'].'.html';
                $data[$k]['thumb_title'] = mbSub($item['title'],0,8);
            }
            return $data;
        }else{
            return array();
        }
    }

    /**
     * curl获得数据
     * @param $url
     * @param string $type
     * @param array $params
     * @param boolean $flag 默认反序列化得到数组, false时不做处理
     * @return mixed
     */
    private function requests( $url, $type='GET', $params=array(),$flag=true )
    {
        $_type = ($type == 'POST') ? 'POST' : 'GET';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_type);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt(
            $ch, CURLOPT_POSTFIELDS, $params
        );
        $result = curl_exec($ch);
        if($result === false) {
            $result = curl_error($ch);
        }
        curl_close($ch);
        if($flag){
            $result =  json_decode(trim($result,chr(239).chr(187).chr(191)),true);
        }
        return $result;
    }
}