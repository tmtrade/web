<?php
/**
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/3/8 0008
 * Time: 上午 11:16
 */
//define('CRM_API_HOST', "http://crm.chofn.com:88/");
/**
 * 定义接口用户名
 * @var string
 */
define('CRM_API_USER', 'yizhchan');
/**
 * 定义接口密钥
 * @var string
 */
define('CRM_API_KEY', '216Iv321Sz247FDasasafff');
class tradeInfo{
    public static function request($param)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        //curl_setopt($ch, CURLOPT_URL, CRM_API_HOST.'Api/yizhchan.php');
        curl_setopt($ch, CURLOPT_URL, CRM_URL.'Api/yizhchan.php');
        curl_setopt(
            $ch, CURLOPT_POSTFIELDS,
            array_merge(
                array(
                    'api_user' => CRM_API_USER,
                    'api_key' => md5(CRM_API_KEY.$param['id'])
                ),
                $param
            )
        );
        $result = curl_exec($ch);
        if($result === false) {
            $result = curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }
    /**
     * 获取网络信息
     * @since  	2016-01-11
     * @author 	haydn
     * @param	int 	$cid	客户id
     * @return 	json 	$data 	数据包
     */
    public static function getNetwork($query,$pageIndex,$pageSize = 5)
    {
        $param = array(
            'api_type'   => 'getNetwork',
            'id'         => 100001,
            'map'		 => serialize($query),
            'pageSize'   => $pageSize,
            'pageIndex'	 => $pageIndex,
        );
        return self::request($param);
    }
    /**
     * 用网络id获取信息
     * @since  	2016-01-11
     * @author 	haydn
     * @param	int 	$aid	网络信息id
     * @return 	json 	$data 	数据包
     */
    public static function getBrandInfo($aid)
    {
        $param = array(
            'api_type'   => 'getBrandInfo',
            'id'         => 100001,
            'aid'		 => $aid,
        );
        return self::request($param);
    }
    /**
     * 获取已经成交和已经立案的商标
     * @author 	haydn
     * @since	2016-02-27
     * @param	array		$query		查询数组包  例如：$query['aid'] = array(1879941,1879884,1880011);
     * @param	int 		$pageIndex	第几页
     * @param	int 		$pageSize	每页显示
     * @return	json
     */
    public static function getClinchBrand($query = array(),$pageIndex = 1,$pageSize = 5)
    {
        $param 	  = array(
            'api_type'   => 'getClinchBrand',
            'id'         => 100001,
            'map'		 => serialize($query),
            'pageSize'   => $pageSize,
            'pageIndex'	 => $pageIndex,
        );
        return self::request($param);
    }
}