<?
/**
 * 用户中心组件
 *
 * 与用户中心交互接口
 * 
 * @package	Module
 * @author	Xuni
 * @since	2016-03-14
 */
class UserCenterModule extends AppModule
{
    //判断用户是否收藏商标
    public function existLook($numbers,$type=1)
    {
        //判断是否登录
        if ( empty($this->loginId) ) return array();
        if ( empty($numbers) ) return array();

        $res = $this->importBi('usercenter')->existLook($numbers, $this->loginId,$type);
        return $res;
    }

    //获取用户信息
	public function getUserInfo()
    {
        //判断是否登录
        if ( empty($this->loginId) ) return array();

        $res = $this->importBi('usercenter')->getUserInfo($this->loginId);
        return intval($res);
    }
}
?>