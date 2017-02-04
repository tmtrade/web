<?
/**
* 商标相关信息
*
* 查询商标基础信息、图片、状态等
*
* @package	Module
* @author	Xuni
* @since	2015-10-22
*/
class CheckcountModule extends AppModule
{

    public $models = array(
        'checkcount'		=> 'checkcount',
    );
    /**
    * 点击数加1
    * @author    haydn
    * @since    2015-12-08
    * @access    public
    * @return    bool
    */
    public function click($tradid)
    {
        $count = $this->import('checkcount')->getCount($tradid);
        if( $count > 0 ){
            $data['hits']   = $count + 1;
            $r['eq']['number']    = $tradid;
            $this->import('checkcount')->update($data,$r);
        }else{
            $data['hits']   = 1;
            $data['number'] = $tradid;
            $this->import('checkcount')->clickAdd($data);
        }
    }
    /**
    * 获取点击数总和
    * @author   haydn
    * @since    2015-12-08
    * @access   public
    * @return   int         $count 点击总和
    */
    public function getAllCount()
    {
        $r['col']   = array('sum(`hits`) as count');
        $data       = $this->import('checkcount')->find($r);
        return $data['count'];
    }
}
?>