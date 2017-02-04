<?
/**
*
* 专利商品创建，修改，删除等
*
* @package	Module
* @author	Far
* @since	2016-04-27
*/
class PatentModule extends AppModule
{
    public $models = array(
        'patent'                => 'patent',
        'contact'               => 'patentContact',
        'tminfo'                => 'patentInfo',
        'userPatentHistory'     => 'userPatentHistory',
        'ptlist'                => 'patentList',
    );


   //获取商品number和联系电话查询改商标下的联系人信息
    public function getSaleContactByPhone($number, $phone)
    {
        $code   = (strpos($number, '.') !== false) ? strstr($number, '.', true) : $number;
        $r['eq'] = array(
                'code' => $code,
                'phone' => $phone,
                );
        $r['limit'] = 1;
        return $this->import('contact')->find($r);
    }

    //创建默认的商品信息
    public function addDefault($number, $info, $contact="")
    {
        if ( empty($number) ) return false;
        if ( $this->existSale($number) ) return false;
        $number = strtolower($number);//专利编号 带.
        $code   = $info['id'];
        $_class = array();
        $_group = array();
        foreach ($info['ipcs'] as $ky => $val) {
            array_push($_class, current($val['ancestors']));
            array_push($_group, $val['id']);
            $_group = array_merge($_group, (array)$val['ancestors']);
        }
        $title  = $info['title']['original'];//专利标题
        $type   = 0;//专利类型
        if ( strpos($info['typeName'], '发明') !== false ){
            $type = 1;
        }elseif ( strpos($info['typeName'], '新型') !== false || strpos($info['typeName'], '实用') !== false ){
            $type = 2;
        }elseif ( strpos($info['typeName'], '外观') !== false ){
            $type = 3;
        }
        
        $group = implode(',', array_unique(array_filter($_group)));//专利所有群组
        $_class = array_unique(array_filter($_class));
           if ( $type == 3 ){
               $class  = implode(',', $_class);
           }else{
               if ( empty($_class) ){
                   $_class = array();
                   foreach (explode(',', $group) as $ky => $val) {
                       $_class[] = strtolower( substr($val,0,1) );
                   }
               }
               $_class = array_map('strtolower', $_class);
               $class  = empty($_class) ? '' : implode(',', array_map('ord', $_class));
           }
            
        
        $applyDate  = (int)strtotime($info['application_date']);//申请日
        $publicDate = (int)strtotime($info['earliest_publication_date']);//最早公开日
        $viewPhone  = $this->load('phone')->getRandPhone();
        $_memo      = '后台手动创建专利';
        $patent = array(
            'number'        => $number,
            'code'          => $code,
            'class'         => $class,
            'group'         => $group,
            'title'         => $title,
            'type'          => $type,
            'applyDate'     => $applyDate,
            'publicDate'    => $publicDate,
            'date'          => time(),
            'viewPhone'     => $viewPhone,
            'memo'          => $_memo,
            );

        $ptinfo = array(
            'number'    => $number,
            'code'      => $code,
            'intro'     => '',
            );
        $this->begin('patent');//开始事务
        $patentId  = $this->import('patent')->create($patent);
        $contactId = true;
        if ( $patentId > 0 ){
            $ptinfo['patentId'] = $patentId;
            $flag1      = $this->import('tminfo')->create($ptinfo);
            if(!empty($contact)){
                $contactId  = $this->addContact($contact, $patentId);//添加联系人
            }

            if($flag1 && $contactId){
                $this->commit('patent');
                return $patentId;
            }
        }
        $this->rollBack('patent');
        return false;
        
    }

    //添加出售联系人信息（可多个）
    public function addContact($data, $patentId)
    {
        //判断是否二维数组
        if ( is_array(current($data)) ){
            foreach ($data as $k => $v) {
                $res = $this->addContact($v, $patentId);
                if ( !$res ) return false;
            }
            return $res;
        }        
        $data['patentId'] = $patentId;
        return $this->import('contact')->create($data);
    }


    //判断是否出售基础信息是否存在
    public function existSale($number)
    {
        if ( empty($number) ) return false;
        $code   = (strpos($number, '.') !== false) ? strstr($number, '.', true) : $number;
        $r['eq']    = array('code'=>$code);
        $r['col']   = array('id');
        $res = $this->import('patent')->find($r);
        if ( empty($res) ) return false;
        return $res['id'];
    }


     //判断联系人信息是否存在
    public function existContact($number, $userId, $phone)
    {
        if ( empty($number) ) return false;
        $code   = (strpos($number, '.') !== false) ? strstr($number, '.', true) : $number;
        $r['eq'] = array('code'=>$code);
        if ( $userId > 0 ){
            $r['eq']['uid'] = $userId;
        }
        if ( !empty($phone) ){
            $r['eq']['phone'] = $phone;
        }
        $count = $this->import('contact')->setCache(false)->count($r);
        return ($count > 0) ? true : false;
    }
    //获取单个专利数据
    public function getPatentInfoByWanxiang($number,$addlist=1)
    {
        if ( empty($number) ) return array();

        $eq = (strpos($number, '.') !== false) ? array('number'=>$number) : array('code'=>$number);

        $r['eq']    = $eq;
        $r['limit'] = 1;
        $info = $this->import('ptlist')->find($r);
        if ( !empty($info['data']) ){
            $data = unserialize($info['data']);
            return $data;
        }
        
        $code   = (strpos($number, '.') !== false) ? strstr($number, '.', true) : $number;
        $url    = 'http://wanxiang.chaofan.wang/detail.php?id=%s&t=json';

        $url    = sprintf($url, $code);
        $data   = $this->requests($url);
        if ( !empty($data) && !empty($data['id']) && $addlist==1){            
            $_data = array(
                'code'      => $code,
                'number'    => $number,
                'data'      => serialize($data),
                );
            $this->import('ptlist')->create($_data);
        } 
        return $data;
    }
    
    //请求连接
    public function requests( $url, $type='GET', $params=array() )
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
        $res =  json_decode(trim($result,chr(239).chr(187).chr(191)),true);
        return $res;
    }
}
?>