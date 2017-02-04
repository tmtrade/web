<?
class ProposerBi extends Bi
{
	/**
	 * 对外接口域名编号(默认为超凡网、其他待定)
	 */
	public $apiId = 1;


	/**
	 * 获取账户信息
	 * 
	 * @access	public
	 * @param	string	$account	登录账户
	 * @param	string  $prposer	申请人信息
	 * @return	array   返回账户所有信息(空为账户不存在)
	 */
	public function VerifyEmail($account, $proposer)
	{
		$param = array(
			'email'     => $account,
			'proposer'  => $proposer,
			);
			
		return $this->request("openapi/isProposerByEmail", $param );
	}
}
?>