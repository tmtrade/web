<?
/**
 * 用户中心接口
 * 
 * @access	public
 * @package bi
 * @author	Xuni
 * @since	2015-11-05
 */
class UserCenterBi extends Bi
{
	/**
	 * 对外接口域名编号(默认为超凡网、其他待定)
	 */
	public $apiId = 4;


   /**
	* uc_ukey	用户的cookies
	* trademark 商标号，格式为  array('16297660','14528025','10000')  或者单个商标号：16297660
	* source	来源：1:交易；2：竞手
	* 返回结果为
	*	Array ( 
	*	[16297660] => 1 
	*	[14528025] => 1 
	*	[10000] => 0 
	*	) 
	*/
    public function existLook($numbers, $ukey,$type=1)
    {
    	if ( empty($numbers) || !is_array($numbers) ) return array();

    	if ( empty($ukey) ) return array();

		$params = array(
			'uc_ukey'	=> $ukey,
			'trademark'	=> $numbers,
			'source'	=> '1',
			'type'	=> $type,
			'webname'	=> 'usercenter',//不变
			'key'		=> "trademark1104martinewodd",//不变
		);
		return $this->request("systemapi/getCollectTrademark/", $params);
    }

    /**
     * 获取用户中心用户信息
     * 
	 * @access	public
	 * @author	Xuni
	 * @since	2016-03-21
	 */
	public function getUserInfo($ukey)
    {
		$params = array(
			'uc_ukey'	=> $ukey,
			'webname'	=> 'usercenter',//不变
			'key'		=> "trademark1104martinewodd",//不变
		);
		
		return $this->request("systemapi/getUserId/", $params);
    }


}
?>