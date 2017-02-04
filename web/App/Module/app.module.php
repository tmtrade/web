<?
/**
 * 应用公用业务组件
 *
 * 应用相关的业务方法
 * 
 * @package	Model
 * @author	void
 * @since	2015-06-12
 */
abstract class AppModule extends Module
{
	public function __construct()
	{
		//自定义业务逻辑
		$this->setLoginUser();
	}
	
	/**
	 * 获取业务对象(系统对接时使用)
	 * @author	void
	 * @since	2015-03-26
	 *
	 * @access	public
	 * @param	string	$name	业务代理类名
	 * @return	object  返回业务对象
	 */
	public function importBi($name)
	{
		static $config = array();
		if ( empty($config) ) {
			require(ConfigDir.'/Extension/service.config.php');
		}
		
		static $objList = array();
		if ( isset($objList[$name]) && $objList[$name] ) {
			return $objList[$name];
		}

		$file = BiDir.'/'.strtolower($name).'.bi.php';
		require_once($file);
		$className      = $name.'Bi';
		$bi             = new $className();
		$bi->url        = $config[$bi->apiId]['url'];
		$bi->token      = $config[$bi->apiId]['token'];
		$objList[$name] = $bi;
		
		return $bi;
	}

	/**
	 * 设置用户信息数据
	 *
	 * @author	Xuni
	 * @since	2016-03-14
	 *
	 * @access	public
	 * @return	void
	 */
	protected final function setLoginUser()
	{
		if ( empty($_COOKIE['uc_ukey']) ){
			$this->loginId 		= '';
			$this->nickname 	= '';
			$this->userMobile 	= '';
			$this->isLogin 		= false;
			return false;
		}else{
			$this->loginId 		= $_COOKIE['uc_ukey'];
			$this->nickname 	= $_COOKIE['uc_nickname'];
			$this->userMobile 	= $_COOKIE['uc_mobile'];
			$this->isLogin 		= true;
		}
		return true;
	}
}
?>